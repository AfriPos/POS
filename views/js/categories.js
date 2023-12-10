/*=============================================
EDIT CATEGORY
=============================================*/

$(".tables").on("click", ".btnEditCategory", function(){

	var idCategory = $(this).attr("idCategory");

	var datum = new FormData();
	datum.append("idCategory", idCategory);

	$.ajax({
		url: "ajax/categories.ajax.php",
		method: "POST",
      	data: datum,
      	cache: false,
     	contentType: false,
     	processData: false,
     	dataType:"json",
     	success: function(answer){
     		
     		console.log("answer", answer);

			$("#editCategory").val(answer["Category"]);
			$("#idCategory").val(answer["id"]);

     	}, error: function() {
			Swal.fire("Error", "Failed to retrieve category data from the server.", "error");
		}

	})

})

/*=============================================
DELETE CATEGORY
=============================================*/
$(".tables").on("click", ".btnDeleteCategory", function(){

	 var idCategory = $(this).attr("idCategory");

	 Swal.fire({
	 	title: 'Are you sure you want to delete the category?',
		text: "If you're not sure you can cancel!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#0069d9',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancel',
		confirmButtonText: 'Yes, delete category!'
	 }).then(function(result){

	 	if(result.value){

	 		window.location = "index.php?route=category&idCategory="+idCategory;

	 	}

	 })

})
/*=============================================
ASK FOR CATEGORY DELETION
=============================================*/

$(".tables").on("click", ".askDeleteCategory", function(){

	var idCategory = $(this).attr("idCategory");

	var datum = new FormData();
	datum.append("idCategory", idCategory);

	$.ajax({
		url: "ajax/categories.ajax.php",
		method: "POST",
      	data: datum,
      	cache: false,
     	contentType: false,
     	processData: false,
     	dataType:"json",
     	success: function(answer){

			$("#id").val(answer["id"]);

     	}, error: function() {
			Swal.fire("Error", "Failed to retrieve category data from the server.", "error");
		}

	})

})