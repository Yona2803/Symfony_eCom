// **
// **** initialize Elements ****
// **
const Form_ErrorMsg = document.querySelector(".Form_ErrorMsg");
const Msg_404 = document.querySelector(".Msg_404");
const Form_Profile = document.querySelector("#Edite_Profile");
const Submit_BtnForm = document.querySelector("#Submit_BtnForm");

// **
// **** Get the entire routeInfo array from LocalStorage ****
// **
function RoutePath() {
  let routeInfo = JSON.parse(localStorage.getItem("routeInfo")) || [];
  let Route_Path;
  let Route_Text;

  if (Array.isArray(routeInfo) && routeInfo.length > 0) {
    Route_Path = routeInfo[0].srcPage_Path;
    Route_Text = routeInfo[0].srcPage_Text;
  } else {
    Route_Path = "/";
    Route_Text = "Home";
  }
  document.getElementById("Route_Path").href = Route_Path;
  document.getElementById("Route_Path").innerHTML = Route_Text;
}
RoutePath();

function MyCart() {
  // Sending by Ajax to Back-End => Show response in HTML
  $.ajax({
    url: `/MyAccountPage/ShowProfile?User_id=${User_id}`,
    type: "GET",
    success: function (response) {
      if (response && response.length > 0) {
        Msg_404.style.display = "none";
        Form_Profile.style.display = "flex";
      } else {
        Msg_404.style.display = "flex";
        Form_Profile.display = "none";
      }
    },
    error: function (xhr, status, error) {
      console.log("Status:", status);
      console.log("Error:", error);
      console.log("Response:", xhr.responseText);
      console.log("Response JSON:", xhr.responseJSON);
    },
  });
}

// **
// **** PassWords ****
// **
const Current_Password = document.querySelector("#users_currentPassword");
const New_Password = document.querySelector("#users_newPassword");
const Confirm_Password = document.querySelector("#users_confirmPassword");

// **
// **** Password Validation ****
// **

function checkPassWord(Caller) {
  console.log(Caller);
  if (Caller == "NewPass") {
    if (Confirm_Password.value) {
      if (New_Password.value === Confirm_Password.value) {
        Submit_BtnForm.disabled = false;
      } else {
        Submit_BtnForm.disabled = true;
        updateStatus("PassWord");
      }
    }
  }

  if (Caller == "ConfirmPass") {
    if (New_Password.value !== Confirm_Password.value) {
      Submit_BtnForm.disabled = true;
      updateStatus("PassWord");
    } else {
      Submit_BtnForm.disabled = false;
    }
  }
}

// **
// **** Status Msg ****
// **
const statusElement = document.querySelector("#StatusMsg");

function updateStatus(StatusMsg) {
  let message = "";

  if (StatusMsg === "User_Id") {
    message = "⚠️ Error: User Id not found, the server issue.";
  } else if (StatusMsg === "User_Data") {
    message = "⚠️ Error: User Data not found, the server issue.";
  } else if (StatusMsg === "CurrentPassWord") {
    message = "⚠️ Error: Current password is incorrect.";
  } else if (StatusMsg === "NewPassWord") {
    message = "Everything Updated successfully 🎯";
  } else if (StatusMsg === "ok") {
    message = "Everything Updated successfully 💯";
  } else if (StatusMsg === "PassWord") {
    message = "⚠️ Error: New and confirmed passwords are not the same.";
  } else if (StatusMsg === "Undetermined") {
    message = "⚠️ Error: Something went wrong, please try again later.";
  }

  statusElement.innerHTML = message;
  statusElement.style.height = "48px";
  statusElement.style.width = "400px";

  setTimeout(() => {
    statusElement.innerHTML = "";
    statusElement.style.width = "0";
    statusElement.style.height = "0";
  }, 3000);
}
