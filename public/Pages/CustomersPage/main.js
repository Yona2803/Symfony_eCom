function deleteCustomer(customerId) {
    const userConfirmed = window.confirm(
        "Are you sure you want to remove this customer?"
    );

    if (userConfirmed) {
        fetch(`/Users/delete/${customerId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "successRemoving") {
                    document.getElementById(`customer-${customerId}`).remove();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            });
        } else {
        
        console.log("Customer deletion canceled.");
    }
}
