// Get the input fields for new password and repeat password
// var userId = document.querySelector(".userId");
var oldPasswordInput = document.querySelector(".oldpass");
var newPasswordInput = document.querySelector(".newpass");
var repeatPasswordInput = document.querySelector('.rnewpass');
var oldpasswordMismatchWarning = document.getElementById('oldpasswordMismatchWarning');
var emptyFieldWarning = document.getElementById('emptyFieldWarning');
// Variable to keep track of ongoing AJAX request for old password check
var checkingOldPassword = false;

// Function to check if the old password is correct
function checkOldPassword() {
  var oldpassword = document.querySelector(".oldpass").value;
  var userIdInput = document.querySelector(".userId");
  var userId = userIdInput.value;

  var data = new FormData();
  data.append("oldpassword", oldpassword);
  data.append("user-id", userId);

  // Set the checkingOldPassword flag to true while AJAX request is ongoing
  checkingOldPassword = true;

  // Make an AJAX request to fetch the user profile data
  $.ajax({
    type: "POST",
    url: "ajax/user.ajax.php",
    data: data,
    contentType: false,
    cache: false,
    processData: false,
    dataType: "json",
    success: function(answer) {
      console.log(answer);
      checkingOldPassword = false; // Reset the flag when AJAX request is completed
      if (!answer) {
        oldpasswordMismatchWarning.style.display = 'block';
      } else {
        oldpasswordMismatchWarning.style.display = 'none';
      }
    } ,error: function(xhr, status, error) {
      console.error("AJAX request error:", error);
    }

  });
}

// Event listener to trigger the check when the user types in the old password field
oldPasswordInput.addEventListener('keyup', checkOldPassword);

// Function to check if the new and repeat passwords match
function checkPasswordMatch() {
  var newPassword = newPasswordInput.value;
  var repeatPassword = repeatPasswordInput.value;
  if (newPassword !== repeatPassword) {
    passwordMismatchWarning.style.display = 'block';
  } else {
    passwordMismatchWarning.style.display = 'none';
  }
}

// Event listener to trigger the check when the user types in the repeat password field
repeatPasswordInput.addEventListener('input', checkPasswordMatch);

 // Function to check if all required fields are filled
 function checkFormFields() {
    var oldPassword = oldPasswordInput.value.trim();
    var newPassword = newPasswordInput.value.trim();
    var repeatPassword = repeatPasswordInput.value.trim();

    if (oldPassword === "" || newPassword === "" || repeatPassword === "") {
      event.preventDefault();
      emptyFieldWarning.style.display = 'block';
      return false;
    }

    // If all checks pass, return true to allow form submission
    return true;
  }

  // Add an event listener to the "Update password" button
  $(".updatePasswordFooter2 .btn-primary").on("click", function(event) {
    
    var newPassword = document.querySelector('.newpass').value;
    // Check if old password is being checked through AJAX request
    if (checkingOldPassword) {
      event.preventDefault();
      return;
    }

    // Check if any warning message is being displayed
    if (oldpasswordMismatchWarning.style.display === 'block' || passwordMismatchWarning.style.display === 'block') {
      event.preventDefault();
      return;
    }

    // Check if all required fields are filled
    if (!checkFormFields()) {
      event.preventDefault();
      return;
    }
    
    // Check if the new password is strong enough
    if (!checkPasswordStrength(newPassword)) {
      // Display a message to the user indicating that the password is not strong enough
      // alert("Password is not strong enough. It should contain at least 8 characters, including at least one lowercase letter, one uppercase letter, one digit, and one special character.");
      
      Swal.fire(
        'Password not changed',
        'Password is not strong enough. It should contain at least 8 characters, including at least one lowercase letter, one uppercase letter, one digit, and one special character.',
        'error'
      )
      // Prevent form submission
      event.preventDefault();
      return;
  }

    // If all checks pass, submit the form
    $("#passwordChangeForm").submit();
  });
  
$(document).on("click", "#store_id", function(){

  var storeid = $(this).attr("value");

  var data= new FormData();
  data.append("store_id", storeid);

  $.ajax({
      type: "POST",
      url: "ajax/session.ajax.php",
      data: data, // Send the userId as a query parameter
      contentType:false,
      caches:false,
      processData:false,
      dataType: "json",
      success: function(answer) {
        window.location = "dashboard"
      } ,error: function(xhr, status, error) {
        console.error("AJAX request error:", error);
      }
    });
});
$(document).on("click", "#exit_store", function(){

var storeid = $(this).attr("value");

var data= new FormData();
data.append("exit_store", storeid);

$.ajax({
    type: "POST",
    url: "ajax/session.ajax.php",
    data: data, // Send the userId as a query parameter
    contentType:false,
    caches:false,
    processData:false,
    dataType: "json",
    success: function(answer) {
      window.location = "dashboard"
    } ,error: function(xhr, status, error) {
      console.error("AJAX request error:", error);
    }
  });
});

// Wait for the document to be ready
$(document).ready(function() {
  // Add an event listener to the "Change password" button
  $(".changePasswordButton").on("click", function() {
    // Hide the current content (profile information)
    $(".modalBodyContent, .updatePasswordFooter1").fadeOut(200, function() {
      // Display the password change content
      $(".passwordChangeContent").fadeIn(200);
      $(".updatePasswordFooter2").fadeIn(200);
    });
  });
  
    // Add an event listener to the modal's hidden.bs.modal event
    $('#userProfileModal').on('hidden.bs.modal', function() {
      $("#passwordChangeForm")[0].reset();
      // Reset the modal content when the modal is closed
      $(".passwordChangeContent, .updatePasswordFooter2").fadeOut(200, function() {
        // Display the profile information content
        oldpasswordMismatchWarning.style.display = 'none';
        passwordMismatchWarning.style.display = 'none';
        emptyFieldWarning.style.display = 'none';
        $(".modalBodyContent, .updatePasswordFooter1").fadeIn(200);
      });
    });
});

// Listen for changes in the profile picture input
$("#profilePictureInput").change(function () {
    // Submit the form when an image is selected
    $("#profilePictureForm").submit();

    $("#profilePictureInput").val('');
    
});

// fetch settings data
$(".settings").on("click", function() {

  $.ajax({
    type: "GET",
    url: "ajax/settings.ajax.php",
    cache: false,
    success: function(settingsData) {
      
    console.log(settingsData);
      // Parse the JSON response
      try {
        settingsData = JSON.parse(settingsData);
      } catch (error) {
        console.error("Error parsing JSON response:", error);
        return;
      }
  
      // Find the objects with SettingName
      var LoyaltyPointValue = settingsData.find(setting => setting.SettingName === "LoyaltyPointValue");
      var LoyaltyValueConversion = settingsData.find(setting => setting.SettingName === "LoyaltyValueConversion");
      var loyaltyPointsSetting = settingsData.find(setting => setting.SettingName === "Loyaltypoints");
      var CustomerDetailsSetting = settingsData.find(setting => setting.SettingName === "CustomerDetails");

      // Convert values to integers
      var loyaltyPointValueInt = parseInt(LoyaltyPointValue.SettingValue, 10);
      var loyaltyValueConversionInt = parseInt(LoyaltyValueConversion.SettingValue, 10);
      var loyaltyPointsSettingInt = parseInt(loyaltyPointsSetting.SettingValue, 10);
      var customerDetailsValueInt = parseInt(CustomerDetailsSetting.SettingValue, 10);

      // Set values for form fields
      $('#loyaltyPointValue').val(loyaltyPointValueInt);
      $('#loyaltyValueConversion').val(loyaltyValueConversionInt);
  
      // Check if both settings have SettingValue of "1"
      if (CustomerDetailsSetting && loyaltyPointsSetting) {
        if (customerDetailsValueInt === 1 && loyaltyPointsSettingInt === 1) {  
          // Check both checkboxes
          $('#loyaltyPoints').prop('checked', true);
          $('#fetchdetails').prop('checked', true);
        } else {          
          // Check a checkbox based on the condition
          if (customerDetailsValueInt === 1) {
            $('#fetchdetails').prop('checked', true);
          } else if (loyaltyPointsSettingInt === 1) {
            $('#loyaltyPoints').prop('checked', true);
          }
        }
      }
    },
    error: function(xhr, status, error) {
      console.error("AJAX request error:", error);
    }
  });
  
});

// this function holds the ajax request
function settingsAjax(item, value) {
  
  var data= new FormData();
  data.append("item", item);
  data.append("value", value);

  $.ajax({
    type: "POST",
    url: "ajax/settings.ajax.php",
    data: data, 
    contentType:false,
    caches:false,
    processData:false,
    dataType: "json",
    error: function(xhr, status, error) {
      console.error("AJAX request error:", error);
    }
  });
}


// Listen for changes in the "Activate Loyalty Points" checkbox
$('#loyaltyPoints').on('change', function() {
  var item = "Loyaltypoints";
  if (this.checked) {
    // Checkbox is checked
    // console.log("Loyalty Points activated");
    var value = 1;
    settingsAjax(item, value);
    // Perform your action here
  } else {
    // Checkbox is unchecked
    // console.log("Loyalty Points deactivated");
    var value = 0;
    settingsAjax(item, value);
    // Perform your action here
  }
});

// Listen for changes in the "Activate Loyalty Points" checkbox
$('#fetchdetails').on('change', function() {
  var item = "CustomerDetails";
  if (this.checked) {
    // Checkbox is checked
    // console.log("Loyalty Points activated");
    var value = 1;
    settingsAjax(item, value);
    // Perform your action here
  } else {
    // Checkbox is unchecked
    // console.log("Loyalty Points deactivated");
    var value = 0;
    settingsAjax(item, value);
    // Perform your action here
  }
});



// Function to check the strength of a password
function checkPasswordStrength(password) {
  // Define a regular expression pattern for a strong password
  var strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

  // Test the password against the pattern
  return strongPasswordPattern.test(password);
}

// Function to update the password strength meter and text
function updatePasswordStrength() {
  var passwordInput = document.querySelector('.newpass');
  var passwordStrengthMeter = document.getElementById('passwordStrengthMeter');
  var passwordStrengthText = document.getElementById('passwordStrengthText');

  var password = passwordInput.value;
  var strength = 0;

  if (password.length >= 8) strength += 25;
  if (/[a-z]/.test(password)) strength += 25;
  if (/[A-Z]/.test(password)) strength += 25;
  if (/[0-9]/.test(password)) strength += 25;

  passwordStrengthMeter.style.width = strength + '%';
  passwordStrengthMeter.setAttribute('aria-valuenow', strength);

  if (strength === 0) {
    passwordStrengthMeter.classList.remove('bg-danger', 'bg-warning', 'bg-info', 'bg-success');
    passwordStrengthText.innerHTML = '';
  } else if (strength <= 25) {
    passwordStrengthMeter.classList.add('bg-danger');
    passwordStrengthMeter.classList.remove('bg-warning', 'bg-info', 'bg-success');
    passwordStrengthText.innerHTML = 'Weak';
  } else if (strength <= 50) {
    passwordStrengthMeter.classList.add('bg-warning');
    passwordStrengthMeter.classList.remove('bg-danger', 'bg-info', 'bg-success');
    passwordStrengthText.innerHTML = 'Moderate';
  } else if (strength <= 75) {
    passwordStrengthMeter.classList.add('bg-info');
    passwordStrengthMeter.classList.remove('bg-danger', 'bg-warning', 'bg-success');
    passwordStrengthText.innerHTML = 'Strong';
  } else {
    passwordStrengthMeter.classList.add('bg-success');
    passwordStrengthMeter.classList.remove('bg-danger', 'bg-warning', 'bg-info');
    passwordStrengthText.innerHTML = 'Very Strong';
  }
}

// Function to handle the form submission
function validatePasswordChangeForm(event) {
  var newPassword = document.querySelector('.newpass').value;

  // Check if the new password is strong enough
  if (!checkPasswordStrength(newPassword)) {
    // Display a message to the user indicating that the password is not strong enough
    // alert("Password is not strong enough. It should contain at least 8 characters, including at least one lowercase letter, one uppercase letter, one digit, and one special character.");
    
    Swal.fire(
      'Password not changed',
      'Password is not strong enough. It should contain at least 8 characters, including at least one lowercase letter, one uppercase letter, one digit, and one special character.',
      'error'
    )
    // Prevent form submission
    event.preventDefault();
  }
}

// Add an event listener to the password input field to update the strength meter as the user types
document.querySelector('.newpass').addEventListener('input', updatePasswordStrength);

// Add an event listener to the form to trigger the password strength check on form submission
// document.getElementById('passwordChangeForm').addEventListener('submit', validatePasswordChangeForm);


