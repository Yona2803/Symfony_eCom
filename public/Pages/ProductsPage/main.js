// Add Route to Local storage
addRoute("/Products", "Products &#x2f; ");










function openPopup(productId) {
  document.getElementById("updatePopup").style.display = "block";
  document.getElementById("productId").value = productId;
}

function closePopup() {
  document.getElementById("updatePopup").style.display = "none";
}


