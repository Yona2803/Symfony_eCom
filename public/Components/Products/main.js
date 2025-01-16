// Add to cart (example in JavaScript)
function addToCart(id) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const existingItem = cart.find((item) => item.id === id);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({ id: id, quantity: 1 });
  }
  localStorage.setItem("cart", JSON.stringify(cart));

  // call to update the icon page
  updateCartIcon();
}

// Add Route to Local storage
// let ProductName = document.getElementById("ProductName").innerHTML;
// let Route_Path = "homePage";
// let Route_Text = "Home / " + ProductName + " / ";
// addRoute("homePage", "Home / " + ProductName + " / ");
