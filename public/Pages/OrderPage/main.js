function changeOrderStatus(orderId, orderStatus) {
    const statusChangeValidation = confirm(
        "You gonna change the status of an order, Do you want to continue?"
    );

    if (statusChangeValidation) {
        fetch(`/order/${orderId}/${orderStatus}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "successChanged") {
                    let row = document.querySelector(`#order-${orderId}`);
                    let btn = document.querySelector(`#btnComplete-${orderId}`);
                    let spanStatusName = document.querySelector(`#statusName-${orderId}`);
                    
                    // Change the background color based on the order status
                    if (data.orderStatus !== "Delivered") {
                        row.style.backgroundColor = "pink";
                    } else {
                        row.style.backgroundColor = "rgb(177, 225, 192)";
                        const dropdown = document.querySelector(`#dropdown-container-${orderId}`);
                        dropdown.style.display = "none";
                    }

                    // change the the value of the order status
                    spanStatusName.textContent = orderStatus;

                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    }
}

// Get all dropdown elements
const dropdowns = document.querySelectorAll(".dropdown");

// Function to close all dropdowns
function closeAllDropdowns() {
    document.querySelectorAll(".dropdown-content-v1").forEach((content) => {
        content.style.display = "none";
    });
}

// Track the current open dropdown
let currentOpenDropdown = null;

// Add click event listeners to each dropdown
dropdowns.forEach((dropdown) => {
    const button = dropdown.querySelector(".dropbtn");
    const content = dropdown.querySelector(".dropdown-content-v1");

    // Toggle dropdown when clicking the button
    button.addEventListener("click", (e) => {
        e.stopPropagation(); // Prevent event from bubbling up

        // If this dropdown is already open, close it and reset tracking
        if (content.style.display === "flex") {
            content.style.display = "none";
            currentOpenDropdown = null;
            return;
        }

        // Close all dropdowns first
        closeAllDropdowns();

        // Open this dropdown and track it
        content.style.display = "flex";
        currentOpenDropdown = content;
    });

    // Prevent dropdown from closing when clicking inside it
    content.addEventListener("click", (e) => {
        e.stopPropagation();
    });
});

// Close dropdown when clicking anywhere else on the page
document.addEventListener("click", (e) => {
    if (currentOpenDropdown && !e.target.closest(".dropdown")) {
        currentOpenDropdown.style.display = "none";
        currentOpenDropdown = null;
    }
});

// Optional: Close dropdown when pressing Escape key
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && currentOpenDropdown) {
        currentOpenDropdown.style.display = "none";
        currentOpenDropdown = null;
    }
});

// Add click handlers for the status buttons
document.querySelectorAll(".dropdown-content-v1 button").forEach((button) => {
    button.addEventListener("click", (e) => {
        const button = e.target.closest("button");
        const orderRow = e.target.closest("tr");
        const orderId = orderRow.querySelector("#inputOrderId").value;

        const newStatus = e.target.textContent.trim();
        console.log(`Status changed to: ${newStatus} // orderId: ${orderId}`);

        if (newStatus === "Accepted" || newStatus === "Declined") {
            changeOrderStateStatus(orderId, newStatus);
        } else {
            changeOrderStatus(orderId, newStatus);
        }

        if (currentOpenDropdown) {
            currentOpenDropdown.style.display = "none";
            currentOpenDropdown = null;
        }
    });
});

async function getOrderDetailsByOrderId(orderId) {
    try {
        const response = await fetch(`/orderDetails/${orderId}`);
        if (!response.ok) {
            throw new Error("Order details not found");
        }
        const data = await response.json();
        console.log("Fetched Data: ", data);
        return data;
    } catch (error) {
        console.error("Error fetching data:", error);
        return null;
    }
}

async function openPopupOrderDetails(orderId) {
    const loadingMessage = document.getElementById("loadingMessage"); // Add a loading message element in your HTML
    const ordersDetailsPopup = document.getElementById("ordersDetails");

    try {
        // Show loading message
        if (loadingMessage) {
            loadingMessage.style.display = "flex";
        }

        // Hide the popup initially (in case it was already open)
        ordersDetailsPopup.style.display = "none";

        // Fetch order details
        const data = await getOrderDetailsByOrderId(orderId);

        // Hide loading message
        if (loadingMessage) {
            loadingMessage.style.display = "none";
        }

        // Check if data is valid
        if (
            !data ||
            !Array.isArray(data.orderDetails) ||
            data.orderDetails.length === 0
        ) {
            document.getElementById("order-details-body").innerHTML =
                '<tr><td colspan="4">No order details found.</td></tr>';
            ordersDetailsPopup.style.display = "block";
            return;
        }

        // Récupération des informations du client (on prend le premier élément)
        const orderInfo = data.orderDetails[0];

        const customerDetails = document.getElementById("customer-details");
        const formattedDate = new Date(orderInfo.orderDate.date).toLocaleDateString(
            "en-EN",
            {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            }
        );

        customerDetails.innerHTML = `
            <p><strong>Client:</strong> ${orderInfo.firstName || ""} ${orderInfo.lastName || ""
            }</p>
            <p><strong>Email:</strong> ${orderInfo.email}</p>
            <p><strong>Order N°:</strong> ${orderInfo.orderId}</p>
            <p><strong>Date:</strong> ${formattedDate}</p>
            <p><strong>Montant Total:</strong> ${orderInfo.totalAmount.toFixed(
                2
            )} DH</p>
        `;

        // Génération du tableau des détails de commande
        const tableBody = document.getElementById("order-details-body");
        tableBody.innerHTML = data.orderDetails
            .map(
                (detail) => `
            <tr>
                <td>${detail.name}</td>
                <td style="text-align: center;">${detail.quantity}</td>
                <td>${detail.price.toFixed(2)} DH</td>
                <td>${detail.totalPrice.toFixed(2)} DH</td>
            </tr>
        `
            )
            .join("");

        // Afficher la popup
        ordersDetailsPopup.style.display = "block";
    } catch (error) {
        console.error("Error:", error);

        // Hide loading message in case of error
        if (loadingMessage) {
            loadingMessage.style.display = "none";
        }

        // Show error message to the user
        alert("Unable to load order details. Please try again later.");
    }
}

function closePopupOrderDetails() {
    document.getElementById("ordersDetails").style.display = "none";
}

function changeOrderStateStatus(orderId, orderStateStatus) {
    fetch(`/order/${orderId}/state/${orderStateStatus}`, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            console.log("from state", data);
            let row = document.querySelector(`#order-${orderId}`);
            let btn = document.querySelector(`#btnComplete-${orderId}`);
            let spanStatusName = document.querySelector(`#statusName-${orderId}`);

            // Change the background color based on the order status
            if (data) {
                if (orderStateStatus === "Accepted") {
                    row.style.backgroundColor = "rgb(177, 225, 192)";
                } else {
                    row.style.backgroundColor = "pink";
                }
                let td = document.querySelector(`#order-State-Status-Span-${orderId}`);
                td.textContent = orderStateStatus;
            }
        });
}
