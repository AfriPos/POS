/*=============================================
EDIT CUSTOMER
=============================================*/

$(".tables").on("click", ".btnEditcustomer", function(){

	var customerid = $(this).attr("customerid");

	var datum = new FormData();
	datum.append("customerid", customerid);

	$.ajax({
		url: "ajax/customer.ajax.php",
		method: "POST",
      	data: datum,
      	cache: false,
     	contentType: false,
     	processData: false,
     	dataType:"json",
     	success: function(answer){
     		
     		console.log("answer", answer);

            $("#editcustomerId").val(answer.customer_id);
			$("#editcustomerName").val(answer.name);
			$("#editcustomerAddress").val(answer.address);
			$("#editcontactNumber").val(answer.phone);
			$("#editcustomerEmail").val(answer.email);

     	}, error: function() {
			Swal.fire("Error", "Failed to retrieve customer data from the server.", "error");
		}

	})

})