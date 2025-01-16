function deleteItem(itemId) {
    fetch(`/wishlist/delete/${itemId}`, {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then((response) => {
            if (response.ok) {
                document.getElementById(`item-${itemId}`).remove();
            } else {
                console.error("Failed to delete item");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}
