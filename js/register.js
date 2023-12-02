$(document).ready(function () {
  // Add click event to the Register button
  $("#registrationForm").submit(function (event) {
    // Prevent the default form submission
    event.preventDefault();

    // Manually create data object
    var fullname = $("input[name='fullname']").val();
    var email = $("input[name='email']").val();
    var password = $("input[name='password']").val();
    var repeatPassword = $("input[name='repeat_password']").val();
    var dob = $("input[name='dob']").val();
    var age = $("input[name='age']").val();
    var contact = $("input[name='contact']").val();
    var address = $("input[name='address']").val();

    var data = {
      fullname: fullname,
      email: email,
      password: password,
      repeat_password: repeatPassword,
      dob: dob,
      age: age,
      contact: contact,
      address: address,
    };

    $.ajax({
      type: "POST",
      url: "registration.php",
      data: data,
    })
    .done(function (response) {
      console.log(response);
      var responseData = JSON.parse(response);

      if (responseData.success) {
        var userId = responseData.user_id;
        alert(userId);
        localStorage.setItem("user_id", userId);
        window.location.href = "profile.html";
      } else {
        // Handle errors
        console.log(response.errors);
        alert("Registration failed. Please check your details.");
      }
    })
    .fail(function (error) {
      console.log(error);
      alert("Registration failed. Please check your details.");
    });
  });
});
