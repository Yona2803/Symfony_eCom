
function RoutePath() {
    let routeInfo = JSON.parse(localStorage.getItem("routeInfo")) || [];
    let Route_Path;
    let Route_Text;
  
    // Log the entire routeInfo array for debugging
    if (Array.isArray(routeInfo) && routeInfo.length > 0) {
      Route_Path = routeInfo[0].srcPage_Path;
      Route_Text = routeInfo[0].srcPage_Text;
    } else {
      // Add new route info
      Route_Path = "/";
      Route_Text = "Home";
    }
    document.getElementById("Route_Path").href = Route_Path;
    document.getElementById("Route_Path").innerHTML = Route_Text;
  }
  RoutePath();