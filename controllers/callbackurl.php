<?php
header("Content-Type: application/json");

class CallbackController {
    
    public function addCallbackPayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // DATA
            $mpesaResponse = file_get_contents('php://input');
            $mpesaResponse = json_decode($mpesaResponse, true);

            if ($mpesaResponse['Body']['stkCallback']['ResultCode'] == 0) {
                // Payment was successful
                // Create an invoice and associate the payment

                $amount = $mpesaResponse['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
                $paymentId = $mpesaResponse['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];

                // Use the payment ID to insert into the invoices table
                $invoiceModel = new InvoiceModel();

                $productsList = json_decode($_POST["productsList"], true);
                // echo json_encode($productsList);

                $totalPurchasedProducts = array();

                foreach ($productsList as $key => $value) {

                    array_push($totalPurchasedProducts, $value["Quantity"]);

                    $tableProducts = "products";

                    $item = "id";
                    $valueProductId = $value["id"];
                    $order = "id";

                    $getProduct = productModel::mdlShowProducts($tableProducts, $item, $valueProductId, $order);

                    $item1a = "sales";
                    $value1a = $value["Quantity"] + $getProduct["sales"];

                    $newSales = productModel::mdlUpdateProduct($tableProducts, $item1a, $value1a, $valueProductId);

                    $item1b = "stock";
                    $value1b = $getProduct["stock"] - $value["Quantity"];

                    $newStock = productModel::mdlUpdateProduct($tableProducts, $item1b, $value1b, $valueProductId);

                }

                $currentDate = date('Y-m-d');
                $future_date = date("Y-m-d", strtotime($currentDate . " +15 days"));

                $productsList = $_POST['productsList'];
                $invoiceStartDate = $currentDate;
                $invoiceDueDate = $future_date;
                $invoiceCustomerName = $_POST['cname'];
                $invoicePhone = $_POST['phone'];
                $invoiceIdNumber = $_POST['cid'];
                $invoiceTotalTax = $_POST['totaltax'];
                $invoiceSubtotal = $_POST['subtotal'];
                $invoiceTotal = $_POST['total'];
                $invoiceDiscount = $_POST['totaldiscount'];
                $invoiceDueAmount = $_POST['dueamount'];
                $invoiceUserId = $_SESSION['userId'];

                // Generate the invoice ID
                $nextNumericPart = $invoiceModel->getNextInvoiceNumericPart(); // Get the next available numeric part
                $invoiceId = "INVC-" . str_pad($nextNumericPart, 8, '0', STR_PAD_LEFT);

                // Insert invoice data into the invoices table, linking it with the payment
                $invoiceModel->insertInvoice($invoiceId, $productsList, $invoiceStartDate, $invoiceDueDate, $invoiceCustomerName, $invoicePhone, $invoiceIdNumber, $invoiceTotalTax, $invoiceSubtotal,  $invoiceTotal, $invoiceDiscount, $invoiceDueAmount, $invoiceUserId);

                // Retrieve payment data from the form or request parameters
                $amount = $_POST['txtpaid'];
                $paymentMethod = $_POST['r3'];

                // Create an instance of the PaymentModel
                $paymentModel = new PaymentModel();

                // Insert payment data into the payments table
                $paymentModel->insertPayment($paymentId, $amount, $paymentMethod, $invoiceId);

                $response = array(
                    "ResultCode" => 0,
                    "ResultDesc" => "Confirmation Received Successfully"
                );
            } else {
                // Payment was not successful
                // Delete the invoice and do not record the payment

                // Your code to delete the invoice goes here

                $response = array(
                    "ResultCode" => 1,
                    "ResultDesc" => "Payment Failed"
                );
            }

            // Send the response back
            echo json_encode($response);
        }
    }
}

$callbackController = new CallbackController();
$callbackController->addCallbackPayment();
