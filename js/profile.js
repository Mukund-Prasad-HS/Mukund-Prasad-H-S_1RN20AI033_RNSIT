$(document).ready(function () {
  $.ajax({
    type: "POST",
    url: "profile.php",
    data: {
      action: "getProfile",
      userid: localStorage.getItem("user_id"),
    },
    dataType: "json", // Expect JSON response
    success: function (response) {
      if (response.error) {
        console.log("Error loading profile information: " + response.error);
      } else {
        displayProfile(response);
      }
    },
    error: function (error) {
      console.log("Error loading profile information.");
    },
  });

  function displayProfile(profileData) {
    $("#profileInfo").html(`
            <h2>Welcome, ${profileData.full_name}!</h2>
            <p>Email: ${profileData.email}</p>
            <p>Age: ${profileData.age}</p>
            <p>Contact: ${profileData.contact}</p>
            <p>Address: ${profileData.address}</p>
            <button id='editProfileBtn'>Edit Profile</button>
        `);
  }

  function showEditForm() {
    $.ajax({
      type: "POST",
      url: "profile.php",
      data: { action: "getEditForm" },
      dataType: "json", // Expect JSON response
      success: function (response) {
        if (response.error) {
          console.log("Error loading edit profile form: " + response.error);
        } else {
          displayEditForm(response);
        }
      },
      error: function (error) {
        console.log("Error loading edit profile form.");
      },
    });
  }

  // Function to display the edit profile form
  function displayEditForm(profileData) {
    $("#editProfileForm").html(`
            <form id='editProfileForm' action='' method='post'>
                <input type='text' name='full_name' value='${profileData.full_name}' />
                <input type='text' name='email' value='${profileData.email}' />
                <input type='text' name='age' value='${profileData.age}' />
                <input type='text' name='contact' value='${profileData.contact}' />
                <input type='text' name='address' value='${profileData.address}' />
                <button type='submit'>Save Changes</button>
                <button type='button' id='cancelEditBtn'>Cancel</button>
            </form>
        `);
    $("#profileInfo").hide();
    $("#editProfileForm").show();
  }

  // Event listener for the edit button
  $(document).on("click", "#editProfileBtn", function () {
    showEditForm();
  });

  // Event listener for the cancel button in the edit form
  $(document).on("click", "#cancelEditBtn", function () {
    $("#editProfileForm").hide();
    $("#profileInfo").show();
  });

  // Event listener for submitting the edit form
  $(document).on("submit", "#editProfileForm", function (event) {
    event.preventDefault();
    var formData = $(this).serialize();

    $.ajax({
      type: "POST",
      url: "profile.php",
      data: formData + "&action=editProfile", // Include action in form data
      dataType: "json", // Expect JSON response
      success: function (response) {
        if (response.error) {
          console.log("Error submitting edit profile form: " + response.error);
        } else {
          // Reload the profile information after editing
          loadProfile();
          $("#editProfileForm").hide();
          $("#profileInfo").show();
          alert(response.success); // You can customize this based on your needs
        }
      },
      error: function (error) {
        console.log("Error submitting edit profile form.");
        
      },
    });
  });
});
