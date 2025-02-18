// Function to fetch data via Ajax and return a Promise
const fetchRecordData = async (orderId) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `/Orders/FetchRecordDetailsby/${orderId}`,
      type: "GET",
      success: function (response) {
        let ordersList = "";
        if (response && response.length > 0) {
          response.forEach(function (order) {
            ordersList += `
              <div class="Bottom-Section-Item" id="BS-Order${
                order.orderId
              }-Item-${order.itemId}">
                <div class="itemInfo">
                  ${
                    order.itemImage
                      ? `<img src="${order.itemImage}" alt="Product Image: ${order.itemName}">`
                      : `<img src="img/No_Img.png" alt="No Image Available for product: ${order.itemName}">`
                  }
                </div>
                <p>${order.itemName}</p>
                <span>${order.quantity}</span>
                <span>
                  ${new Intl.NumberFormat("fr-FR", {
                    style: "decimal",
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                  })
                    .format(order.totalPrice)
                    .replace(",", ".")}
                  &nbsp;Dh
                </span>
              </div>
            `;
          });
        } else {
          ordersList = "<p>No record found...</p>";
        }
        resolve(ordersList);
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data:", error);
        reject(error);
      },
    });
  });
};

// Handle order click
async function fetchWithCacheCondition(orderId) {
  // Assuming toggleState is defined globally
  if (toggleState) {
    return;
  }

  // Use querySelectorAll if there might be multiple elements with this id (or better use a class)
  const dataDivs = document.querySelectorAll(`#BS-Order${orderId}-List`);

  // Check if data is already in cache
  if (dataCache[orderId]) {
    dataDivs.forEach((div) => {
      div.innerHTML = dataCache[orderId];
    });
  } else {
    try {
      const data = await fetchRecordData(orderId);
      // Cache the data
      dataCache[orderId] = data;
      dataDivs.forEach((div) => {
        div.innerHTML = data;
      });
    } catch (error) {
      console.error("Error while fetching data for order:", error);
    }
  }
}
