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

  // Sending by Ajax to Back-End
  $.ajax({
    url: `/MyCart/ShowProducts?items_ids=${queryParams}`,
    type: "GET",
    success: function (response) {
      if (response && response.length > 0) {
        response.forEach(function (productArray) {
          productArray.forEach(function (product) {
            let productHTML = `<div class="Product" id="${product.id}">
                                      <div>
                                          <img src="${
                                            product.itemImage
                                          }" alt="Product Image">
                                          <p>${product.name}</p>
                                      </div>
                                      <span id="price${product.id}">${
              product.price
            } Dh</span>
                                      <div>
                                          <input type="number" id="quantity${
                                            product.id
                                          }" value="${getQuantityById(
              product.id
            )}" name="quantity${product.id}" min="1" max="${
              product.stock
            }" onchange="calculate_ById(${product.id},${
              product.price
            })" onclik="calculate_All()">
                                      </div>
                                      <span id="itemTotale${product.id}"></span>
                                  </div>`;

            container.innerHTML += productHTML;
            calculate_ById(product.id, product.price);
          });
        });
      } else {
        container.innerHTML = "<p>No products found in the cart.</p>";
      }
      calculate_All();
    },
    error: function (xhr, status, error) {
      console.log("Status:", status);
      console.log("Error:", error);
      console.log("Response:", xhr.responseText);
      console.log("Response JSON:", xhr.responseJSON);
    },
  });
}
MyCart_Products();

// Update Cart : Refresh Page
const refreshButton = document.querySelector(".refresh");
const refreshPage = () => {
  calculate_All();
  // MyCart_Products();
};
refreshButton.addEventListener("click", refreshPage);

// calculate totale of each item
function calculate_ById(id, price) {
  try {
    let quantity =
      parseInt(document.getElementById("quantity" + id).value) || 1;
    let total = price * quantity;

    document.getElementById("itemTotale" + id).innerText =
      total.toFixed(2) + " Dh";
    update_LocalStorage(id, quantity);
    // calculate_All();
  } catch (error) {
    console.error(`Error calculating total for item ${productId}:`, error);
  }
}

function update_LocalStorage(id, quantity) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const existingItem = cart.find((item) => item.id === id);

  if (existingItem) {
    existingItem.quantity = quantity;
  } else {
    cart.push({ id: id, quantity: 1 });
  }
  localStorage.setItem("cart", JSON.stringify(cart));
}

function calculate_All() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  let total = 0;
  cart.forEach((item) => {
    let quantity =
      parseInt(document.getElementById("quantity" + item.id).value) || 0;
    let price =
      parseInt(
        document
          .getElementById("price" + item.id)
          .textContent.replace(" Dh", "")
      ) || 0;

    total += price * quantity;
  });

  //ShippingStatus, HT, TTC
  let TTC = total * 1.2;
  let ShippingStatus;
  let TTC_Shipping;

  if (TTC >= 250) {
    ShippingStatus = "Free";
    TTC_Shipping = TTC;
  } else if (TTC >= 100) {
    ShippingStatus = "15 Dh";
    TTC_Shipping = TTC + 15;
  } else {
    ShippingStatus = "25 Dh";
    TTC_Shipping = TTC + 25;
  }

  document.getElementById("HT").innerText = total.toFixed(2) + " Dh";
  document.getElementById("ShippingStatus").innerText = ShippingStatus;
  document.getElementById("TTC").innerText = TTC_Shipping.toFixed(2) + " Dh";
}
