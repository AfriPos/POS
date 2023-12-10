$(".storeLogo").change(function(){

	var image = this.files[0];
	
	/*=============================================
  	WE VALIDATE THAT THE FORMAT IS JPG OR PNG
  	=============================================*/

  	if(image["type"] != "image/jpeg" && image["type"] != "image/png"){

  		$(".storeLogo").val("");

  		 Swal.fire({
		      title: "Error uploading image",
		      text: "The image should be in JPG or PNG format!",
		      icon: "error",
			  confirmButtonColor: '#0069d9',
		      confirmButtonText: "Close!"
		    });

  	}else if(image["size"] > 2000000){

  		$(".storeLogo").val("");

  		 Swal.fire({
		      title: "Error uploading image",
		      text: "The image shouldn't be more than 2MB!",
		      icon: "error",
			  confirmButtonColor: '#0069d9',
		      confirmButtonText: "Close!"
		    });

  	}else{

  		var imageData = new FileReader;
  		imageData.readAsDataURL(image);

  		$(imageData).on("load", function(event){

  			var imagePath = event.target.result;

  			$(".storeLogopreview").attr("src", imagePath);

  		})

  	}
});



/*=============================================
EDIT STORE
=============================================*/

$(".tables tbody").on("click", "button.btnEditStore", function(){

	var Storeid = $(this).attr("Storeid");
	
	var datum = new FormData();
    datum.append("Storeid", Storeid);

	$.ajax({

		url:"ajax/stores.ajax.php",
		method: "POST",
		data: datum,
		cache: false,
		contentType: false,
		processData: false,
		dataType:"json",
		success:function(answer){

			$("#editstoreId").val(answer["store_id"]);
	
			$("#editstoreName").val(answer["store_name"]);
	
			$("#editstoreAddress").val(answer["store_address"]);
	
			$("#editcontactNumber").val(answer["contact_number"]);
	
			$("#editstoreEmail").val(answer["email"]);
	
			$("#editstoreManager").val(answer["store_manager"]);
	
			$("#editopeningTime").val(answer["opening"]);
	
			$("#editclosingTime").val(answer["closing"]);
	
			if(answer["logo"] != ""){
	
				  $("#currentImage").val(answer["logo"]);
	
				  $(".storeLogopreview").attr("src",  answer["logo"]);
	
			}
			
		}, error: function() {
			Swal.fire("Error", "Failed to retrieve store data from the server.", "error");
		}

	});

});

/*=============================================
DELETE PRODUCT
=============================================*/

$(".tables tbody").on("click", "button.btnDeleteStore", function(){

	var Storeid = $(this).attr("Storeid");
	// var code = $(this).attr("code");
	var image = $(this).attr("image");
	
	Swal.fire({

		title: 'Are you sure you want to delete the store?',
		text: "If you're not sure you can cancel this action!",
		icon: 'warning',
        showCancelButton: true,
		confirmButtonColor: '#0069d9',
		cancelButtonColor: '#d33',
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Yes, delete store!'
        }).then(function(result){
        if (result.value) {

        	window.location = "index.php?route=manage-stores&id="+Storeid+"&image="+image;

        }

	})

})

var openingTimeInput = document.getElementById('openingTime');
var closingTimeInput = document.getElementById('closingTime');

openingTimeInput.addEventListener('change', validateTime);
closingTimeInput.addEventListener('change', validateTime);

function validateTime() {
  var openingTime = openingTimeInput.value;
  var closingTime = closingTimeInput.value;

	if (openingTime && closingTime) {
		if (openingTime >= closingTime) {
		Swal.fire({
			icon: 'error',
			title: 'Invalid Time',
			text: 'Opening time cannot be later than closing time.',
			timer: 2000,
			showConfirmButton: false
		});
		// Clear the invalid value
		this.value = '';
		}
	}
}
