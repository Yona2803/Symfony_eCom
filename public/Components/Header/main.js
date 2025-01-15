function updateCartIcon() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  const cartCircle = document.querySelector("#circle");
  const cart_itemQte = document.querySelector("#cart_itemQte");

  if (cart.length > 0) {
    cartCircle.style.display = "block";
    cart_itemQte.textContent = cart.length;
  } else {
    cartCircle.style.display = "none";
  }
}
updateCartIcon();