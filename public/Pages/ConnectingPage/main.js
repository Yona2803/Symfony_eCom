let logIn = document.querySelector("#logIn-registration");
let signIn = document.querySelector("#signIn-registration");

function SwitchTo(Form) {
  if (Form === "signIn") {
    logIn.style.display = "none";
    signIn.style.display = "flex";
  } else if (Form === "logIn") {
    logIn.style.display = "flex";
    signIn.style.display = "none";
  }
}

// function SendLocalstorage() {
//   let cart = JSON.parse(localStorage.getItem("cart")) || [];

//   if (cart) {
//      console.log("Cart: ".cart) 
//      return;
//   } else {
//     console.log("no Carte")
//     return ;
//   }
//   let queryParams = encodeURIComponent(JSON.stringify(cart));

//   // Sending by Ajax to Back-End => Show response in HTML
//   $.ajax({
//     url: `/MyCart/ShowProducts?items_ids=${queryParams}`,
//     type: "POST",
//     success: function (response) {
//       if (response && response.length > 0) {
//         Msg_404.style.display = "none";
//         Form_Profile.style.display = "flex";
//       } else {
//         Msg_404.style.display = "flex";
//         Form_Profile.display = "none";
//       }
//     },
//     error: function (xhr, status, error) {
//       console.log("Status:", status);
//       console.log("Error:", error);
//       console.log("Response:", xhr.responseText);
//       console.log("Response JSON:", xhr.responseJSON);
//     },
//   });
// }
