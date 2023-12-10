$(".reciept").change(function(){
	var file = this.files[0];
	var fileSize = file.size; // File size in bytes
	var maxSize = 2 * 1024 * 1024; // 2MB in bytes
	var validFileTypes = ["application/pdf", "image/jpeg", "image/png", "image/gif"];
	var fileType = file.type;
	// Check if the file type is valid
	if (!validFileTypes.includes(fileType)) {
		// Invalid file type 
		Swal.fire({
		      title: "Error uploading image",
		      text: "Invalid file type. Please choose a PDF or an image file.",
		      icon: "error",
			  confirmButtonColor: '#0069d9',
		      confirmButtonText: "Close!"
		    });
		this.value = ""; // Clear the file input
		$(".recieptthumb").attr("src", ""); // Clear the thumbnail image
		return;
	}
	if (fileSize > maxSize) {
		// File size exceeds the limit
		Swal.fire({
			title: "Error uploading file",
			text: "File size exceeds the maximum limit of 2MB. Please choose a smaller file.",
			icon: "error",
			confirmButtonColor: '#0069d9',
			confirmButtonText: "Close!"
		  });
		this.value = ""; // Clear the file input
		$(".recieptthumb").attr("src", ""); // Clear the thumbnail image
		return;
	}

	var fileReader = new FileReader();

	fileReader.onload = function(event) {
		var fileType = file.type;
		var imagePath;

		if (fileType === "application/pdf") {
			// Set PDF icon as image
			imagePath = "views/expenses/defaults/pdf.png";
		} else {
			// Set file data URL as image
			imagePath = event.target.result;
		}

		$(".recieptthumb").attr("src", imagePath);
	};

	fileReader.readAsDataURL(file);
});

$(".editReceipt").change(function(){
	var file = this.files[0];
	var fileSize = file.size; // File size in bytes
	var maxSize = 2 * 1024 * 1024; // 2MB in bytes
	var validFileTypes = ["application/pdf", "image/jpeg", "image/png", "image/gif"];
	var fileType = file.type;
	// Check if the file type is valid
	if (!validFileTypes.includes(fileType)) {
		// Invalid file type 
		Swal.fire({
		      title: "Error uploading image",
		      text: "Invalid file type. Please choose a PDF or an image file.",
		      icon: "error",
			  confirmButtonColor: '#0069d9',
		      confirmButtonText: "Close!"
		    });
		this.value = ""; // Clear the file input
		$(".editrecieptthumb").attr("src", ""); // Clear the thumbnail image
		return;
	}
	if (fileSize > maxSize) {
		// File size exceeds the limit
		Swal.fire({
			title: "Error uploading file",
			text: "File size exceeds the maximum limit of 2MB. Please choose a smaller file.",
			icon: "error",
			confirmButtonColor: '#0069d9',
			confirmButtonText: "Close!"
		  });
		this.value = ""; // Clear the file input
		$(".editrecieptthumb").attr("src", ""); // Clear the thumbnail image
		return;
	}

	var fileReader = new FileReader();

	fileReader.onload = function(event) {
		var fileType = file.type;
		var imagePath;

		if (fileType === "application/pdf") {
			// Set PDF icon as image
			imagePath = "views/expenses/defaults/pdf.png";
		} else {
			// Set file data URL as image
			imagePath = event.target.result;
		}

		$(".editrecieptthumb").attr("src", imagePath);
	};

	fileReader.readAsDataURL(file);
});

/*=============================================
EDIT EXPENSES
=============================================*/
$(".tables").on("click", "button.btnEditExpense", function(){
	var expenseId = $(this).attr("expenseId");
  
	var data = new FormData();
	data.append("expenseId", expenseId);
  
	$.ajax({
		url: "ajax/expenses.ajax.php",
		method: "POST",
		data: data,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(answer) {
			console.log("answer", answer);
			$("#editExpenseId").val(answer["id"]);
			$("#editExpense").val(answer["expense"]);
			$("#editDate").val(answer["date"]);
			$("#editAmount").val(answer["amount"]);
			$("#editExpenseType").val(answer["expense_type"]);
		
			if (answer["receipt"].toLowerCase().endsWith(".pdf")) {
				$(".editrecieptthumb").attr("src", "views/expenses/defaults/pdf.png");
			} else {
				$(".editrecieptthumb").attr("src", answer["receipt"]);
			}
		
			$("input[name='existingFilePath']").val(answer["receipt"]); // Set existing file path
		}, error: function() {
			Swal.fire("Error", "Failed to retrieve expense data from the server.", "error");
		}

	});

});
  
  

/*=============================================
DELETE PRODUCT
=============================================*/

$(".tables tbody").on("click", "button.btnDeleteExpense", function(){

	var expenseId = $(this).attr("expenseId");
	Swal.fire({

		title: 'Are you sure you want to delete the expense?',
		text: "If you're not sure you can cancel this action!",
		icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0069d9',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Yes, delete expense!'
        }).then(function(result){
        if (result.value) {

        	window.location = "index.php?route=expenses&deleteExpenseId=" + expenseId;
        }

	})

})
