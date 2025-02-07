function deleteCategoryById(categoryId) {
    const userConfirmed = window.confirm(
        "Are you sure you want to remove this product from your wishlist?"
    );

    if (userConfirmed) {
        fetch(`/categories/delete/${categoryId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "successRemoving") {
                    document.getElementById(`category-${categoryId}`).remove();
                    alert(data.message);
                } else {
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

async function openPopupUpdateCategory(categoryId) {
    document.getElementById("categoryId").value = categoryId;
    const category = await getCategoryById(categoryId);

    if (category) {
        document.querySelector(".custom-name-field").value = category.name;
        if (category.image) {
            document.querySelector(".image-preview").src = `data:image/jpeg;base64,${category.image}`;
        }
    }

    document.getElementById("updateCategoryPopup").style.display = "block";
}

function closePopupUpdateCategory() {
    document.getElementById("updateCategoryPopup").style.display = "none";
}

async function getCategoryById(categoryId) {
    try {
        const response = await fetch(`/categories/${categoryId}`);
        if (!response.ok) {
            throw new Error("Category not found");
        }
        const data = await response.json();
        console.log("Fetched Data:", data);
        return data;
    } catch (error) {
        console.error("Error fetching category data:", error);
        return null;
    }
}
