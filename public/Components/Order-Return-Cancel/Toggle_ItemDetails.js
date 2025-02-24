// Global state: true => "detailed toggling disabled"
let toggleState = false;

// --- Helper Functions to Update an Order's Display ---
function setOrderDetailed(order) {
  const TopSection_icon = order.querySelector(".Icon");
  const BoxClose = order.querySelector(".BoxClose");
  const BoxOpen = order.querySelector(".BoxOpen");
  const LeftSection = order.querySelector(".Top-Section-left");
  const dropdownBtn = order.querySelector(".dropdown");
  const BottomSection = order.querySelector(".Bottom-Section");
  const Mid_Section = order.querySelectorAll(".Middle-Section");

  // Top-Section-Icon
  TopSection_icon.classList.remove("UnDetailed_TS_Icon");
  TopSection_icon.classList.add("Detailed_TS_Icon");
  BoxClose.style.display = "none";
  BoxOpen.style.display = "flex";

  // Top-Section-left
  LeftSection.classList.remove("UnDetailed_TS_Left");
  LeftSection.classList.add("Detailed_TS_Left");
  LeftSection.querySelector("h4").style.fontSize = "20px";

  // Top-Section-Right
  if (dropdownBtn) {
    dropdownBtn.style.display = "block";
  }

  // Bottom-Section
  BottomSection.style.display = "flex";

  // Middle-Section
  Mid_Section.forEach((Section) => {
    if (
      Section.classList.contains("StatusCancel") ||
      Section.classList.contains("StatusReturn")
    ) {
      Section.style.display = "flex";
    }
  });
}

function setOrderUndetailed(order) {
  const TopSection_icon = order.querySelector(".Icon");
  const BoxClose = order.querySelector(".BoxClose");
  const BoxOpen = order.querySelector(".BoxOpen");
  const LeftSection = order.querySelector(".Top-Section-left");
  const dropdownBtn = order.querySelector(".dropdown");
  const BottomSection = order.querySelector(".Bottom-Section");
  const Mid_Section = order.querySelectorAll(".Middle-Section");

  // Top-Section-Icon
  TopSection_icon.classList.add("UnDetailed_TS_Icon");
  TopSection_icon.classList.remove("Detailed_TS_Icon");

  BoxClose.style.display = "flex";
  BoxOpen.style.display = "none";

  // Top-Section-left
  LeftSection.classList.add("UnDetailed_TS_Left");
  LeftSection.classList.remove("Detailed_TS_Left");
  LeftSection.querySelector("h4").style.fontSize = "16px";

  // Top-Section-Right  
  if (dropdownBtn) {
  dropdownBtn.style.display = "none";
  }
  // Bottom-Section
  BottomSection.style.display = "none";

  // Middle-Section
  Mid_Section.forEach((Section) => {
    if (
      Section.classList.contains("StatusCancel") ||
      Section.classList.contains("StatusReturn")
    ) {
      Section.style.display = "none";
    }
  });
}

// --- Attach Click Handlers Once ---
function attachOrderEventListeners() {
  const orders = document.querySelectorAll(".ThisOrder");

  orders.forEach((order) => {
    order.addEventListener("click", () => {
      // Reset all other orders to UnDetailed first
      orders.forEach((otherOrder) => {
        if (otherOrder !== order) {
          setOrderUndetailed(otherOrder);
        }
      });

      const LeftSection = order.querySelector(".Top-Section-left");

      // Check global toggleState:
      if (toggleState === true) {
        // Toggling to Detailed is disabled.
        if (LeftSection.classList.contains("Detailed_TS_Left")) {
          // If already detailed, allow collapsing.
          setOrderUndetailed(order);
        } else {
          return;
        }
      } else {
        // Normal toggling: click toggles between Detailed and UnDetailed.
        if (LeftSection.classList.contains("Detailed_TS_Left")) {
          setOrderUndetailed(order);
        } else {
          setOrderDetailed(order);
        }
      }
    });
  });
}

// --- Update Loading Elements & Force Reset Orders if Needed ---
function LoadingElements(state) {
  // --- Initialize ---
  if (!state) {
    attachOrderEventListeners();
  }

  // Update the global toggle state.
  toggleState = state;

  // If toggling to Detailed is now disabled (state is true),
  // force any Detailed orders to switch to UnDetailed.
  if (toggleState === true) {
    const orders = document.querySelectorAll(".ThisOrder");
    orders.forEach((order) => {
      const LeftSection = order.querySelector(".Top-Section-left");
      if (LeftSection.classList.contains("Detailed_TS_Left")) {
        setOrderUndetailed(order);
      }
    });
  }
}
