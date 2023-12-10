/*=============================================
LOAD DYNAMIC PRODUCTS TABLE
=============================================*/


$('.productsTable').DataTable({
	"ajax": "ajax/datatable-products.ajax.php", 
	"deferRender": true,
	"retrieve": true,
	"processing": true,
	"responsive": true,
});

/*=============================================
UPLOADING PRODUCT IMAGE
=============================================*/

$(".txtproductimage").change(function(){

	var image = this.files[0];
	
	/*=============================================
  	WE VALIDATE THAT THE FORMAT IS JPG OR PNG
  	=============================================*/

  	if(image["type"] != "image/jpeg" && image["type"] != "image/png"){

  		$(".txtproductimage").val("");

  		 Swal.fire({
		      title: "Error uploading image",
		      text: "The image should be in JPG or PNG format!",
		      icon: "error",
			  confirmButtonColor: '#0069d9',
		      confirmButtonText: "Close!"
		    });

  	}else if(image["size"] > 2000000){

  		$(".txtproductimage").val("");

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

  			$(".preview").attr("src", imagePath);

  		})

  	}
});

/*=============================================
EDIT PRODUCT
=============================================*/
$(".tables tbody").on("click", "button.btnEditProduct", function(){

	var barcodeProduct = $(this).attr("idProduct");
	
	var datum = new FormData();
    datum.append("barcodeProduct", barcodeProduct);

     $.ajax({

		url:"ajax/products.ajax.php",
		method: "POST",
		data: datum,
		cache: false,
		contentType: false,
		processData: false,
		dataType:"json",
		success:function(answer){
			var categoryData = new FormData();
			categoryData.append("idCategory",answer["idCategory"]);


			$.ajax({

				url:"ajax/categories.ajax.php",
				method: "POST",
				data: categoryData,
				cache: false,
				contentType: false,
				processData: false,
				dataType:"json",
				success:function(answer){

					// console.log(answer);
					
					$("#editcategory").val(answer["id"]);
					$("#editcategory").html(answer["Category"]);

				}, error: function() {
					Swal.fire("Error", "Failed to retrieve category data from the server.", "error");
				}

			})

			$("#editbarcode").val(answer["barcode"]);

			$("#editproductname").val(answer["product"]);

			$("#editcategory").val(answer["Category"]);

			$("#editdescription").val(answer["description"]);

			$("#editstock").val(answer["stock"]);

			$("#editpurchaseprice").val(answer["purchaseprice"]);

			$("#editsaleprice").val(answer["saleprice"]);

			$("#edittaxcat").val(answer["taxId"]);

			if(answer["image"] != ""){

				$("#currentImage").val(answer["image"]);

				$(".preview").attr("src",  answer["image"]);

			}

		}, error: function() {
			Swal.fire("Error", "Failed to retrieve product data from the server.", "error");
		}

  	})

})

/*=============================================
DELETE PRODUCT
=============================================*/

$(".tables tbody").on("click", "button.btnDeleteProduct", function(){

	var barcodeProduct = $(this).attr("idProduct");
	// var code = $(this).attr("code");
	var image = $(this).attr("image");
	
	Swal.fire({

		title: 'Are you sure you want to delete the product?',
		text: "If you're not sure you can cancel this action!",
		icon: 'warning',
        showCancelButton: true,
		confirmButtonColor: '#0069d9',
		cancelButtonColor: '#d33',
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Yes, delete product!'
        }).then(function(result){
        if (result.value) {

        	window.location = "index.php?route=products&product-id="+barcodeProduct+"&image="+image;

        }

	})

})

/*=============================================
VIEW PRODUCT
=============================================*/

$(".tables tbody").on("click", "button.btnViewProduct", function(){
	
	var image = $(this).attr("image");
	var barcodeProduct = $(this).attr("idProduct");

	var datum = new FormData();
	datum.append("barcodeProduct", barcodeProduct);
	
	$.ajax({

		url:"ajax/products.ajax.php",
		method: "POST",
		data: datum,
		cache: false,
		contentType: false,
		processData: false,
		dataType:"json",
		success:function(answer){
			
			var datum = new FormData();
			datum.append("barcodeProduct", barcodeProduct);
			datum.append("productname", answer.product);


			$.ajax({
				url: 'ajax/activitylog.ajax.php',
				method: "POST",
				data: datum,
				cache: false,
				contentType: false,
				processData: false,
				dataType:"json",
				success: function(response) {
					window.location = "index.php?route=viewproduct&product-id=" + barcodeProduct + "&image=" + image;
				}, error: function() {
					Swal.fire("Error", "Failed to update logs to the server.", "error");
				}

			});

		}, error: function() {
			Swal.fire("Error", "Failed to retrieve product data from the server.", "error");
		}

	});

});

$(".tables tbody").on("click", "button.btnPrintProductBarcode", function(){

	var barcodeProduct = $(this).attr("idProduct");
	// var code = $(this).attr("code");
	var image = $(this).attr("image");

        	window.location = "index.php?route=printbarcode&product-id="+barcodeProduct+"&image="+image;

})




/*==================================================
CHECK FOR CHANGE IN THE SELECT PRODUCT MENU TO ADD STOCK
====================================================*/
$('.productsdrop').change(function() {

	var selectedProduct = $('.productsdrop option:selected').text();
	$('#sproduct').val(selectedProduct);

	
	var barcode = $('.productsdrop').val();

	// Fetch the product data
	$.ajax({
		url: 'ajax/products.ajax.php',
		method: 'POST',
		data: { barcodeProduct: barcode },
		dataType: 'json',
		success: function(response) {
			// Handle the product data response
			// console.log(response);  // You can inspect the response object in the browser's console
			// Update the necessary fields with the received data
			// Example:
				// Set the product image
				$('#productImage').attr('src', response.image);
				$("#cstock").val(response.stock);
				if (response.hasexpiry == 1) {
					// Product has expiry, show manufacturing and expiry date fields
					$("#mdate, #edate, #snumber").closest(".form-group").show();
					$("#addProductBtn").show();
				} else {
					// Product does not have expiry, hide manufacturing and expiry date fields
					$("#mdate, #edate, #snumber").closest(".form-group").hide();
					// Hide the "Add Product" button
					$("#addProductBtn").hide();
				}
		}, error: function() {
			Swal.fire("Error", "Failed to retrieve product data from the server.", "error");
		}

	});

});


var data = [];
function addStockProduct() {
    var serialNumber = $("#snumber").val();
    var manufacturingDate = $("#mdate").val();
    var expiryDate = $("#edate").val();
    selectedQuantity = parseInt($("#orderproducts option:selected").data("quantity"));


    // Check if any of the required fields is empty
    if (!manufacturingDate || !expiryDate || !serialNumber) {
        // Display an error message or handle it as needed
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please fill in all required fields',
        });
        return;
    } else{
		// Get product data from the form
		const mdate = document.getElementById('mdate').value;
		const edate = document.getElementById('edate').value;
		const snumber = document.getElementById('snumber').value;

		// Create a new product entry
		const productEntry = document.createElement('div');
		productEntry.innerHTML = `<p>Serial Number: ${snumber}, Manufacturing Date: ${mdate}, Expiry Date: ${edate}</p>`;
		
		data.push({"sNumber":snumber, "mfDate":mdate, "exDate":edate})
		const updatedArrayJSON = JSON.stringify(data);
		$('#productList').val(updatedArrayJSON);
		console.log(data);

		// Append the product entry to the product list
		document.getElementById('shownproducts').appendChild(productEntry);

		// Clear the input fields
		document.getElementById('mdate').value = '';
		document.getElementById('edate').value = '';
		document.getElementById('snumber').value = '';

    }

}

// check if the product exists in the db
// $('#txtbarcode').change(function() {

// 	var item = "barcode"
// 	var order = "id"
// 	var value = $(this).val();
	
// 	var datum = new FormData();
//     datum.append("item", item);
//     datum.append("order", order);
//     datum.append("value", value);

// 		$.ajax({

// 		url:"ajax/products.ajax.php",
// 		method: "POST",
// 		data: datum,
// 		cache: false,
// 		contentType: false,
// 		processData: false,
// 		dataType:"json",
// 		success:function(answer){
// 			if (answer) {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Product already exists!',
//                     showConfirmButton: false,
//                     timer: 2000, // 2 seconds
//                     willClose: function() {
//                         $('#txtbarcode').val(''); // Reset the text field
//                     }
//                 });
// 			}
// 		}

//   	})

// });