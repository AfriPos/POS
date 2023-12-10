/*=============================================
EDIT CATEGORY
=============================================*/

$(".tables").on("click", ".btnEdittax", function(){

	var idtax = $(this).attr("taxId");

	var datum = new FormData();
	datum.append("idtax", idtax);

	$.ajax({
		url: "ajax/tax.ajax.php",
		method: "POST",
      	data: datum,
      	cache: false,
     	contentType: false,
     	processData: false,
     	dataType:"json",
     	success: function(answer){
     		
     		// console.log("answer", answer);

     		 $("#actualtaxId").val(answer["taxId"]);
     		 $("#editVAT").val(answer["VAT"]);
     		 $("#editdiscount").val(answer["VATName"]);

     	}, error: function() {
			Swal.fire("Error", "Failed to retrieve tax data from the server.", "error");
		}

	})

})

/*=============================================
DELETE CATEGORY
=============================================*/
$(".tables").on("click", ".btnDeletetax", function(){

	 var idtax = $(this).attr("taxId");

	 Swal.fire({
	 	title: 'Are you sure you want to delete the Tax?',
		text: "If you're not sure you can cancel!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#0069d9',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancel',
		confirmButtonText: 'Yes, delete Tax!'
	 }).then(function(result){

	 	if(result.value){

	 		window.location = "index.php?route=tax&idtax="+idtax;

	 	}

	 })

})