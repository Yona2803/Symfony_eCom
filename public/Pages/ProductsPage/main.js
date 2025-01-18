// Add to cart (example in JavaScript)
function addToCart(event, id) {
  event.stopPropagation();
  
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
addRoute("/Products", "Products &#x2f; ");


