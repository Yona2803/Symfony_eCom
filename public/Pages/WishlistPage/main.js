function deleteItem(itemId) {

    const userConfirmed = window.confirm("Are you sure you want to remove this product from your wishlist ?");

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




function addProductToWishlist(itemId){
    fetch(`/wishlist/add/${itemId}`, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        },
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