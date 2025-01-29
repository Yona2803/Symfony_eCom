async function sendLocalStorageData() {
  try {
    if (!window.localStorage) {
      throw new Error("localStorage is not available");
    }

    let cartData;
    let wishListData;
    try {
      const rawCart = localStorage.getItem("cart");
      const rawWishList = localStorage.getItem("wishList");

      if (rawCart) {
        cartData = JSON.parse(rawCart);
      }

      if (rawWishList) {
        wishListData = JSON.parse(rawWishList);
      }
    } catch (parseError) {
      console.error("Failed to parse cart or wishList data:", parseError);
      return;
    }

    const data = {};

    if (cartData && cartData.length > 0) {
      data.cart = cartData;
    } else {
      data.cart = [];
    }

    if (wishListData && wishListData.length > 0) {
      data.wishList = wishListData;
    } else {
      data.wishList = [];
    }

    const jsonData = JSON.stringify(data);

    // Perform the API request
    const response = await fetch("/SyncLocalStorage", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      credentials: "same-origin",
      body: jsonData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseData = await response.json();

    if (responseData) {
      // Update local storage with server response
      if (responseData.updatedCart) {
        const updatedCart = responseData.updatedCart.map((item) => ({
          id: Number(item.id),
          quantity: Number(item.quantity),
        }));

        localStorage.setItem("cart", JSON.stringify(updatedCart));
      }
      if (responseData.updatedWishList) {
        const updatedWishList = responseData.updatedWishList.map((item) => ({
          id: Number(item.id),
          quantity: Number(item.quantity),
        }));

        localStorage.setItem("wishList", JSON.stringify(updatedWishList));
      }
      updateCartIcon();
    }

    return responseData;
  } catch (error) {
    console.error("Failed to sync localStorage:", error);
    throw error;
  }
}
