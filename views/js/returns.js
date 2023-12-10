$(document).ready(function() {
    var checkStock = function() {
      var barcodeProduct = $('#selectReturnProduct').val();
      var returnQuantity = parseInt($('#quantity').val()); // Get the return quantity
  
      // Create AJAX request data
      var data = {
        barcodeProduct: barcodeProduct
      };
  
      // Perform AJAX request to check stock
      $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
          var stock = parseInt(response.stock);
          console.log(stock);
  
          if (returnQuantity > stock) {
            // Show error message if return quantity exceeds stock
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Return quantity exceeds available stock.',
              timer: 3000,
              showConfirmButton: false
            });
  
            // Reset quantity value to 1
            $('#quantity').val(1);
          }
        },
        error: function() {
          // Show error message if there is an error with the AJAX request
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to check stock. Please try again later.',
            timer: 3000,
            showConfirmButton: false
          });
        }
      });
    };
  
    // Attach event listeners to quantity input
    $('#quantity').on('input keyup', checkStock);
  });