function deleteItem(itemId) {
  const userConfirmed = window.confirm(
    "Are you sure you want to remove this product from your wishlist?"
  );


  if (userConfirmed) {
    fetch(`/wishlist/delete/${itemId}`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "successRemoving") {
          document.getElementById(`item-${itemId}`).remove();
          alert(data.message);
        } else {
          console.error(data.message);
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  } else {
    console.log("Item deletion canceled.");
  }

}

function toggleWishlist(itemId, ClickedButton) {
  // Add wishlist item to localStorage
  console.log(itemId); console.log(ClickedButton)

  
  let wishList = JSON.parse(localStorage.getItem("wishList")) || [];
  const existingItemIndex = wishList.findIndex((item) => item.id === itemId);

  if (existingItemIndex !== -1) {
    wishList.splice(existingItemIndex, 1);
    ClickedButton.classList.remove("active");
  } else {
    ClickedButton.classList.toggle("active");
    wishList.push({ id: itemId });
  }
  localStorage.setItem("wishList", JSON.stringify(wishList));

  fetch(`/toggleWishlist/${itemId}`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    }
  })

    .then((response) => response.json())  
            .then((data) => {
                if (data.status === "addToWishlist") {
                    alert(data.message); 
                } else {
                    alert(data.message); 
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    
}


// initialize wishList icon state
function initializeIcon() {
  let wishList = JSON.parse(localStorage.getItem("wishList")) || [];

  if (wishList !== undefined || wishList.length !== 0) {
    wishList.forEach((Product) => {
      let productElement = document.querySelector("#WishlistPrd" + Product.id);
      if (productElement) {
        productElement.classList.add("active");
      }
    });
  }
}
initializeIcon();

