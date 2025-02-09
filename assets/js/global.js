function addRoute(Route_Path, Route_Text) {
  let routeInfo = JSON.parse(localStorage.getItem("routeInfo")) || [];

  if (Array.isArray(routeInfo) && routeInfo.length > 0) {
    routeInfo[0] = {
      srcPage_Path: Route_Path,
      srcPage_Text: Route_Text,
    };
  } else {
    routeInfo = [
      {
        srcPage_Path: Route_Path,
        srcPage_Text: Route_Text,
      },
    ];
  }

  localStorage.setItem("routeInfo", JSON.stringify(routeInfo));
}

// Add to cart
async function addToCart_One(id) {
    try {
      let cartData = [];
      const rawCart = localStorage.getItem("cart");
  
      if (rawCart) {
        try {
          cartData = JSON.parse(rawCart);
        } catch (error) {
          console.error("Error parsing cart data:", error);
          alert("There was an issue with the cart data. Please try again.");
          return;
        }
      }
  
      const existingItem = cartData.find((item) => item.id === id);
  
      if (!existingItem) {
        cartData.push({ id: id, quantity: 1 });
      }
  
      // Save the updated cart back to localStorage
      localStorage.setItem("cart", JSON.stringify(cartData));
  
      updateCartIcon();
  
      const data = JSON.stringify({
        cart: cartData,
      });
  
      // Perform the API request
      const response = await fetch("/MyCartItems", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
        body: data,
      });
  
      if (!response.ok) {
        const errorDetails = await response.text();
        console.error(
          `HTTP error! status: ${response.status}, details: ${errorDetails}`
        );
        throw new Error(`HTTP error! status: ${response.status}`);
      }
  
      const responseData = await response.json();
  
      // Check if the cart was updated successfully and log it
      if (responseData.updatedCart) {
        console.log("Cart updated:", responseData.updatedCart);
      }
  
      return responseData;
    } catch (error) {
      console.error("Error: ", error);
      throw error;
    }
  }
  

  // Add wishlist item to localStorage and DB
function toggleWishlist(itemId, ClickedButton) {
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
  updateCartIcon();

  fetch(`/toggleWishlist/${itemId}`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "addToWishlist") {
        console.log(data.message);
      } else {
        console.log(data.message);
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

