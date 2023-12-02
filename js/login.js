$(document).ready(function () {
  console.log("hai");
  $("#loginForm").submit(function (event) {
    event.preventDefault();

    var email = $("input[name='email']").val();
    var password = $("input[name='password']").val();

    $.ajax({
      type: "POST",
      url: "login.php",
      data: { login: true, email: email, password: password },
      success: function (response) {
        var responseData = JSON.parse(response);
        console.log(responseData.user_id);

        if (responseData.success) {
          localStorage.setItem("user_id", responseData.user_id);
          window.location.href = "profile.html";
        } else {
          // Display an error message on the webpage
          $("#error-message").text("Login failed. Please check your credentials.");
        }
      },
      error: function (error) {
        // Display an error message on the webpage
        $("#error-message").text("Login failed. Please try again later.");
      },
    });
  });

  // Toggle password visibility
  $("#togglePassword").click(function () {
    var passwordInput = $("#password");
    var type = passwordInput.attr("type") === "password" ? "text" : "password";
    passwordInput.attr("type", type);

    // Change the eye icon based on password visibility
    $(this).toggleClass("fa-eye-slash");
  });
});
