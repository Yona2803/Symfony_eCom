function addRoute(Route_Path, Route_Text) {
  let routeInfo = JSON.parse(localStorage.getItem("routeInfo")) || [];
    
    if (Array.isArray(routeInfo) && routeInfo.length > 0) {
        routeInfo[0] = {
            srcPage_Path: Route_Path,
            srcPage_Text: Route_Text
        };
    } else {
        routeInfo = [{ 
            srcPage_Path: Route_Path, 
            srcPage_Text: Route_Text 
        }];
    }

    localStorage.setItem("routeInfo", JSON.stringify(routeInfo));
};