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

function submitBtn() {
  let input = document.getElementById("searchInput").value;
  let BtnSearch = document.querySelector(".search-bar button");

  if (input.trim() !== "") {
    BtnSearch.disabled = false;
    BtnSearch.classList.add("Button_True");
    BtnSearch.classList.remove("Button_False");

  } else {
    BtnSearch.disabled = true;
    BtnSearch.classList.remove("Button_True");
    BtnSearch.classList.add("Button_False");
  }
}
submitBtn();
