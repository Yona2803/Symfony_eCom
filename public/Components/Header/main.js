function updateCartIcon() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  const wishList = JSON.parse(localStorage.getItem("wishList")) || [];

  const cartCircle = document.querySelector("#cartCircle");
  const cart_itemQte = document.querySelector("#cart_itemQte");

  const wishlistCircle = document.querySelector("#wishlistCircle");
  const wishlist_itemQte = document.querySelector("#wishlist_itemQte");


  if (cart.length > 0) {
    cartCircle.style.display = "block";
    cart_itemQte.textContent = cart.length;
  } else {
    cartCircle.style.display = "none";
    cart_itemQte.textContent = "";
  }

  if (wishList.length > 0) {
    wishlistCircle.style.display = "block";
    wishlist_itemQte.textContent = wishList.length;
  } else {
    wishlistCircle.style.display = "none";
    wishlist_itemQte.textContent = "";
  }
}
updateCartIcon();

let input = document.getElementById("searchInput");
let BtnSearch = document.querySelector(".search-bar button");

function submitBtn() {
  if (input.value.trim() !== "") {
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

// **** Drop Down Menu ****
let icon = document.querySelector("#MenuOption");
let DropDown_Menu = document.querySelector("#DropDown_Menu");

// ** on hover **
icon.addEventListener('mouseover', function() {
    DropDown_Menu.style.display = 'flex';
});

icon.addEventListener('mouseout', function(event) {
    if (!DropDown_Menu.contains(event.relatedTarget)) {
        DropDown_Menu.style.display = 'none';
    }
});

DropDown_Menu.addEventListener('mouseover', function() {
    DropDown_Menu.style.display = 'flex';
});

DropDown_Menu.addEventListener('mouseout', function(event) {
    if (!icon.contains(event.relatedTarget)) {
        DropDown_Menu.style.display = 'none';
    }
});

// ** on click **
icon.addEventListener('click', function() {
  if (DropDown_Menu.style.display === 'none') {
      DropDown_Menu.style.display = 'flex';
  } else {
      DropDown_Menu.style.display = 'none';
  }
});

DropDown_Menu.addEventListener('click', function() {
  if (DropDown_Menu.style.display === 'flex') {
      DropDown_Menu.style.display = 'none';
  }
});
