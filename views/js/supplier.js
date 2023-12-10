
/*=============================================
EDIT PRODUCT
=============================================*/

$(".tables tbody").on("click", "button.btnEditSupplier", function(){

	var Supplier = $(this).attr("idSupplier");
	
	var datum = new FormData();
    datum.append("Supplier", Supplier);

    $.ajax({

        url:"ajax/supplier.ajax.php",
        method: "POST",
        data: datum,
        cache: false,
        contentType: false,
        processData: false,
        dataType:"json",
        success:function(answer){

        $("#supplierId").val(answer["supplierid"]);

        $("#newSupplier").val(answer["name"]);

        $("#newAddress").val(answer["address"]);

        $("#newEmail").val(answer["email"]);

        $("#newContact").val(answer["contact"]);

    }, error: function() {
        Swal.fire("Error", "Failed to retrieve supplier data from the server.", "error");
    }

  })

})

/*=============================================
DELETE SUPPLIERS
=============================================*/

$(".tables tbody").on("click", "button.btnDeleteSupplier", function(){
    console.log('clicked');

	var Supplier = $(this).attr("idSupplier");
	
	Swal.fire({

		title: 'Are you sure you want to delete the supplier?',
		text: "If you're not sure you can cancel this action!",
		icon: 'warning',
        showCancelButton: true,
		confirmButtonColor: '#0069d9',
		cancelButtonColor: '#d33',
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Yes, delete supplier!'
        }).then(function(result){
        if (result.value) {

        	window.location = "index.php?route=suppliers&id="+Supplier;

        }

	})

})

// Validate fields in the add supplier form
function validateAddedSupplier() {
    var contactInput = document.getElementById('Contact').value;
  
    // Check phone length and prefix
    if (contactInput.startsWith("254")) {
        // Phone starts with 254, should have a minimum and maximum of 12 characters
        if (contactInput.length < 12 || contactInput.length > 12) {
            Swal.fire({
            icon: "warning",
            title: "Invalid Phone Number",
            text: "Phone number should have exactly 12 digits when starting with 254.",
            confirmButtonColor: '#0069d9',
            });
            return false; // Prevent form submission
        }
    } else if (contactInput.startsWith("01") || contactInput.startsWith("07")) {
        // Phone starts with 01 or 07, should have a minimum and maximum of 10 characters
        if (contactInput.length < 10 || contactInput.length > 10) {
            Swal.fire({
            icon: "warning",
            title: "Invalid Phone Number",
            text: "Phone number should have exactly 10 digits when starting with 01 or 07.",
            confirmButtonColor: '#0069d9',
            });
            return false; // Prevent form submission
        }
    } else {
        // Phone has an invalid prefix
        Swal.fire({
            icon: "warning",
            title: "Invalid Phone Number",
            text: "Phone number should start with 254, 01, or 07.",
            confirmButtonColor: '#0069d9',
        });
        return false; // Prevent form submission
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailInput = document.getElementById('Email').value;
    const isEmailValid = emailRegex.test(emailInput);

    if (isEmailValid) {
        // Form submission is valid
        return true;
    } else {
        // Email address is invalid
        Swal.fire('Error!', 'Invalid email address.', 'error');
        return false;
    }
    
}

// Validate fields in the edit supplier form

function validateEdittedSupplier() {
    var contactInput2 = document.getElementById('newContact').value;
  
    // Check phone length and prefix
    if (contactInput2.startsWith("254")) {
        // Phone starts with 254, should have a minimum and maximum of 12 characters
        if (contactInput2.length < 12 || contactInput2.length > 12) {
            Swal.fire({
            icon: "warning",
            title: "Invalid Phone Number",
            text: "Phone number should have exactly 12 digits when starting with 254.",
            confirmButtonColor: '#0069d9',
            });
            return false; // Prevent form submission
        }
    } else if (contactInput2.startsWith("01") || contactInput2.startsWith("07")) {
        // Phone starts with 01 or 07, should have a minimum and maximum of 10 characters
        if (contactInput2.length < 10 || contactInput2.length > 10) {
            Swal.fire({
            icon: "warning",
            title: "Invalid Phone Number",
            text: "Phone number should have exactly 10 digits when starting with 01 or 07.",
            confirmButtonColor: '#0069d9',
            });
            return false; // Prevent form submission
        }
    } else {
        // Phone has an invalid prefix
        Swal.fire({
            icon: "warning",
            title: "Invalid Phone Number",
            text: "Phone number should start with 254, 01, or 07.",
            confirmButtonColor: '#0069d9',
        });
        return false; // Prevent form submission
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailInput = document.getElementById('newEmail').value;
    const isEmailValid = emailRegex.test(emailInput);

    if (isEmailValid) {
        // Form submission is valid
        return true;
    } else {
        // Email address is invalid
        Swal.fire('Error!', 'Invalid email address.', 'error');
        return false;
    }
    
}
