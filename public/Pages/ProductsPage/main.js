// Add Route to Local storage
addRoute("/Products", "Products &#x2f; ");




async function openPopup(productId) {
  
  const loadingMessage = document.getElementById("loadingMessage"); // Add a loading message element in your HTML
  document.getElementById("productId").value = productId;

  try {
    // Show loading message
    if (loadingMessage) {
      loadingMessage.style.display = "flex";
  }

    const productData = await getProductData(productId);

    if (productData) {

      // Hide loading message
      if (loadingMessage) {
        loadingMessage.style.display = "none";
    }

      document.querySelector(".custom-name-field").value = productData.name;
      document.querySelector(".custom-price-field").value = productData.price;
      document.querySelector(".custom-stock-field").value = productData.stock;
      document.querySelector(".custom-description-field").value =
        productData.description;
      document.querySelector(".custom-category-field").value =
        productData.category;

        if (productData.image){
          document.querySelector(".image-preview").src = `data:image/jpeg;base64,${productData.image}`;
        } else{
          document.querySelector(".image-preview").src = 'img/No_Img.png';
        }

      productData.tags.forEach((tag) => {
        const checkbox = document.querySelector(
          `.custom-tags-field input[value="${tag}"]`
        );
        if (checkbox) {
          checkbox.checked = true;
        }
      });

      const popup = document.getElementById("updateProduct");
      popup.style.display = "flex";
      popup.style.justifyContent = "center";
      popup.style.alignItems = "center";
    }
  } catch (error) {
    console.error("Error opening popup:", error);
  }
}

function closePopup() {
  document.getElementById("updateProduct").style.display = "none";
}

async function getProductData(productId) {
  try {
    const response = await fetch(`/products/${productId}`);
    if (!response.ok) {
      throw new Error("Product not found");
    }
    const data = await response.json();
    console.log("Fetched Data:", data);
    return data;
  } catch (error) {
    console.error("Error fetching product data:", error);
    return null;
  }
}
