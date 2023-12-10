/*=============================================
EDIT DISCOUNT
=============================================*/

$(".tables").on("click", ".btnEditDiscount", function(){

	var idDiscount = $(this).attr("idDiscount");
    // console.log(idDiscount);

	var datum = new FormData();
	datum.append("idDiscount", idDiscount);

	$.ajax({
		url: "ajax/discount.ajax.php",
		method: "POST",
      	data: datum,
      	cache: false,
     	contentType: false,
     	processData: false,
     	dataType:"json",
     	success: function(answer){
     		
     		// console.log("answer", answer);
             datum.append("barcodeProduct",answer["product"]);

            $.ajax({
        
                url:"ajax/products.ajax.php",
                method: "POST",
                data: datum,
                cache: false,
                contentType: false,
                processData: false,
                dataType:"json",
                success:function(answer){

                    $("#editproduct").val(answer["product"]);

                }, error: function() {
                    Swal.fire("Error", "Failed to retrieve product data from the server.", "error");
                }
              
            });
        

            $("#editdiscountname").val(answer["discount"]);
        
            $("#editdiscountamount").val(answer["amount"]);

            $("#editstartdate").val(answer["startdate"]);

            $("#editenddate").val(answer["enddate"]);

            $("#barcode").val(answer["product"]);

            $("#discountid").val(answer["disId"]);

     	}, error: function() {
			Swal.fire("Error", "Failed to retrieve discount data from the server.", "error");
		}

	});

})

/*=============================================
DELETE Discount
=============================================*/
$(".tables").on("click", ".btnDeleteDiscount", function(){

	 var idDiscount = $(this).attr("idDiscount");

	 Swal.fire({
	 	title: 'Are you sure you want to delete the discount?',
		text: "If you're not sure you can cancel!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#0069d9',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancel',
		confirmButtonText: 'Yes, delete discount!'
	 }).then(function(result){

	 	if(result.value){

	 		window.location = "index.php?route=discount&idDiscount="+idDiscount;

	 	}

	 })

})


    function validateStartDateInputs(dateInput){
        // Get the current date
        const currentDate = new Date();
        // Get the selected start date
        const startDate = new Date(dateInput.value);

        // Compare the start date with the current date
        if (startDate < currentDate) {
            Swal.fire("Error", "Start date cannot be less than the current date.", "error");
            dateInput.value = ''; // Clear the invalid value
        }
    }
    // Get the start date and end date input elements
    const startDateInput = document.getElementById('startdate');
    const endDateInput = document.getElementById('enddate');

    // Get the editstart date and edit end date input elements
    const editstartDateInput = document.getElementById('editstartdate');
    const editendDateInput = document.getElementById('editenddate');

    // Add event listener to start date input
    startDateInput.addEventListener('change', function() {
        validateStartDateInputs(startDateInput);
    });

    // Add event listener to edit start date input
    startDateInput.addEventListener('change', function() {
        validateStartDateInputs(editstartDateInput);
    });

    // Add event listener to end date input
    endDateInput.addEventListener('change', function() {
        // Get the selected start date and end date
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        // Calculate the time difference in milliseconds
        const timeDiff = endDate.getTime() - startDate.getTime();

        // Calculate the time difference in hours
        const timeDiffInHours = timeDiff / (1000 * 3600);

        // Check if the time difference is less than 24 hours
        if (timeDiffInHours < 24) {
            Swal.fire("Error", "The time difference between the start date and end date should be at least 24 hours.", "error");
            endDateInput.value = ''; // Clear the invalid value
        }
    });

    // Add event listener to end date input
    editendDateInput.addEventListener('change', function() {
        // Get the selected start date and end date
        const startDate = new Date(editstartDateInput.value);
        const endDate = new Date(editendDateInput.value);

        // Calculate the time difference in milliseconds
        const timeDiff = endDate.getTime() - startDate.getTime();

        // Calculate the time difference in hours
        const timeDiffInHours = timeDiff / (1000 * 3600);

        // Check if the time difference is less than 24 hours
        if (timeDiffInHours < 24) {
            Swal.fire("Error", "The time difference between the start date and end date should be at least 24 hours.", "error");
            endDateInput.value = ''; // Clear the invalid value
        }
    });

$(".tables").on("change keyup", ".btnEditDiscount", function(){

});
