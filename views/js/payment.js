/*=============================================
MAKE PAYMENT
=============================================*/

$(".tables tbody").on("click", "button.addPayment", function(){

    var idInvoice = $(this).attr("idInvoice");

    var datum = new FormData();
    datum.append("idInvoice", idInvoice);

    $.ajax({

        url:"ajax/payment.ajax.php",
        method: "POST",
        data: datum,
        cache: false,
        contentType: false,
        processData: false,
        dataType:"json",
        success:function(answer){

			$("#invoiceId").val(answer.InvoiceID);
			$("#total").val(answer.TotalAmount);
			$("#due").val(answer.DueAmount);
			$("#paid").val(parseInt(answer.DueAmount) + parseInt(answer.TotalAmount));
			$("#payment").val(Math.abs(answer.DueAmount));
        }, error: function() {
            console.log("error occurred showing invoices");
        }

    });

});

// Use jQuery to listen for changes in the payment method radio buttons
$('.paymentmethods').on('change', function() {
    // Check if the selected payment method is not empty
    if ($('.paymentmethods:checked').val()) {
        // Show the modal footer
        $('#modalFooter').show();
    } else {
        // Hide the modal footer if no payment method is selected
        $('#modalFooter').hide();
    }
});


// validate the payment value
$(document).on("keyup change", "#payment", function() {

    var due = Math.abs(parseInt(document.querySelector('#due').value));
    var payment = parseInt(document.querySelector('#payment').value);

    if (payment <= 0) {

        Swal.fire({
                icon: "warning",
                title: "The payment cannot be 0 or less than 0!",
                showConfirmButton: true,
                confirmButtonColor: '#0069d9',
                confirmButtonText: "Close"
                })
        $(this).val(due);

    }

})


/*=============================================
VIEW INVOICE MODAL
=============================================*/

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

$(".tables tbody").on("click", "button.viewInvoice", function(){

    var idInvoice = $(this).attr("idInvoice");

    // Get references to the links
    var viewPdfLink = document.getElementById("view-pdf-link");
    var downloadPdfLink = document.getElementById("download-pdf-link");

    // Set the href attributes using JavaScript
    viewPdfLink.href = 'views/pdfs/view-invoice.php?invoiceId=' + idInvoice;
    viewPdfLink.target = '_blank';
    
    downloadPdfLink.href = 'views/pdfs/download-invoice.php?invoiceId=' + idInvoice;
    downloadPdfLink.target = '_blank';

    openInvoiceModal(idInvoice)
  
});

function openInvoiceModal(idInvoice) {

    var invoice_datum = new FormData();
    invoice_datum.append("idInvoice", idInvoice);

    var payment_datum = new FormData();
    payment_datum.append("invoiceid", idInvoice);

    
    var file = "payment";
    var variable = "idInvoice"
    var value = idInvoice;
    $.ajax({
  
        url:"ajax/payment.ajax.php",
        method: "POST",
        data: invoice_datum,
        cache: false,
        contentType: false,
        processData: false,
        dataType:"json",
        success:function(answer){
            
            var file = "sales";
            var variable = "customerid"
            var value = answer.CustomerID;
            customajaxRequest(file, variable, value, function(response) {
                // console.log(response.name);
                // const customername = response
                // invoice header
                if (response[0].name == "") {
                    $(".invoice-name-number").html("Null <br>" + answer.InvoiceID + "<br>");
                } else {
                    $(".invoice-name-number").html(response[0].name + "<br>" + answer.InvoiceID + "<br>");
                }
            });
            const invoiceid = answer.InvoiceID
            const duedate = answer.DueDate
            
     

            $(".invoice-dates").html(answer.DateCreated + "<br>" + answer.DueDate);
            
            // invoice table                
            var datum = new FormData();
            datum.append("InvoiceID", answer.InvoiceID);

            $.ajax({

                url:"ajax/sales.ajax.php",
                method: "POST",
                data: datum,
                cache: false,
                contentType: false,
                processData: false,
                dataType:"json",
                success:function(answer){
                    // console.log(answer);
                    // Get the table body
                    var tbody = document.querySelector('#invoice-table tbody');
                    
                    for (var i = 0; i < answer.length; i++) {
                        // Create a new table row
                        var newRow = document.createElement('tr');

                        // var Cell1 = document.createElement('td');
                        // Cell1.textContent = index + 1;
                        // newRow.appendChild(Cell1);
                        
                        var Cell2 = document.createElement('td');
                        Cell2.textContent = answer[i].product;
                        newRow.appendChild(Cell2);
                        
                        var Cell3 = document.createElement('td');
                        Cell3.textContent = answer[i].barcode;
                        newRow.appendChild(Cell3);
                        
                        var Cell4 = document.createElement('td');
                        Cell4.textContent = answer[i].Quantity;
                        newRow.appendChild(Cell4);
                        
                        var Cell5 = document.createElement('td');
                        Cell5.textContent = answer[i].saleprice;
                        newRow.appendChild(Cell5);
                        
                        var Cell6 = document.createElement('td');
                        Cell6.textContent = answer[i].saleprice * answer[i].Quantity;
                        newRow.appendChild(Cell6);
                        
                        // Add the new row to the table body
                        tbody.appendChild(newRow);
                    }

                }, error: function() {
                    Swal.fire("Error", "Failed to retrieve product data for the invoice from the server.", "error");
                }

            });

            // invoice sums
            $(".subtotal").text("Ksh " + formatCurrency(answer.Subtotal));
            $(".vat").text("Ksh " + formatCurrency(answer.TotalTax));
            $(".discount").text("Ksh " + formatCurrency(answer.Discount));
            $(".total").text("Ksh " + formatCurrency(answer.TotalTax));
            $(".due").text("Ksh " + formatCurrency(answer.DueAmount));

            // var invoiceid = answer.invoiceId
            var payment_datum = new FormData();
            payment_datum.append("invoiceid", answer.InvoiceID);
            $.ajax({
        
                url:"ajax/payment.ajax.php",
                method: "POST",
                data: payment_datum,
                cache: false,
                contentType: false,
                processData: false,
                dataType:"json",
                success:function(answer){
                    console.log(answer);
                    // Get the table body
                    var tbody = document.querySelector('#payment-table tbody');
                    
                    // initialize the total amount
                    let totalAmount = 0;
                    for (const data in answer) {
                        if (answer.hasOwnProperty(data)) {
                            const payment = answer[data];

                            const amount = parseFloat(payment.Amount); // Convert amount to a number
        
                            if (!isNaN(amount)) {
                                totalAmount += amount;
                            }                            
                    
                            // Create a new table row
                            var newRow = document.createElement('tr');

                            var Cell1 = document.createElement('td');
                            var paymentButton = document.createElement('div');
                            paymentButton.className = 'btn btn-sm btn-success';
                            paymentButton.textContent = payment.PaymentMethod;
                            Cell1.appendChild(paymentButton);
                            newRow.appendChild(Cell1);
                            
                            var Cell2 = document.createElement('td');
                            Cell2.textContent = payment.ReceiptNumber;
                            newRow.appendChild(Cell2);
                            

                            var Cell3 = document.createElement('td');
                            Cell3.textContent =  "Ksh " + formatCurrency(payment.Amount);
                            newRow.appendChild(Cell3);
                            
                            // Add the new row to the table body
                            tbody.appendChild(newRow);
                        }
                    }                            
                    
                    // Create a new table row element
                    var newRow = document.createElement('tr');

                    // Create the first cell with "colspan" attribute
                    var cell1 = document.createElement('td');
                    cell1.setAttribute('colspan', '2');
                    cell1.className = 'text-end';
                    cell1.textContent = 'Total paid:';
                    newRow.appendChild(cell1);

                    // Create the second cell
                    var cell2 = document.createElement('td');
                    cell2.textContent = "Ksh " + formatCurrency(totalAmount);
                    newRow.appendChild(cell2);

                    // Append the new row to the table body
                    tbody.appendChild(newRow);
                    
                }, error: function() {
                    Swal.fire("Error", "Failed to retrieve payment data from the server.", "error");
                }
        
            });
        }, error: function() {
            Swal.fire("Error", "Failed to retrieve invoice data from the server.", "error");
        }
  
    });
    
}
function openPaymentModal(receiptNumber, invoiceid, duedate) {
    // open the payment modal
    // $('#viewPaymentModal').modal('show');
    // Create a button element
    var button = document.createElement('button');

    // Add the necessary attributes for modal functionality
    button.setAttribute('data-toggle', 'modal');
    button.setAttribute('data-target', '#viewPaymentModal');
    button.className = 'invisible';

    // Append the button to a container element (e.g., a div with the ID 'buttonContainer')
    var buttonContainer = document.getElementById('buttonContainer'); // Change to your container's ID
    buttonContainer.appendChild(button);

    // Get the button element by its class or ID
    var button = document.querySelector('.invisible'); // Change to your class or ID

    // Trigger a click event on the button
    button.click();

    var payment_data = new FormData();
    payment_data.append("receiptNumber", receiptNumber);

    $.ajax({

        url:"ajax/payment.ajax.php",
        method: "POST",
        data: payment_data,
        cache: false,
        contentType: false,
        processData: false,
        dataType:"json",
        success:function(answer){

            var file = "sales";
            var variable = "customerid"
            var value = answer.CustomerID;
            customajaxRequest(file, variable, value, function(response) {
                if (response[0].name == "") {
                    $(".payment-col1").html("Null <br>" + answer.InvoiceID + "<br>" + answer.PaymentMethod);
                } else {
                    $(".payment-col1").html(response[0].name + "<br>" + answer.InvoiceID + "<br>" +answer.PaymentMethod);
                }  
            });     

            $(".payment-col2").html(answer.Amount + "<br>" + receiptNumber + "<br>" + duedate);

                        
            // Get the table body
            var tbody = document.querySelector('#invoice2-table tbody');

            // Create a new table row
            var newRow = document.createElement('tr');

            var Cell1 = document.createElement('td');
            var paymentButton = document.createElement('button');
            paymentButton.className = 'btn btn-sm btn-success';
            paymentButton.textContent = "Invoice";
            Cell1.appendChild(paymentButton);
            newRow.appendChild(Cell1);
            
            var Cell2 = document.createElement('td');
            Cell2.textContent = invoiceid;
            newRow.appendChild(Cell2);
            

            var Cell3 = document.createElement('td');
            Cell3.textContent =  "Ksh " + formatCurrency(answer.Amount);
            newRow.appendChild(Cell3);
            
            // Add the new row to the table body
            tbody.appendChild(newRow);

        }, error: function() {
			Swal.fire("Error", "Failed to retrieve paymenmt data from the server.", "error");
		}

    });
}


$('#viewInvoiceModal').on('hidden.bs.modal', function (e) {

    // Select the table body
    var tbody = $('#invoice-table-body');
    
    // Remove all rows from the table body
    tbody.empty();

    // Select the table body
    var tbody = $('#payment-table-body');
    
    // Remove all rows from the table body
    tbody.empty();

});

$('#viewPaymentModal').on('hidden.bs.modal', function (e) {

    // Select the table body
    var tbody = $('#invoice2-table-body');
    
    // Remove all rows from the table body
    tbody.empty();

});

/*=============================================
view reciept
=============================================*/
$(".tables tbody").on("click", "button.view-receipt", function(){

    var receiptNumber = $(this).attr("receipt");
    

    // Get references to the links
    var viewPdfLink = document.getElementById("view-reciept-pdf-link");
    var downloadPdfLink = document.getElementById("download-reciept-pdf-link");

    // Set the 'receipt' attribute for the elements
    $(viewPdfLink).attr("receipt", receiptNumber);
    $(downloadPdfLink).attr("receipt", receiptNumber);

    var payment_data = new FormData();
    payment_data.append("receiptNumber", receiptNumber);

    $.ajax({

        url:"ajax/payment.ajax.php",
        method: "POST",
        data: payment_data,
        cache: false,
        contentType: false,
        processData: false,
        dataType:"json",
        success:function(answer){
            const receiptNumber = answer.ReceiptNumber;
            const idInvoice = answer.InvoiceID;
            if (!customername) {
                customername = "Null"
            }
        
            var payment_data = new FormData();
            payment_data.append("idInvoice", idInvoice);
        
            $.ajax({
        
                url:"ajax/payment.ajax.php",
                method: "POST",
                data: payment_data,
                cache: false,
                contentType: false,
                processData: false,
                dataType:"json",
                success:function(answer){

                    const duedate = answer.DueDate;

                    // $('#viewPaymentModal').modal('show');
                    // Create a button element
                    var button = document.createElement('button');

                    // Add the necessary attributes for modal functionality
                    button.setAttribute('data-toggle', 'modal');
                    button.setAttribute('data-target', '#viewPaymentModal');
                    button.className = 'invisible';

                    // Append the button to a container element (e.g., a div with the ID 'buttonContainer')
                    var buttonContainer = document.getElementById('buttonContainer'); // Change to your container's ID
                    buttonContainer.appendChild(button);

                    // Get the button element by its class or ID
                    var button = document.querySelector('.invisible'); // Change to your class or ID

                    // Trigger a click event on the button
                    button.click();
                    
                    openPaymentModal(receiptNumber, idInvoice, duedate)
        
                }
                
            });            

        }
        
    });



});

$("#view-reciept-pdf-link").on("click", function(){
    var receipt = $(this).attr("receipt");
    var fileURL = "views/pdfs/receipts/" + receipt + ".pdf";

    // Open the PDF in a new blank tab
    var newTab = window.open(fileURL, '_blank');
    
    // Check if the window was opened successfully
    if (!newTab || newTab.closed || typeof newTab.closed === 'undefined') {
        // Display SweetAlert indicating the PDF was not found
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'PDF not found!'
        });
        
    }

});


$("#download-reciept-pdf-link").on("click", function(){
    var receipt = $(this).attr("receipt");
    var fileURL = "views/pdfs/receipts/" + receipt + ".pdf";
    var link = document.createElement('a');
    link.href = fileURL;
    link.target = '_blank';
    link.download = receipt + '.pdf';
    
    // Check if the file exists using an AJAX request
    $.ajax({
        type: 'HEAD',
        url: fileURL,
        success: function () {
            // File exists, create and trigger the link
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        error: function () {
            // File doesn't exist, display SweetAlert
            swal("File Not Found", "The receipt PDF file doesn't exist.", "error");
        }
    });
    

    // Append the anchor to the document
    document.body.appendChild(link);

    // Trigger a click on the anchor to start the download
    link.click();

    // Remove the anchor from the document after download
    document.body.removeChild(link);
    
});

/*=============================================
DELETE TRANSACTION
=============================================*/

$(".tables tbody").on("click", "button.delete-Transaction", function(){

	var reciept = $(this).attr("receipt");
	
	Swal.fire({

		title: 'Are you sure you want to delete the transaction?',
		text: "If you're not sure you can cancel this action!",
		icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0069d9',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Yes, delete transaction!'
        }).then(function(result){
        if (result.value) {

        	window.location = "index.php?route=transactions&reciept="+reciept;

        }

	})

})

    function printReceipt() {
        // Open the generated receipt in a new window or tab
        window.open('views/pdfs/bill-a4.php', '_blank');
    }


/*=============================================
RENEW SERVICE
=============================================*/
$(".payment").on("click", function(){
    
    var stopFlag = false; // Flag to stop further requests

    // Function to toggle between "Pay" and the loading spinner
    function toggleButtonState() {
        if (stopFlag) {
            // Revert the button text when stopping requests
            $(".payment").text("Pay");
        } else {
            // Replace button text with the loading spinner
            $(".payment").html('<div class="loading-spinner"></div> Processing...');
        }
    }

    // Initial state: show "Pay" on the button
    toggleButtonState();

    function makeAjaxRequest() {
        if (stopFlag) {
            // Hide the loading spinner when stopping requests
            $(".loading-spinner").hide();
            return; // Stop making requests if the flag is set to true
        }
        var organizationcode = $(".payment").attr("organizationcode");
    
        var datum = new FormData();
        datum.append("organizationcode", organizationcode);
    
        // Create an AJAX request
        $.ajax({
            url:"ajax/payment.ajax.php",
            method: "POST",
            data: datum,
            cache: false,
            contentType: false,
            processData: false,
            dataType:"json",
            success:function(answer){
                console.log("AJAX request successful:", answer);
                $(".check-phone").show();
                if (answer <= 0) {
                    // If data is greater than 0, schedule another request
                    setTimeout(makeAjaxRequest, 1000); // Wait for 1 second before making the next request
                } else{
                    
                    $(".transaction-success").show();

                    // Hide the success message after 3 seconds (adjust the time as needed)
                    setTimeout(function() {
                        $(".transaction-success").hide();
                        // Reload the current page
                        location.reload();
                    }, 5000); // 5 seconds

                }
            },
            error: function(xhr, status, error) {
                // Handle other types of errors here
                console.log("AJAX request error:", status, error);
            }
        });
    }
    
    // Start the recursive AJAX request
    makeAjaxRequest();

    // Schedule a stop after 60 seconds (60,000 milliseconds)
    setTimeout(function() {
        
        console.log("Stopping after 60 seconds");

        $(".transaction-failed").show();

        // Hide the success message after 3 seconds (adjust the time as needed)
        setTimeout(function() {
            $(".transaction-failed").hide();
            $(".check-phone").hide();
        }, 5000); // 5 seconds

        stopFlag = true; // Set the flag to stop further requests
        // Revert the button text after 60 seconds
        toggleButtonState();
    }, 60000);

});


/*=============================================

=============================================*/

// Handle form submission
const form = $("#renewalForm");
const payButton = form.find(".payment");

payButton.click(function (event) {
    
    event.preventDefault(); // Prevent the default form submission behavior

    // Serialize the form data
    const formData = form.serialize();

    // Send an AJAX request to your server
    $.ajax({
        type: "POST",
        url: "ajax/renewal.ajax.php", // Replace with the path to your PHP script
        data: formData,
        dataType: "json", // Specify JSON as the expected data type
        success: function (response) {
            // Handle the success response here
            console.log(response);
            // You can update the UI or perform other actions based on the response
        },
        error: function (xhr, status, error) {
            // Handle errors here
            console.error(xhr.responseText);
        }
    });
});