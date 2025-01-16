// Add to cart (example in JavaScript)
function addToCart(id) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const existingItem = cart.find((item) => item.id === id);

  let quantity = parseInt(document.getElementById("quantity" + id).value) || 1;

  if (existingItem) {
    existingItem.quantity = quantity;
  } else {
    cart.push({ id: id, quantity: quantity });
  }
  localStorage.setItem("cart", JSON.stringify(cart));

  // call to update the icon page
  updateCartIcon();
}

// Get elements and initial values
const productNameElement = document.getElementById("ProductName");
const routePathElement = document.getElementById("Route_Path");
const ProductName = productNameElement ? productNameElement.innerHTML : "";

function getRoute() {
  const routeInfo = JSON.parse(localStorage.getItem("routeInfo")) || [];
  let Route_Path = "";
  let Route_Text = "";
  if (Array.isArray(routeInfo) && routeInfo.length > 0) {
    if (
      routeInfo[0].srcPage_Text === "Home &#x2f; " ||
      routeInfo[0].srcPage_Text === "Products &#x2f; "
    ) {
      Route_Path =
        routeInfo[0].srcPage_Text === "Home &#x2f; " ? "/" : "/Products";
      Route_Text =
        routeInfo[0].srcPage_Text === "Home &#x2f; "
          ? "Home &#x2f; "
          : "Products &#x2f; ";
      addRoute(
        `ProductDetails/${idPrd}`,
        `${Route_Text}${ProductName} &#x2f; `
      );
    } else {
      Route_Path =
        routeInfo[0].srcPage_Text === `Home &#x2f; ${ProductName} &#x2f; `
          ? "/"
          : "/Products";
      Route_Text =
        routeInfo[0].srcPage_Text === `Home &#x2f; ${ProductName} &#x2f; `
          ? "Home &#x2f; "
          : "Products &#x2f; ";
      addRoute(`ProductDetails/${idPrd}`, `${Route_Text}`);
    }

    routePathElement.href = Route_Path ? Route_Path : routeInfo[0].srcPage_Path;
    routePathElement.innerHTML = Route_Text;
  }
}

getRoute();
