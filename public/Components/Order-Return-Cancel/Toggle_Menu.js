function Callitnow() {
  // Get all dropdown elements
  const dropdowns = document.querySelectorAll(".dropdown");

  // Function to close all dropdowns
  function closeAllDropdowns() {
    document.querySelectorAll(".dropdown-content").forEach((content) => {
      content.style.display = "none";
    });
    document.querySelectorAll(".dropbtn svg").forEach((svgArrow) => {
      svgArrow.style.rotate = "0deg";
    });
  }

  // Track the current open dropdown and its arrow
  let currentOpenDropdown = null;
  let currentArrow = null;

  // Add click event listeners to each dropdown
  dropdowns.forEach((dropdown) => {
    const button = dropdown.querySelector(".dropbtn");
    const svgArrow = dropdown.querySelector(".dropbtn svg");
    const content = dropdown.querySelector(".dropdown-content");

    // Toggle dropdown when clicking the button
    button.addEventListener("click", (e) => {
      e.stopPropagation();

      // If this dropdown is already open, close it and reset tracking
      if (content.style.display === "flex") {
        content.style.display = "none";
        svgArrow.style.rotate = "0deg";
        currentOpenDropdown = null;
        currentArrow = null;
        return;
      }

      // Close all dropdowns first
      closeAllDropdowns();

      // Open this dropdown and track it
      content.style.display = "flex";
      svgArrow.style.rotate = "180deg";

      currentOpenDropdown = content;
      currentArrow = svgArrow;
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
      currentArrow.style.rotate = "0deg";
      currentOpenDropdown = null;
      currentArrow = null;
    }
  });

  // Optional: Close dropdown when pressing Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && currentOpenDropdown) {
      currentOpenDropdown.style.display = "none";
      currentArrow.style.rotate = "0deg";
      currentOpenDropdown = null;
      currentArrow = null;
    }
  });

  // Add click handlers for the status buttons
  document.querySelectorAll(".dropdown-content button").forEach((button) => {
    button.addEventListener("click", (e) => {
      // const newStatus = e.target.textContent.trim();

      ChangeStatus(e.target.id);

      if (currentOpenDropdown) {
        currentOpenDropdown.style.display = "none";
        currentArrow.style.rotate = "0deg";
        currentOpenDropdown = null;
        currentArrow = null;
      }
    });
  });
}

async function ChangeStatus(params) {
  // Sending by Ajax to Back-End => Show response in HTML
  $.ajax({
    url: `/Orders/Status/${params}`,
    type: "GET",
    success: function (response) {
      if (!response.status || !response.orderId) {
        alert("Something went wrong,.. we can't update the status");
      } else {
        document.getElementById(`StatusId${response.orderId}`).innerHTML = response.status;
      }
    },
  });
}
