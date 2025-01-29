// Add to cart (example in JavaScript)
function addToCart(id) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const existingItem = cart.find((item) => item.id === id);

  if (!existingItem) {
  //   existingItem.quantity += 1;
  // } else {
    cart.push({ id: id, quantity: 1 });
  }
  localStorage.setItem("cart", JSON.stringify(cart));

  // call to update the icon page
  updateCartIcon();
}

// Add Route to Local storage
addRoute("/Products", "Products &#x2f; ");









function openPopup(productId) {
  document.getElementById("updatePopup").style.display = "block";
  document.getElementById("productId").value = productId;
}

function closePopup() {
  document.getElementById("updatePopup").style.display = "none";
}

