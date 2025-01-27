function RoutePath() {
  let routeInfo = JSON.parse(localStorage.getItem("routeInfo")) || [];
  let Route_Path;
  let Route_Text;

  if (Array.isArray(routeInfo) && routeInfo.length > 0) {
    Route_Path = routeInfo[0].srcPage_Path;
    Route_Text = routeInfo[0].srcPage_Text;
  } else {
    Route_Path = "/";
    Route_Text = "Home /";
  }
  document.getElementById("Route_Path").href = Route_Path;
  document.getElementById("Route_Path").innerHTML = Route_Text;
}
RoutePath();

// **** MyCart_Products ****
function MyCart_Products() {
  let container = document.querySelector(".Cart_Products");
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  let ids = cart.map((item) => item.id);
  let queryParams = encodeURIComponent(JSON.stringify(ids));

  $.ajax({
    url: `/CheckOut/CheckOutItems?items_ids=${queryParams}`,
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
                                                                                    
                                        <span id="itemTotale${
                                          product.id
                                        }"></span>
                                    </div>`;

            container.innerHTML += productHTML;
            calculateTotale_forEach(product.id, product.price);
          });
        });
        calculate_All();
      } else {
        container.innerHTML = "<p>No products found.</p>";
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

function calculateTotale_forEach(id, price) {
  try {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    let item = cart.find((item) => item.id === id);
    let quantity = item ? item.quantity : null;

    // Ensure quantity is a valid number
    if (isNaN(quantity)) {
      console.error(`Invalid quantity for item with ID ${id}`);
      return;
    }

    let total = 0;
    let returnedValue;
    if (quantity > 0) {
      total = price * quantity;

      returnedValue =
        new Intl.NumberFormat("fr-FR", {
          style: "decimal",
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })
          .format(total)
          .replace(",", ".") + " Dh";

      document.getElementById("itemTotale" + id).innerText = returnedValue;
    }
  } catch (error) {
    console.error(`Error calculating total for item ${id}:`, error);
  }
}

// ****  ****
function calculate_All() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  let total = 0;
  let ErrorExist = false;
  cart.forEach((item) => {
    let price = document.getElementById("itemTotale" + item.id).innerText;

    let cleanPrice = parseFloat(price.replace(/\s/g, "").replace(",", "."));
    total += cleanPrice;
  });

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

// **** Finalize Checkout ****
function Finalize_Checkout() {
  console.log("We aren't ready yet,..");
}