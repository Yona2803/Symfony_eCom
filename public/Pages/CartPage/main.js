function RoutePath() {
  let routeInfo = JSON.parse(localStorage.getItem("routeInfo")) || [];
  let Route_Path;
  let Route_Text;

  // Log the entire routeInfo array for debugging
  if (Array.isArray(routeInfo) && routeInfo.length > 0) {
    Route_Path = routeInfo[0].srcPage_Path;
    Route_Text = routeInfo[0].srcPage_Text;
  } else {
    // Add new route info
    Route_Path = "/";
    Route_Text = "Home";
  }
  document.getElementById("Route_Path").href = Route_Path;
  document.getElementById("Route_Path").innerHTML = Route_Text;
}
RoutePath();

function MyCart_Products() {
  let container = document.querySelector(".Cart_Products");
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  // Get Quantity By Id from Local Storage
  function getQuantityById(id) {
    let item = cart.find((item) => item.id === id);
    let Qte = item ? item.quantity : null;
    return Qte;
  }

  // preparing id's to be sent via HTTP request
  let ids = cart.map((item) => item.id);
  let queryParams = encodeURIComponent(JSON.stringify(ids));

  // Sending by Ajax to Back-End => Show response in HTML
  $.ajax({
    url: `/MyCart/ShowProducts?items_ids=${queryParams}`,
    type: "GET",
    success: function (response) {
      if (response && response.length > 0) {
        response.forEach(function (productArray) {
          productArray.forEach(function (product) {
            let productHTML = `<div class="Product" id="${product.id}">
                                      <div>
                                       ${
                                         product.itemImage &&
                                         product.itemImage !==
                                           "data:image/jpg;base64,"
                                           ? `<img src="${product.itemImage}" alt="Product Image: ${product.name}">`
                                           : `<img src="img/No_Img.png" alt="No Image Available for product: ${product.name}">`
                                       }
                                          <p>${product.name}</p>
                                      </div>
                                      <span id="price${product.id}">
  ${new Intl.NumberFormat("fr-FR", {
    style: "decimal",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
    .format(product.price)
    .replace(",", ".")}
</span>
                                      <div>
                                          <input type="number" id="quantity${
                                            product.id
                                          }" value="${getQuantityById(
              product.id
            )}" name="quantity${product.id}" min="1" max="${
              product.stock
            }" onchange="calculate_ById(${product.id},${product.price},${
              product.stock
            })" > 
            
                                      </div>
                                      <span id="itemTotale${product.id}"></span>
                                  </div>`;

            container.innerHTML += productHTML;
            calculate_ById(product.id, product.price, product.stock);
          });
        });
        calculate_All();
      } else {
        container.innerHTML = "<p>No products found in the cart.</p>";
      }
    },
    error: function (xhr, status, error) {
      console.log("Status:", status);
      console.log("Error:", error);
      console.log("Response:", xhr.responseText);
      console.log("Response JSON:", xhr.responseJSON);
    },
  });
}
// check if Cart exist in Local storage
let cart = JSON.parse(localStorage.getItem("cart")) || [];
let cartIds = cart.map((item) => item.id);

if (cartIds.length > 0) {
  MyCart_Products();
} else {
  let container = document.querySelector(".Cart_Products");
  container.innerHTML = "<p>No products found in the cart.</p>";
}

// Update Cart : Refresh Page
const refreshButton = document.querySelector(".refresh");
const refreshPage = () => {
  calculate_All();
  sendLocalStorageData();
};
refreshButton.addEventListener("click", refreshPage);

// calculate totale of each item
function calculate_ById(id, price, MaxStock) {
  try {
    let quantity = parseInt(document.getElementById("quantity" + id).value);

    // Ensure quantity is a valid number
    if (isNaN(quantity)) {
      console.error(`Invalid quantity for item with ID ${id}`);
      return;
    }

    let total = 0;
    if (quantity > 0 && quantity <= MaxStock) {
      total = price * quantity;

      document.getElementById("itemTotale" + id).innerText =
        new Intl.NumberFormat("fr-FR", {
          style: "decimal",
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })
          .format(total)
          .replace(",", ".") + " Dh";

      update_LocalStorage(id, quantity, MaxStock);
    }
  } catch (error) {
    console.error(`Error calculating total for item ${id}:`, error);
  }
}

function update_LocalStorage(id, quantity, MaxStock) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const existingItem = cart.find((item) => item.id === id);

  if (existingItem) {
    if (quantity > 0 && quantity <= MaxStock) {
      existingItem.quantity = quantity;
    }
  } else {
    const newQuantity = quantity > 0 && quantity <= MaxStock ? quantity : 1;
    cart.push({ id: id, quantity: newQuantity });
  }
  localStorage.setItem("cart", JSON.stringify(cart));
}

function calculate_All() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  let total = 0;
  let ErrorExist = false;
  if (cart.length > 0) {
    cart.forEach((item) => {
      let quantity = document.getElementById("quantity" + item.id);

      let price = document.getElementById("price" + item.id).innerText;

      let cleanPrice = parseFloat(price.replace(/\s/g, "").replace(",", "."));

      if (
        parseInt(quantity.value) <= parseInt(quantity.max) &&
        parseInt(quantity.value) > 0
      ) {
        total += cleanPrice * parseInt(quantity.value);
      } else {
        ErrorExist = true;
        return;
      }
    });
  } else {
    ErrorExist = true;
    return;
  }

  //ShippingStatus, HT, TTC
  let TTC = total * 1.2;
  let ShippingStatus;
  let TTC_Shipping;

  if (!ErrorExist) {
    if (TTC >= 250) {
      ShippingStatus = "Free";
      TTC_Shipping = TTC;
    } else if (TTC >= 100) {
      ShippingStatus =
        new Intl.NumberFormat("fr-FR", {
          style: "decimal",
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })
          .format(15)
          .replace(",", ".") + " Dh";
      TTC_Shipping = TTC + 15;
    } else {
      ShippingStatus =
        new Intl.NumberFormat("fr-FR", {
          style: "decimal",
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })
          .format(25)
          .replace(",", ".") + " Dh";
      TTC_Shipping = TTC + 25;
    }
    document.getElementById("HT").innerText =
      new Intl.NumberFormat("fr-FR", {
        style: "decimal",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
        .format(total)
        .replace(",", ".") + " Dh";

    document.getElementById("ShippingStatus").innerText = ShippingStatus;

    document.getElementById("TTC").innerText =
      new Intl.NumberFormat("fr-FR", {
        style: "decimal",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
        .format(TTC_Shipping)
        .replace(",", ".") + " Dh";

  } else {
    alert(
      "Something is wrong : check the Qty of each item, Please fill the inputs with the arrows of input fields"
    );
  }
}
