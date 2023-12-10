var productarr = [];
var productinfo = [];

$(function() {
  $('#scanbarcode').on('change', function() {
    var barcode = $('#scanbarcode').val();

    $.ajax({
      url: 'ajax/pos.ajax.php',
      method: 'get',
      dataType: 'json',
      data: { barcode: barcode },
      success: function(data) {
        if (data && data["id"]) {
          if (jQuery.inArray(data["id"], productarr) != -1) {
            // Existing product
            var actualqty = parseInt($('#qty_id' + data["id"]).val()) + 1;
            $('#qty_id' + data["id"]).val(actualqty).trigger('change');
            updateArray();

            var saleprice = parseInt(actualqty) * data["saleprice"];
            $('#saleprice_id' + data["id"]).text(saleprice);
            $("#scanbarcode").val("");
            calculateSubtotal();

          } else {
            // New product
            if (data["stock"] > 0) {
              addRow(data["id"], data["product"], data["saleprice"], data["stock"], data["taxId"], data["barcode"]);
              productarr.push(data["id"]);
              $("#scanbarcode").val("");
            } else {
              Swal.fire("Out of Stock", "The product is out of stock.", "warning");
              $("#scanbarcode").val("");
            }
            function addRow(id, product, saleprice, stock, taxId, barcode) {


              var datum = new FormData();
              datum.append("productid", id);

              // Make the AJAX request
              $.ajax({
                url: 'ajax/discount.ajax.php',
                method: "POST",
                data: datum,
                cache: false,
                contentType: false,
                processData: false,
                dataType:"json",
                success: function(data) {
                  console.log(data);
                  // var status = data["status"];
                  var start = new Date(data["startdate"]);
                  var end = new Date(data["enddate"]);
                  var current = new Date();

                  // Validate the discount status
                  if (current >= start && current <= end) {
                    var discount = data["amount"] ? data["amount"] : 0;
                  }
                  else{
                    var discount = 0;
                  }
                  
                  var qty = 1; // Default quantity

                  productinfo.push({"id":id, "productName":product, "Quantity":qty, "salePrice":saleprice, "Discount":discount})
                  updateArray();


                  var tr = '<tr>' +
                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-dark">' + product + '</span><input type="hidden" class="form-control product_c" name="productarr[]" value="' + product + '"><input type="hidden" class="form-control idtax" name="tax_arr[]" value="' + taxId + '"><input type="hidden" class="form-control barcode" name="barcode_arr[]" value="' + barcode + '"></td>' +
                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary stocklbl" name="stock_arr[]" id="stock_id' + id + '">' + stock + '</span><input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd' + id + '" value="' + stock + '"></td>' +
                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id' + id + '">' + saleprice + '</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd' + id + '" value="' + saleprice + '"></td>' +

                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning discount" name="discount_arr[]" id="discount_id' + id + '">' + discount + '</span><input type="hidden" class="form-control discount_c" name="discount_c_arr[]" id="discount_idd' + id + '" value="' + discount + '"></td>' +

                  '<td><input type="number" class="form-control qty" min="1" name="quantity_arr[]" id="qty_id' + id + '" value="' + 1 + '" size="1"></td>' +
                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-danger totalamt" name="netamt_arr[]" id="saleprice_id' + id + '">' + saleprice + '</span><input type="hidden" class="form-control saleprice" name="saleprice_c_arr[]" id="saleprice_idd' + id + '" value="' + saleprice + '"></td>' +
              
                  //remove item button
              
                  '<td style="text-align:left; vertical-align:middle;"><center><name="remove" class="btnremove" data-id="' + id + '"><span class="fas fa-trash" style="color:red;"></span></center></td>' +
                  '</tr>';
              
                $('.details').append(tr);
              
                calculateSubtotal();

                }
              });
            
            }
          }
        } else {
          Swal.fire("Error", "The product does not exist in the database.", "error");
          $("#scanbarcode").val("");
        }
      } ,error: function(xhr, status, error) {
      console.error("AJAX request error:", error);
    }
    });

  });

});

$(document).on('click', '.btnremove', function() {
  var removed = $(this).attr("data-id")
  productarr = jQuery.grep(productarr, function(value){
    return value != removed;
  })

  // Remove the corresponding sub-array from productinfo
  productinfo = productinfo.filter(function(item) {
    return item.id !== removed;
  });

  $(this).closest('tr').remove();
  
  console.log(productinfo);

  updateArray();
  calculateSubtotal();
});

$(document).on("keyup change", ".qty", function() {
  var quantity = $(this).val();
  var tr = $(this).closest("tr");
  var stock = parseInt(tr.find(".stock_c").val());
  var id = tr.find(".btnremove").data("id");

  // Assume the dynamically created array is stored in the variable 'productArray'

  // Find the index of the object with a specific id
  const productId = id; // Integer value
  const productIndex = productinfo.findIndex(obj => obj.id === productId.toString());

  // Check if the object with the specified id exists in the array
  if (productIndex !== -1) {
    // Access and update the Quantity property
    productinfo[productIndex].Quantity = quantity;

  }
  
  updateArray();
  calculateSubtotal()


  if (quantity > stock) {
    Swal.fire("WARNING", "You only have " + stock + " units left", "warning");
    $(this).val(1);
  }

  // tr.find(".totalamt").text(quantity * tr.find(".price").text());
  // tr.find(".saleprice").val(quantity * tr.find(".price").text());
  // calculateSubtotal();
});


var productarr = [];
$(function() {
  $('#pos-select').on('change', function() {
    var barcode = $('#pos-select').val();


    $.ajax({
      url: 'ajax/pos.ajax.php',
      method: 'get',
      dataType: 'json',
      data: { barcode: barcode },
      success: function(data) {
        if (data && data["id"]) {
          if (jQuery.inArray(data["id"], productarr) != -1) {
            // Existing product
            var actualqty = parseInt($('#qty_id' + data["id"]).val()) + 1;
            $('#qty_id' + data["id"]).val(actualqty).trigger('change');
            updateArray();
            
            var saleprice = parseInt(actualqty) * data["saleprice"];
            $('#saleprice_id' + data["id"]).text(saleprice);
            $("#pos-select").val("");
            calculateSubtotal();

          } else {
             // New product
             if (data["stock"] > 0) {
              addRow(data["id"], data["product"], data["saleprice"], data["stock"], data["taxId"], data["barcode"]);
              productarr.push(data["id"]);
              $("#pos-select").val("");
            } else {
              Swal.fire("Out of Stock", "The product is out of stock.", "warning");
              $("#pos-select").val("");
            }
            function addRow(id, product, saleprice, stock, taxId, barcode) {


              var datum = new FormData();
              datum.append("productid", id);

              // Make the AJAX request
              $.ajax({
                url: 'ajax/discount.ajax.php',
                method: "POST",
                data: datum,
                cache: false,
                contentType: false,
                processData: false,
                dataType:"json",
                success: function(data) {
                  // var status = data["status"];
                  var start = new Date(data["startdate"]);
                  var end = new Date(data["enddate"]);
                  var current = new Date();
                   
                  // Validate the discount status
                  if (current >= start && current <= end) {
                    var discount = data["amount"] ? data["amount"] : 0;
                  }
                  else{
                    var discount = 0;
                  }
                  
                  var qty = 1; // Default quantity

                  productinfo.push({"id":id, "productName":product, "Quantity":qty, "salePrice":saleprice, "Discount":discount})
                  updateArray();

                  var tr = '<tr>' +
                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-dark">' + product + '</span><input type="hidden" class="form-control product_c" name="productarr[]" value="' + product + '"><input type="hidden" class="form-control idtax" name="tax_arr[]" value="' + taxId + '"><input type="hidden" class="form-control barcode" name="barcode_arr[]" value="' + barcode + '"></td>' +
                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-primary stocklbl" name="stock_arr[]" id="stock_id' + id + '">' + stock + '</span><input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd' + id + '" value="' + stock + '"></td>' +
                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning price" name="price_arr[]" id="price_id' + id + '">' + saleprice + '</span><input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd' + id + '" value="' + saleprice + '"></td>' +

                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-warning discount" name="discount_arr[]" id="discount_id' + id + '">' + discount + '</span><input type="hidden" class="form-control discount_c" name="discount_c_arr[]" id="discount_idd' + id + '" value="' + discount + '"></td>' +

                  '<td><input type="number" class="form-control qty" min="1" name="quantity_arr[]" id="qty_id' + id + '" value="' + 1 + '" size="1"></td>' +
                  '<td style="text-align:left; vertical-align:middle; font-size:17px;"><span class="badge badge-danger totalamt" name="netamt_arr[]" id="saleprice_id' + id + '">' + saleprice + '</span><input type="hidden" class="form-control saleprice" name="saleprice_c_arr[]" id="saleprice_idd' + id + '" value="' + saleprice + '"></td>' +
              
                  //remove item button
              
                  '<td style="text-align:left; vertical-align:middle;"><center><name="remove" class="btnremove" data-id="' + id + '"><span class="fas fa-trash" style="color:red;"></span></center></td>' +
                  '</tr>';
              
                  $('.details').append(tr);
                
                  calculateSubtotal();

                } ,error: function(xhr, status, error) {
                  console.error("AJAX request error:", error);
                }
              });
            
            }
          }
        } else {
          Swal.fire("Error", "The product does not exist in the database.", "error");
          $("#pos-select").val("");
        }
      },
      error: function() {
        Swal.fire("Error", "Failed to retrieve product data from the server.", "error");
      }
    });
  });
});




function calculateSubtotal() {
  var subTotal = 0;
  var total = 0;
  var tax = 0;
  var taxableAmt = 0;
  var nontaxableAmt = 0;
  var taxablesubTotal = 0;
  var totalDiscount = 0;
  var discountedPrice = 0;
  var totaldue = 0;

  $(".details tr").each(function() {
    var tr = $(this);
    var qty = parseInt(tr.find(".qty").val());
    var price = parseFloat(tr.find(".price").text());
    var saleprice = qty * price;
    var taxId = parseInt(tr.find(".idtax").val());
    var discount = parseFloat(tr.find(".discount").text());
    var totalDis=discount*qty;

    discountedPrice=saleprice-totalDis;
    
    total += discountedPrice;
    totaldue = -total;

    if (taxId > 0) {
      taxableAmt += discountedPrice;
      taxablesubTotal += discountedPrice / ((taxId / 100) + 1);
    } else {
      nontaxableAmt += discountedPrice;
    }

    var productDiscount = qty * discount;
    totalDiscount += productDiscount;

    tr.find(".totalamt").text(discountedPrice);
    tr.find(".saleprice").val(saleprice);
  });

 

  subTotal = taxablesubTotal + nontaxableAmt;
  tax = total - subTotal;

  //total -= totalDiscount;

  $("#txtsubtotal_id").val(subTotal.toFixed(2));
  $("#txttaxtotal_id").val(tax.toFixed(2));
  $("#txttotal_id").val(total.toFixed(2));
  $("#txtdue_id").val(totaldue.toFixed(0));
  $("#taxabletotal_id").val(taxableAmt.toFixed(2));
  $("#nontaxabletotal_id").val(nontaxableAmt.toFixed(2));
  $("#taxablesubtotal_id").val(taxablesubTotal.toFixed(2));
  $("#txtdiscounttotal_id").val(totalDiscount.toFixed(2));
}

/*=============================================
CHECK FOR CHANGE IN THE CASH RADIO BUTTON
=============================================*/	
// Get a reference to the radio button element
var radio = document.getElementById("radioSuccess1");

// Add an event listener to the radio button to detect changes in its checked status
$("#radioSuccess1").change(function() {
  // Check if the radio button is checked
  var isChecked = $(this).prop("checked");

  // Make the element with ID "txtpaid_id" readonly based on the checked status
  $("#txtpaid_id").prop("readonly", !isChecked);
});



/*=============================================
// check if the mpesa radio button is checked
=============================================*/	
// Listen for changes in the radio button selection
$('input[name="r3"]').on('change', function() {
  var selectedOption = $(this);
  var optionValue = selectedOption.val();
  
  // Show the modal if "M-pesa" option is selected
  if (optionValue === 'M-pesa') {
    // Swal.fire({
    //   icon: "warning",
    //   title: "Coming soon.",
    //   showConfirmButton: false,
    //   timer: 2000 // Auto close after 2 seconds
    // }).then(function(result) {
    //   if (result.dismiss === Swal.DismissReason.timer) {
    //     // Timer expired, unselect the radio button
    //     selectedOption.prop('checked', false);
    //   }
    // });
  } else {
    $('#exampleModal').modal('hide');
  }
  

  // Get the total and paid input fields
  var totalInput = $('#txttotal_id');
  var paidInput = $('#txtpaid_id');
  var dueInput = $('#txtdue_id');

  // Attach change event listener to both input fields
  totalInput.on('input', calculateDueAmount);
  paidInput.on('input', calculateDueAmount);

  // Calculate and update the due amount
  function calculateDueAmount() {
    var total = parseFloat(totalInput.val()) || 0; // Get the total amount as a float
    var paid = parseFloat(paidInput.val()) || 0; // Get the paid amount as a float
    var due = paid - total; // Calculate the due amount

    // Set the due amount value in the input field
    dueInput.val(due.toFixed(0));
  }

});
// Get a reference to the radio button element
var radio = document.getElementById("topupMpesa");

// Add an event listener to the radio button to detect changes in its checked status
radio.addEventListener("change", function() {
    // Check if the radio button is checked
    var isChecked = radio.checked;

    // Make the element with ID "txtpaid_id" readonly based on the checked status
    Swal.fire({
      icon: "warning",
      title: "Coming soon.",
      showConfirmButton: false,
      timer: 2000 // Auto close after 2 seconds
    }).then(function(result) {
      if (result.dismiss === Swal.DismissReason.timer) {
        // Timer expired, uncheck the radio button
        radio.checked = false;
      }
    });
});


// Listen for changes in the points radio button selection
$('input[name="r3"]').on('change', function() {
  
  var selectedOption = $(this).val();
      
  // Show the modal if "Cheque" option is selected
  if (selectedOption === 'points') {
    $('.save-order').hide();
    $('.points-plat').show();
  } else {
    $('.save-order').show();
    $('.points-plat').hide();
  }
  
  
});


// validate the enterd phone number and fetch the availlable points
$(document).on("change", "#pselectcustomer", function() {
  totalAmount = parseFloat($('#txttotal_id').val());
  customer_id = $(this).val();
  
  var data = new FormData();
  data.append("customer_id", customer_id);
  
  // fetch points linked to the entered phone number
  $.ajax({
    type: "POST",
    url: "ajax/loyalty.ajax.php",
    data: data,
    contentType: false,
    cache: false,
    processData: false,
    dataType: "json",
    success: function(answer) {
      // answer = answer[0]
      // Check if the list is empty or not
      if (Array.isArray(answer) && answer.length === 0) {
        $('#nophone').show();
        $('#lesspoints').hide();
        $('.payment-methods').hide();
      } else {
        $('#nophone').hide();
        let totalPointsEarned = 0;
        let totalPointsRedeemed = 0;

        answer.forEach(item => {
          totalPointsEarned += parseInt(item.PointsEarned);
        });
        
        answer.forEach(item => {
          totalPointsRedeemed += parseInt(item.PointsRedeemed);
        });

        var totalPoints = totalPointsEarned - totalPointsRedeemed
        
        LoyaltyConversionName = "LoyaltyValueConversion";

        var datum = new FormData();
        datum.append("LoyaltyConversionName", LoyaltyConversionName);
        
        // fetch points linked to the entered phone number
        $.ajax({
          type: "POST",
          url: "ajax/loyalty.ajax.php",
          data: datum,
          contentType: false,
          cache: false,
          processData: false,
          dataType: "json",
          success: function(response) {
            conversionValue = response.SettingValue
            convertedValue = totalPoints*conversionValue
            TopUpAmount = totalAmount - convertedValue
            $(".top-up").val(TopUpAmount);
            if (convertedValue < totalAmount) {
              $('#lesspoints').show();
              $('.payment-methods').show();
              $('.topupCash').click(function() {
                $("#redeemedpoints").val(totalPoints);
                $("#pointamountvalue").val(convertedValue);
              });
            } else{
              var redeemedpoints = totalAmount/conversionValue
              $("#redeemedpoints").val(redeemedpoints);
              $("#pointamountvalue").val(convertedValue);
              $('#eligible').show();
            }
          } ,error: function(xhr, status, error) {
            console.error("AJAX request error:", error);
          }
        });
      }
    } ,error: function(xhr, status, error) {
      console.error("AJAX request error:", error);
    }
  });
});

// Function to update the array and set it to the productsList input
function updateArray() {
  const updatedArrayJSON = JSON.stringify(productinfo);
  $('#productsList').val(updatedArrayJSON);
}


$(function() {
  $('#posForm').on('submit', function() {
    // check if a payment method is selected
    var radios = document.getElementsByName("r3");
    var selected = false;
    

    for (var i = 0; i < radios.length; i++) {
      if (radios[i].checked) {
        selected = true;
        break;
      }
    }

    if (!selected) {
      // No radio button selected, display Sweet Alert
      Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: 'Please select a payment method!',
        timer:2000,
        showConfirmButton:false,
      });
      return false; // Prevent form submission
    }
    // Get the values of the inputs
    var cname = document.getElementById("cname").value;
    var phone = document.getElementById("phone").value;
    var cid = document.getElementById("cid").value;

    // Check if any input is empty
    if ((cname.trim() === "" && cname !== null) || (phone.trim() === "" && phone !== null)) {
      Swal.fire({
        icon: "warning",
        title: "Empty Fields",
        text: "Please fill in all the required fields.",
        timer:2000,
        showConfirmButton:false,
      });
      return false; // Prevent form submission
    }

    // Check phone length and prefix
    if (phone.startsWith("254") && phone !== null) {
      // Phone starts with 254, should have a minimum and maximum of 12 characters
      if (phone.length < 12 || phone.length > 12) {
        Swal.fire({
          icon: "warning",
          title: "Invalid Phone Number",
          text: "Phone number should have exactly 12 digits when starting with 254.",
          timer:2000,
          showConfirmButton:false,
        });
        return false; // Prevent form submission
      }
    } else if (phone.startsWith("01") || phone.startsWith("07")) {
      // Phone starts with 01 or 07, should have a minimum and maximum of 10 characters
      if (phone.length < 10 || phone.length > 10) {
        Swal.fire({
          icon: "warning",
          title: "Invalid Phone Number",
          text: "Phone number should have exactly 10 digits when starting with 01 or 07.",
          timer:2000,
          showConfirmButton:false,
        });
        return false; // Prevent form submission
      }
    } else {
      // Phone has an invalid prefix
      Swal.fire({
        icon: "warning",
        title: "Invalid Phone Number",
        text: "Phone number should start with 254, 01, or 07.",
        timer:2000,
        showConfirmButton:false,
      });
      return false; // Prevent form submission
    }

    if (cid !== null) {
      // Check cid length
      if (cid.length < 8 || cid.length > 8) {
        Swal.fire({
          icon: "warning",
          title: "Invalid Identification Number",
          text: "Identification number should have exactly 8 characters.",
          timer:2000,
          showConfirmButton:false,
        });
        return false; // Prevent form submission
      }
    }

    // Check if any item is added to the cart
    var cartItems = productinfo.length;
    if (cartItems === 0) {
      Swal.fire({
        icon: "error",
        title: "No Items in Cart",
        text: "Please add at least one item to the cart before saving the order.",
        timer:2000,
        showConfirmButton:false,
      });
      return false; // Prevent form submission
    }

    // If all validations pass, allow form submission
    return true;

  });
});



$.ajax({
  type: "GET",
  url: "ajax/settings.ajax.php",
  cache: false,
  success: function(settingsData) {
    
    // Parse the JSON response
    try {
      settingsData = JSON.parse(settingsData);
    } catch (error) {
      console.error("Error parsing JSON response:", error);
      return;
    }
    var CustomerDetailsSetting = settingsData.find(setting => setting.SettingName === "CustomerDetails");
    var loyaltyPointsSetting = settingsData.find(setting => setting.SettingName === "Loyaltypoints");
    
    // Convert to integers
    var customerDetailsValue = parseInt(CustomerDetailsSetting.SettingValue, 10);
    var loyaltyPointsValue = parseInt(loyaltyPointsSetting.SettingValue, 10);

    if (customerDetailsValue && loyaltyPointsValue) {
      if (customerDetailsValue === 1 && loyaltyPointsValue === 1) {
        $('.pselectcustomer').show();
        $('.points').show();
      } else {          
        // Check a checkbox based on the condition
        if (customerDetailsValue === 1) {
          $('.pselectcustomer').show();
        } else if (loyaltyPointsValue === 1) {
          $('.points').show();
        } else{
          $('.pselectcustomer').hide();
          $('.points').hide();
        }
      }
    }
  } ,error: function(xhr, status, error) {
    console.error("AJAX request error:", error);
  }
});