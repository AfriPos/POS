var orderarr = [];
var products = [];

// When the "Add to list" button is clicked
$('#product').on('change', function() {
    var barcodeProduct = $('#product').val(); // Get the selected product ID
  
    var datum = new FormData();
    datum.append("barcodeProduct", barcodeProduct);
  
    $.ajax({
        url: "ajax/products.ajax.php",
        method: "POST",
        data: datum,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(answer) {
            if (answer && answer["id"]) {
                if (jQuery.inArray(answer["id"], orderarr) != -1) {

                    // Existing order
                    var actualqty = parseInt($('#qty_id' + answer["id"]).val()) + 1;
                    $('#qty_id' + answer["id"]).val(actualqty).trigger('change');
                    $('#product').val('');
                    updateProducts();
                    calculateTotal()

                }else{

                    addRow(answer["id"], answer["product"], answer["purchaseprice"]);
                    orderarr.push(answer["id"]);
                    $('#product').val('');
                    
                    function addRow(id, product, purchaseprice) {
                        
                        var qty = 1; // Default quantity
                        products.push({"id":id, "productName":product, "Quantity":qty, "Price":purchaseprice})
                        updateProducts();

                        var tr ='<tr>' +
                                    '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-dark">' + product + '</span></td>' +
                                    '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id' + id + '">' + purchaseprice + '</span></td>' +
                                    '<td><input type="number" class="form-control qty" min="1" name="quantity_arr[]" id="qty_id' + id + '" value="' + 1 + '" size="1"></td>' +
                                    '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-danger totalamt" name="netamt_arr[]" id="purchaseprice_id' + id + '">' + purchaseprice + '</span><input type="hidden" class="form-control purchaseprice" name="purchaseprice_c_arr[]" id="purchaseprice_idd' + id + '" value="' + purchaseprice + '"></td>' +
                                    '<td style="text-align:left; vertical-align:middle;"><center><name="remove" class="btnremove" data-id="' + id + '"><span class="fas fa-trash" style="color:red;"></span></center></td>' +
                                '</tr>';
                    
                        $('.orders').append(tr);
                        calculateTotal()
                    }

                }

            }else{
                Swal.fire({
                    icon: "warning",
                    title: "Select a product to add it to the order list.",
                    showConfirmButton: false,
                    timer: 2000 // Set the timer for 2 seconds (2000 milliseconds)
                }).then(function () {
                    // Redirect to "orders" page after the timer expires
                    $('#product').val('');
                });
            }

        }, error: function() {
			Swal.fire("Error", "Failed to retrieve product data from the server.", "error");
		}

    });

});

// Removing a product from the order list
$(document).on('click', '.btnremove', function() {
    var id = $(this).data('id'); // Get the product ID from the data attribute
  
    // Remove the product ID from the orderarr array
    var index = orderarr.indexOf(id);
    if (index !== -1) {
      orderarr.splice(index, 1);
    }
  
    // Remove the product details from the products array
    products = products.filter(function(product) {
      return product.id !== id;
    });
  
    // Remove the row from the table
    $(this).closest('tr').remove();
  
    // Update the products input field
    updateProducts();
  
    // Recalculate the total price
    calculateTotal();
});

// increase or change the quantity of a product in the order list 
$(document).on('keyup change', '.qty', function() {
    var quantity = $(this).val();
    var tr = $(this).closest('tr');
    var id = tr.find('.btnremove').data('id');
  
    // Find the index of the object with a specific id
    const productIndex = products.findIndex(obj => obj.id === id);
  
    // Check if the object with the specified id exists in the array
    if (productIndex !== -1) {
      // Update the Quantity property
      products[productIndex].Quantity = parseInt(quantity);
    }
  
    updateProducts();
  
    tr.find('.totalamt').text(quantity * tr.find('.price').text());
    tr.find('.saleprice').val(quantity * tr.find('.price').text());
    calculateTotal();
});
  
// Function to update the array and set it to the productsList input
function updateProducts() {
    const updatedArrayJSON = JSON.stringify(products);
    $('#products').val(updatedArrayJSON);
}

// calculate totals for all the products on the list
function calculateTotal() {
    total = 0;

    $(".orders tr").each(function() {
        var tr = $(this);
        var price = parseFloat(tr.find(".price").text());
        var quantity = parseInt(tr.find(".qty").val());
        total += price * quantity;
    });

    $('#total').val(total.toFixed(2));
}

$(document).on('click', '#recieveorder', function() {
    var orderid = $(this).attr("order-id");

    // Use SweetAlert to prompt the user for data collection preference
    Swal.fire({
        title: 'Select Data Collection Type',
        html: 'Choose the type of order delivery data collection:<br/><br/>' +
              '<b>Advanced:</b> Collect detailed information about the delivery <b>(</b> recodring products individualy with serial numbers <b>)</b>.<br/>' +
              '<b>Basic:</b> Collect minimal information for the delivery.',
        showCancelButton: true,
        confirmButtonText: 'Advanced',
        cancelButtonText: 'Basic',
    }).then((result) => {
        if (result.isConfirmed) {
            // User chose Advanced, redirect to the advanced route
            window.location = "index.php?route=order-delivery&order-id=" + orderid + "&data-type=advanced";
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // User chose Basic, redirect to the basic route
            window.location = "index.php?route=order-delivery&order-id=" + orderid + "&data-type=basic";
        }
    });
});

// Initialize an array to store selected products
var selectedProducts = [];
var selectedQuantity = 0;
function addProduct() {
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
        // Check if adding this product will exceed the total quantity from the order
        var totalQuantityInArray = selectedProducts.length;

        if (totalQuantityInArray == selectedQuantity) {
            // Display a SweetAlert error message
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Adding this product exceeds the total quantity for this product specified in the order.',
            });
            return;
        } else{
            // Add the product to the array
            selectedProducts.push({
                manufacturingDate: manufacturingDate,
                expiryDate: expiryDate,
                serialNumber: serialNumber,
            });

        }

    }


    
    // Get product data from the form
    const mdate = document.getElementById('mdate').value;
    const edate = document.getElementById('edate').value;
    const snumber = document.getElementById('snumber').value;

    // Create a new product entry
    const productEntry = document.createElement('div');
    productEntry.innerHTML = `<p>Serial Number: ${snumber}Manufacturing Date: ${mdate}, Expiry Date: ${edate}</p>`;

    // Append the product entry to the product list
    document.getElementById('productList').appendChild(productEntry);

    // Clear the input fields
    document.getElementById('mdate').value = '';
    document.getElementById('edate').value = '';
    document.getElementById('snumber').value = '';
}
const urlParams = new URLSearchParams(window.location.search);
const orderId = urlParams.get("order-id");
// function to create a batch 
function createBatch(){
    var inputquantity = ($("#bquantity").val() !== null && $("#bquantity").val() > 0) ? $("#bquantity").val() : (selectedProducts.length !== null && selectedProducts.length > 0) ? selectedProducts.length : $("#bquantity").val();
    console.log(inputquantity);
    console.log(selectedProducts.length);
    console.log($("#bquantity").val());
    var productId = $("#orderproducts").val();
    var data = new FormData();
    data.append("orderid", orderId);
    data.append("ProductId", productId);
  
    $.ajax({
        url: "ajax/order.ajax.php",
        method: "POST",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
            for (var i = 0; i < response.length; i++) {
                var entry = response[i];
                if (entry['product_id'] === productId) {
                    var quantity = entry['quantity'];
                    if (inputquantity != quantity) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'The quantity does not match the quantity in the order for this product, please check and try again.',
                        }).then(function() {
                            // After the alert is closed, clear the field
                            $("#bquantity").val("");
                        });
                    } else{
                        // console.log("insert");
                        // Check if adding this product will exceed the total quantity from the order
                        var totalQuantityInArray = selectedProducts.length;

                        if (selectedQuantity < totalQuantityInArray) {
                            // Display a SweetAlert error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'The quantity of added products is less than the order quantity.',
                            });
                            return;

                        } else{
                            // Generate a unique ID using a combination of timestamp and a random number
                            var timestamp = new Date().getTime();
                            var randomNum = Math.floor(Math.random() * 9000) + 1000; // Random number between 1000 and 9999
                            var batchId = timestamp + '-' + randomNum;// Convert the array to a JSON string
                            var batchItemsJson = JSON.stringify(selectedProducts);
                            // var productId = $("#orderproducts").val();
                            var quantity = $("#bquantity").val();

                            var file = "products";
                            var variable = "productId"
                            var value = productId;
                            customajaxRequest(file, variable, value, function(response) {
                                if (response.hasexpiry != 1 || datatype == "basic") {
                                    
                                    // Create FormData and append the batchId and batchItems
                                    var data = new FormData();
                                    data.append("batchId", batchId);
                                    data.append("quantity", quantity); 
                                    data.append("OrderId", orderId);
                                    data.append("productId", productId);
                                
                                    $.ajax({
                                        url: "ajax/order.ajax.php", // Replace with your actual backend endpoint
                                        method: "POST",
                                        data: data,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        dataType: "json",
                                        success: function (response) {
                                            if (response.status === 'success') {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success',
                                                    text: response.message,
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: response.message,
                                                });
                                            }
                                        },
                                        error: function (error) {
                                            console.error(":", error);
                                            // Swal.fire({
                                            //     icon: 'error',
                                            //     title: 'Error',
                                            //     text: 'Error creating batch.',
                                            // });
                                        },
                                    });

                                    
                                } else {// Create FormData and append the batchId and batchItems
                                    var data = new FormData();
                                    data.append("batchId", batchId);
                                    data.append("batchItems", batchItemsJson); 
                                    data.append("quantity", selectedQuantity); 
                                    data.append("OrderId", orderId);
                                    data.append("productId", productId);
                                
                                    $.ajax({
                                        url: "ajax/order.ajax.php", // Replace with your actual backend endpoint
                                        method: "POST",
                                        data: data,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        dataType: "json",
                                        success: function (response) {
                                            if (response.status === 'success') {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success',
                                                    text: response.message,
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: response.message,
                                                });
                                            }
                                        },
                                        error: function (error) {
                                            console.error(":", error);
                                            // Swal.fire({
                                            //     icon: 'error',
                                            //     title: 'Error',
                                            //     text: 'Error creating batch.',
                                            // });
                                        },
                                    });
                                }
                            });
                            
                        }
                        // Reset (clear) the list
                        selectedProducts.empty()
                        // Clear the product list
                        document.getElementById('productList').innerHTML = '';

                    }
                    break; // Exit the loop once you find the match
                }
            }
        },
        error: function (error) {
            console.error("Error :", error);
        },
    });

    
}

// Function to fetch and populate product names in a select element based on order ID
function fetchAndPopulateProductNames(orderId) {
    var data = new FormData();
    data.append("orderId", orderId);
  
    $.ajax({
        url: "ajax/order.ajax.php", // Replace with your actual backend endpoint
        method: "POST",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (productNames) {
            const selectElement = $("#orderproducts");

            // Clear existing options
            selectElement.empty();

            // Add a default option
            selectElement.append('<option selected>Select a product</option>');

            // Assuming the productNames array is directly returned from the AJAX response
            productNames.forEach((product) => {
                selectElement.append(`<option value="${product.product_id}" data-quantity="${product.quantity}">${product.product}</option>`);
            });

        },
        error: function (error) {
            console.error("Error fetching product names:", error);
        },
    });
}
  
// Call fetchAndPopulateProductNames function with the order ID from the URL


if (orderId) {
    fetchAndPopulateProductNames(orderId);
}
function customajaxRequest(file, variable, value, callback){
    var data = new FormData();
    data.append(variable, value);

    $.ajax({
        url: `ajax/${file}.ajax.php`,
        method: "POST",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {
            callback(data); // Invoke the callback with the retrieved data
        } ,error: function(xhr, status, error) {
            console.error("AJAX request error:", error);
        }
    });
}
// Assuming your select element has an ID of "floatingSelect"
const datatype = urlParams.get("data-type");
if (DCM != "basic") {
    $("#orderproducts").change(function () {
        
        var productId = $(this).val();
        var file = "products";
        var variable = "productId"
        var value = productId;
        customajaxRequest(file, variable, value, function(response) {

            if (response.hasexpiry == 1) {
                // Product has expiry, show manufacturing and expiry date fields
                $("#mdate, #edate, #snumber").closest(".form-group").show();
                $("#bquantity").closest(".form-group").hide();
                $("#addProductBtn").show();
            } else {
                // Product does not have expiry, hide manufacturing and expiry date fields
                $("#mdate, #edate, #snumber").closest(".form-group").hide();
                $("#bquantity").closest(".form-group").show();
                // Hide the "Add Product" button
                $("#addProductBtn").hide();
            }

        });
        
    });
  
}


// validates the serial number no duplicates
$(document).on("input", "#snumber", function() {
    var serialnumber = $(this).val();
    var file = "order";
    var variable = "serialNumber"
    var value = serialnumber;
    customajaxRequest(file, variable, value, function(response) {
        serialnumber = response[0]['serialNumber'] 
        if (serialnumber && serialnumber == serialnumber) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'This serial number exists, please check and try again.',
            }).then(function() {
                // After the alert is closed, clear the field
                $("#snumber").val("");
            });
        }
    });
});