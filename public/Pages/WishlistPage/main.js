let cart = JSON.parse(localStorage.getItem("cart")) || [];
let wishlist = JSON.parse(localStorage.getItem("wishList")) || [];

// **
// **** Get items from DB
async function MyWishlist_Products() {
  let container = document.querySelector(".Wishlist_Products");

  // preparing ids to be sent via HTTP request
  let ids = wishlist.map((item) => item.id);
  let queryParams = encodeURIComponent(JSON.stringify(ids));

  // Sending by Ajax to Back-End => Show response in HTML
  $.ajax({
    url: `/wishlist/ShowItems?items_ids=${queryParams}`,
    type: "GET",
    success: function (response) {
      if (response && response.length > 0) {
        response.forEach(function (product) {
          let productHTML = `
          <div class="Product" id="Product${product.id}">
            <div class="OptionSection">
              <button type="button" id="DeleteItemId${
                product.id
              }" onclick="deleteWishlistItem(${product.id})">
                  <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M17 3.57143H2.33333L3.66667 19H14.3333L15.6667 3.57143H1M9 7.42857V15.1429M12.3333 7.42857L11.6667 15.1429M5.66667 7.42857L6.33333 15.1429M6.33333 3.57143L7 1H11L11.6667 3.57143" stroke="black" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
              </button>
            </div>
            
            <div class="ProductImage">
              ${
                product.itemImage &&
                product.itemImage !== "data:image/jpg;base64,"
                  ? `<img src="${product.itemImage}" alt="Product Image: ${product.name}">`
                  : `<img src="img/No_Img.png" alt="No Image Available for product: ${product.name}">`
              }
              									${
                                  product.stock > 0
                                    ? `<button type="button" onclick="moveSingleToCart(${product.id})">Move To Cart</button>`
                                    : ``
                                }
                                
  <input type="hidden" id="QteProduct${product.id}" name="QteProduct${
            product.id
          }" value="${product.stock}"> 
  
              </div>
            <h6 id="ProductName${product.id}">${product.name}</h6>
            <div class="ProductInfo">
              <span id="price${product.id}">
              ${new Intl.NumberFormat("fr-FR", {
                style: "decimal",
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })
                .format(product.price)
                .replace(",", ".")} DH</span>
            </div>
          </div>`;
          container.innerHTML += productHTML;
        });
      } else {
        container.innerHTML = "<p>No products found in the Wishlist.</p>";
      }
    },
    error: function (xhr, status, error) {
      console.error("Status:", status, "Error:", error);
      console.error("Response:", xhr.responseText);
      console.error("Response JSON:", xhr.responseJSON);
    },
  });
}

// ** Check if Cart exist in Local storage
if (wishlist.length > 0) {
  MyWishlist_Products();
} else {
  let container = document.querySelector(".Wishlist_Products");
  container.innerHTML = "<p>No products found in the Wishlist.</p>";
}

/**
 * Deletes a single item from the wishlist
 * @param {string|number} itemId
 * @returns {Promise<void>}
 */
async function deleteWishlistItem(itemId) {
  try {
    // Get latest wishlist data
    let wishlist = JSON.parse(localStorage.getItem("wishList")) || [];

    // Validate item exists
    const existingItem = wishlist.find((item) => item.id === itemId);
    if (!existingItem) {
      console.warn(`Item ${itemId} not found in wishlist`);
      return;
    }

    // Update localStorage
    wishlist = wishlist.filter((item) => item.id !== itemId);
    localStorage.setItem("wishList", JSON.stringify(wishlist));

    // Update UI
    const productElement = document.getElementById(`Product${itemId}`);
    if (productElement) {
      productElement.remove();
    }

    updateCartIcon();
    updateCounter();
    checkEmptyContainer();

    // Send delete request to server
    const response = await fetch(`/wishlist/Delete?item=${itemId}`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });

    const data = await response.json();
    if (data.status === "success") {
      console.log(`Item ${itemId} deleted successfully`);
    } else {
      console.error("Failed to delete item from server");
    }
  } catch (error) {
    console.error("Error deleting wishlist item:", error);
    throw error;
  }
}

/**
 * Updates the wishlist counter in the UI
 */
function updateCounter() {
  const counterElement = document.querySelector("#Counter");
  if (counterElement) {
    counterElement.innerHTML = `&#40;${wishlist.length}&#41;`;
  }
}
updateCounter();

/**
 * Moves a single item from wishlist to cart
 * @param {string|number} id - The ID of the item to move
 */
function moveSingleToCart(id) {
  try {
    if (!cart.some((cartItem) => cartItem.id === id)) {
      cart.push({
        id: id,
        quantity: 1,
      });
    }

    // Remove from wishlist
    wishlist = wishlist.filter((wishlistItem) => wishlistItem.id !== id);

    deleteWishlistItem(id);
  } catch (error) {
    console.error("Error moving item to cart:", error);
    throw error;
  }
}

/**
 * Moves all items from wishlist to cart
 */
function moveAllToCart() {
  try {
    // Create new cart, wishlist with existing items
    let updatedCart = [...cart];
    let updatedWishlist = [...wishlist];

    // Add all wishlist items to cart
    updatedWishlist.forEach((wishlistItem) => {
      let CheckItemStock = parseInt(
        document.getElementById(`QteProduct${wishlistItem.id}`).value
      );

      let ProductName = document.getElementById(
        `ProductName${wishlistItem.id}`
      ).innerText;

      if (CheckItemStock > 0) {
        if (!updatedCart.some((cartItem) => cartItem.id === wishlistItem.id)) {
          updatedCart.push({
            id: wishlistItem.id,
            quantity: 1,
          });
        }

        // Remove from wishlist
        updatedWishlist = updatedWishlist.filter(
          (item) => item.id !== wishlistItem.id
        );

        deleteWishlistItem(wishlistItem.id);
      } else {
        alert(
          `Product: "` +
            ProductName +
            `", is out of stock and will not be added to the cart.`
        );
      }
    });

    // Clear wishlist and update storage
    // wishlist = [];
    localStorage.setItem("cart", JSON.stringify(updatedCart));
    localStorage.setItem("wishList", JSON.stringify(updatedWishlist));

    // Update UI
    checkEmptyContainer();
  } catch (error) {
    console.error("Error moving all items to cart:", error);
    throw error;
  }
}

/**
 * Checks if the wishlist container is empty and updates UI accordingly
 */
function checkEmptyContainer() {
  const container = document.querySelector(".Wishlist_Products");
  if (container && container.children.length === 0) {
    container.innerHTML = "<p>No products found in the Wishlist.</p>";
  }
}

// **** Hassan Code :: we're using new logic****
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
          console.log(data.message);
        } else {
          console.log(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  } else {
    console.log("Item deletion canceled.");
  }
}
