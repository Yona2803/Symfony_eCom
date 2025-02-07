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

async function MyCart_Products() {
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
            let productHTML = `<div class="Product" id="Product${product.id}">
                                      <div>
                                      <svg class="DeleteBtn" id="DeleteItemId${
                                        product.id
                                      }" onclick="deleteCartItem(${
              product.id
            })"  width="18" height="18" viewBox="0 0 18 18" fill="none">
<circle cx="9" cy="9" r="9" fill="#DB4444"/>
<path d="M6 12L9 9M12 6L8.99943 9M8.99943 9L6 6M9 9L12 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
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
const refreshPage = async () => {
  const container = document.querySelector(".Cart_Products");
  if (
    !container ||
    container.children.length === 0 ||
    (container.children.length === 1 && container.querySelector("p"))
  ) {
    document.getElementById("HT").innerText = "";
    document.getElementById("TTC").innerText = "";
    document.getElementById("ShippingStatus").innerText = "";
    return;
  }

  calculate_All();
  await sendLocalStorageData();
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

async function deleteCartItem(itemId) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const existingItem = cart.find((item) => item.id === itemId);

  if (!existingItem) {
    console.log("Item not found");
    return;
  }

  // Remove the item with the specified ID
  cart = cart.filter((item) => item.id !== itemId);
  localStorage.setItem("cart", JSON.stringify(cart));
  // Remove the item with the specified ID
  document.getElementById("Product" + itemId).remove();
  calculate_All();
  updateCartIcon();
  checkEmptyContainer();

  const url = `/MyCartItems/Delete?item=${itemId}`;
  fetch(url, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        console.log("Item deleted successfully");
      } else {
        console.error("Error:", data.message);
      }
    })
    .catch((error) => console.error("Error:", error));
}

/**
 * Checks if the wishlist container is empty and updates UI accordingly
 */
function checkEmptyContainer() {
  const container = document.querySelector(".Cart_Products");
  if (container && container.children.length === 0) {
    container.innerHTML = "<p>No products found in the cart.</p>";
    document.getElementById("HT").innerText = "";
    document.getElementById("TTC").innerText = "";
    document.getElementById("ShippingStatus").innerText = "";
  }
}
