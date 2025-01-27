function sendLocalStorageData() {
  let data = {
    cartData: localStorage.getItem("cart"),
    // wishListData: localStorage.getItem('wishList')
  };
  
  if (isNaN(data)){
    return
  }

  fetch("/SyncLocalStorage", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Success:", data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}
