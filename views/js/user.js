/*=============================================
UPLOADING USER PICTURE
=============================================*/

$(".userphoto").change(function(){

	var img = this.files[0];

	/*===============================================
	=            validating image format            =
	===============================================*/
	
	if (img["type"] != "image/jpeg" && img["type"] != "image/png"){

		$(".userphoto").val("");

		Swal.fire({
			icon: "error",
			title: "Error uploading image",
			text: "Image has to be JPEG or PNG!",
			showConfirmButton: true,
			confirmButtonColor: '#0069d9',
			confirmButtonText: "Close"
		});

	}else if(img["size"] > 2000000){

		$(".userphoto").val("");
 
		Swal.fire({
			icon: "error",
			title: "Error uploading image",
			text: "Image too big. It has to be less than 2Mb!",
			showConfirmButton: true,
			confirmButtonColor: '#0069d9',
			confirmButtonText: "Close"
		});

	}else{

		var imgData = new FileReader;
		imgData.readAsDataURL(img);

		$(imgData).on("load", function(event){
			
			var routeImg = event.target.result;

			$(".preview").attr("src", routeImg);

		});

	}
		
	/*=====  End of validating image format  ======*/
	
})


/*=============================================
EDITING USER PICTURE
=============================================*/
$(document).on("click", ".btnEditUser", function(){

 	var userId = $(this).attr("userId");

 	var data = new FormData();
 	data.append("userId", userId);

 	$.ajax({

 		url: "ajax/user.ajax.php",
 		method: "POST",
 		data: data,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(answer){

 			$("#editName").val(answer["name"]);

 			$("#editUsername").val(answer["username"]);

			// Assuming you have a variable named "selectedValue" that holds the desired value
			var selectedRole = answer["role"];
			// Find the select element by its name attribute
			var selectElement = document.querySelector('select[name="editRoleOptions"]');
			// Set the selected value
			selectElement.value = selectedRole;

			// Assuming you have a value you want to select, stored in a variable called 'selectedValue'
			var selectedValue = answer["store_id"];
			// Get the select element
			var selectElement = document.getElementById("Editstore");
			// Set the selected value
			selectElement.value = selectedValue;

 			$("#actualPassword").val(answer["userpassword"]);

 			$("#actualPhoto").val(answer["userphoto"]);

 			$("#currentRole").val(answer.role);

 			$("#editemail").val(answer.email);
 			
 			if(answer["userphoto"] != ''){

 				$('.preview').attr('src', answer["userphoto"]);

 			}

 		}, error: function() {
			Swal.fire("Error", "Failed to retrieve user data from the server.", "error");
		}

 	});

 });


/*=============================================
ACTIVATE USER
=============================================*/
$(document).on("click", ".btnActivate", function(){

	var userId = $(this).attr("userid");
	var userStatus = $(this).attr("status");

	var datum = new FormData();
 	datum.append("activateId", userId);
  	datum.append("activateUser", userStatus);

  	$.ajax({

		url:"ajax/user.ajax.php",
		method: "POST",
		data: datum,
		cache: false,
		contentType: false,
		processData: false,
		success: function(answer){
		
		// console.log("answer", answer);

			if(window.matchMedia("(max-width:767px)").matches){
			
				Swal.fire({
					title: "The user status has been updated",
					icon: "success",
					confirmButtonColor: '#0069d9',
					confirmButtonText: "Close"	
				}).then(function(result) {

					if (result.value) {
						window.location = "registration";
					}

				})

			}
		
		}, error: function() {
			Swal.fire("Error", "Failed to retrieve user data from the server.", "error");
		}

  	})

  	if(userStatus == 0){

  		$(this).removeClass('btn-success');
  		$(this).addClass('btn-danger');
  		$(this).html('Deactivated');
  		$(this).attr('status',1);

  	}else{

  		$(this).addClass('btn-success');
  		$(this).removeClass('btn-danger');
  		$(this).html('Activated');
  		$(this).attr('status',0);

  	}

});


/*=============================================
VALIDATE IF USER ALREADY EXISTS
=============================================*/

$("#username").change(function(){

	$(".alert").remove();

	var user = $(this).val();

	var data = new FormData();
 	data.append("validateUser", user);

  	$.ajax({

		url:"ajax/user.ajax.php",
		method: "POST",
		data: data,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(answer){ 

			//console.log("answer", answer);

			if(answer){

				$("#username").parent().after('<div class="alert alert-warning">This user is already taken</div>');
				
				$("#username").val('');
			}

		}, error: function() {
			Swal.fire("Error", "Failed to retrieve user data from the server.", "error");
		}

    });

});

/*=============================================
VALIDATE IF EMAIL ALREADY EXISTS
=============================================*/

$("#email").change(function(){

	$(".alert").remove();

	var email = $(this).val();

	var data = new FormData();
 	data.append("validateEmail", email);

  	$.ajax({

		url:"ajax/user.ajax.php",
		method: "POST",
		data: data,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(answer){ 

			//console.log("answer", answer);

			if(answer){

				$("#email").parent().after('<div class="alert alert-warning">This email already exists in the system</div>');
				
				$("#email").val('');
			}

		}, error: function() {
			Swal.fire("Error", "Failed to retrieve user data from the server.", "error");
		}

    });

});

/*=============================================
 DELETE USER
 =============================================*/

$(document).on("click", ".btnDeleteUser", function(){

	var userId = $(this).attr("userId");
	var userphoto = $(this).attr("userphoto");
	var username = $(this).attr("username");

	Swal.fire({
		title: 'Are you sure you want to delete the user?',
		text: "if you're not sure you can cancel!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#0069d9',
		  cancelButtonColor: '#d33',
		  cancelButtonText: 'Cancel',
		  confirmButtonText: 'Yes, delete user!'
		}).then(function(result){

		if(result.value){

		  window.location = "index.php?route=registration&userId="+userId+"&username="+username+"&userphoto="+userphoto;

		}

	})

});

  // JavaScript to handle the "View Profile" link click event
$(document).ready(function() {
    $(".view-profile-link").click(function(e) {
        e.preventDefault();
        var userId = $(this).attr("userid");

		var data= new FormData();

		data.append("userId", userId);
        // Make an AJAX request to fetch the user profile data
        $.ajax({
            type: "POST",
            url: "ajax/user.ajax.php",
            data: data, // Send the userId as a query parameter
			contentType:false,
			caches:false,
			processData:false,
            dataType: "json",
            success: function(answer) {
					$("#profilePicture").attr("src", answer.userphoto);
				  $("#userName").text(answer.name);
				  $("#userEmail").text("Email: " + answer.email);
				  $("#userRole").text("Role: " + answer.role);


				  var data= new FormData();
				  data.append("Storeid",answer.store_id);
				  $.ajax({
					type: "POST",
					url: "ajax/stores.ajax.php",
					data: data, // Send the userId as a query parameter
					contentType:false,
					caches:false,
					processData:false,
					dataType: "json",
					success: function(answer) {
						$("#userStore").text("Store: " + answer.store_name);
					}
				});

                // Show the modal
                $("#userProfileModal").modal("show");
            }, error: function() {
				Swal.fire("Error", "Failed to retrieve user data from the server.", "error");
			}
        });
    });
});



