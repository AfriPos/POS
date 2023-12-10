<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Requested-With');

session_start();
require_once "../../controllers/payments.controller.php";
require_once "../../models/payment.model.php";
require_once "../../controllers/product.controller.php";
require_once "../../models/product.model.php";
require_once "../../controllers/user.controller.php";
require_once "../../models/user.models.php";
require_once '../../controllers/tax.controller.php';
require_once '../../models/tax.models.php';
require_once '../../controllers/store.controller.php';
require_once '../../models/store.model.php';
require_once '../../models/connection.php';

require_once '../../vendor/autoload.php';

$item='paymentid';
$value=$_GET['receipt'];
$paymentData=PaymentController::ctrShowPayments($item, $value);

$item2 = 'invoiceId';
$value2 = $paymentData['invoiceId'];
$order = 'id';
$invoices = PaymentController::ctrShowInvoices($item2, $value2);

$item3 = "store_id";
$value3 = $paymentData['store_id'];
$stores = storeController::ctrShowStores($item3, $value3);


$item2 = 'null';
$value2 = 'taxId';
$tax = taxController::ctrShowTax($item2, $value2);

// Create a new TCPDF instance
$pdf = new TCPDF('P', 'mm', array(80, 297), true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Receipt');
$pdf->SetSubject('Receipt');
$pdf->SetKeywords('Receipt, Thermal Printer, Continuous Roll');

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Remove header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$margin = 5; // Adjust the margin value as per your requirement
$pdf->SetMargins($margin, $margin, $margin);

// Set auto page breaks
$pdf->SetAutoPageBreak(false, 0);

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

$invoiceNumber = $invoices['invoiceId'];
// Set your timezone
date_default_timezone_set('Africa/Nairobi');

// Get the current timestamp in your timezone
$timestamp = time();

// Generate a random 4-digit number
$randomNumber = mt_rand(1000, 9999);

// Generate the receipt number based on the current timestamp and random number
$receiptNumber = date('YmdHis', $timestamp) . $randomNumber;
// Set content
$html = '
    <div style="text-align:center;">
        <h2>Company Name</h2>
        <p>Address Line 1<br>Address Line 2<br>Address Line 3</p>
    </div>
    <p>Receipt Number: ' .$receiptNumber. '</p>
    <p>Date: ' . $invoices['datecreated'] . '</p>
    <p>Store: '.$stores['store_name'].'</p>

    <hr style="border: none; border-top: 1px dashed #000;">

    <table class="product-table">
        <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Each</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>';

$jsonArray = $invoices["products"];
// Decode the JSON array
$data = json_decode($jsonArray, true);

$totalAmount = 0;
$vatAmounts = array();

// Iterate over each object and fetch the productName and quantity
foreach ($data as $item) {
    $id = $item['id'];
    $productName = $item['productName'];
    $quantity = $item['Quantity'];

    $itemKey = 'id';
    $value = $id;
    $order = 'id';
    $product = productController::ctrShowProducts($itemKey, $value, $order);

    // Get the tax rate from the product
    $vatRate = $product['taxId'];

    // Calculate the price and total amount based on the tax rate
    $price = ($item['salePrice'] - $item['Discount']) * $quantity;
    $totalAmount += $price;

    // Add the HTML for the current product to the $html variable
    $html .= '
    <tr>
        <td style="white-space: nowrap;">' . $productName . '</td>
    </tr>
    <tr>
        <td style="font-size: 5px;">' . $product['barcode'] . '</td>
        <td>' . $quantity . '</td>
        <td>' . number_format($product['saleprice'], 2) . '</td>
        <td>' . number_format($price, 2) . '</td>
    </tr>';

    // Calculate the VAT amount based on the product's VAT rate
    $subamount = $price / (($vatRate / 100) + 1);
    $vatAmount = $price - $subamount;

    // Accumulate VAT amounts for each VAT rate
    if (isset($vatAmounts[$vatRate])) {
        $vatAmounts[$vatRate] += $vatAmount;
    } else {
        $vatAmounts[$vatRate] = $vatAmount;
    }
}


$html .= '</tbody>
    </table>
    ';
// $item3 = 'invoiceId';
// $value3 = $_GET['invoiceId'];
// $paymentData = PaymentController::ctrShowPayments($item3, $value3);

$cashPaid = 0;
$mpesaPay = 0;
$change = 0;

if (is_array($paymentData) && isset($paymentData['invoiceId'])) {
    $amount = $paymentData['amount'];
    switch ($paymentData['paymentmethod']) {
        case 'cash':
            $cashPaid += $amount;
            break;
        case 'mpesa':
            $mpesaPay += $amount;
            break;
    }
}

// Calculate the change
if ($cashPaid > $totalAmount) {
    $change = $cashPaid - $totalAmount;
}

$html .= '  <hr style="border: none; border-top: 1px dashed #000;">
            <div class="paydetails" style="margin-top:5px;">
        <table class="Totals">
            <tbody>
                
                <tr>
                    <td>Total:</td>
                    <td>' . number_format($totalAmount, 2) . '</td>
                </tr>
                 <tr>
                    <td>Cash Paid:</td>
                    <td>' . number_format($cashPaid, 2) . '</td>
                </tr>
                <tr>
                    <td>Mpesa Pay:</td>
                    <td>' . number_format($mpesaPay, 2) . '</td>
                </tr>
                <tr>
                    <td>Change:</td>
                    <td>' . number_format($change, 2) . '</td>
                </tr>
            </tbody>
        </table>
        <hr style="border: none; border-top: 1px dashed #000;">
        </div>
   ';
   $itemSeller ="userId";
   $valueSeller = $invoices["userId"];
   $seller=userController::ctrShowUsers($itemSeller,$valueSeller);
   $totalQty = 0;
foreach ($data as $item) {
    $quantity = $item['Quantity'];
    $totalQty += $quantity;
}
$html .='
    <table class="servedby">
    <tbody>
    <tr>
    <td>
    Total Qty: ' . $totalQty . ' units
    </td>
    </tr>
    <tr>
    <td>
    Served by: '.$seller['name'].'
    </td>
    </tr>
    </tbody>
    </table>
    <hr style="border: none; border-top: 1px dashed #000;">
';

$mpesaDetailsHtml = '';
    if ($paymentData['paymentmethod'] == 'mpesa') {
        $mpesaDetailsHtml = '
        <div style="border: 1px solid #000;">
            <table>
                <tbody>
                    <tr>
                       <td><strong>Mpesa Details:</strong></td>
                    </tr>
                    <tr>
                       <td>Name:</td>
                    </tr>
                    <tr>
                       <td>Mobile No:</td>
                    </tr>
                    <tr>
                       <td>Mpesa Id:</td>
                    </tr>
                    <tr>
                       <td>Ref No:</td>
                    </tr>
                    <tr>
                       <td>Amount:</td>
                    </tr>
                </tbody>
            </table>
        </div>';
}

$html .= $mpesaDetailsHtml;

$html .= '
    <div class="taxes">
    <table class="taxation-table">
        <thead>
        <tr>
            <th>Rate</th>
            <th>Amt</th>
            <th>VAT Amt</th>
        </tr>
        </thead>
        <tbody>';

foreach ($vatAmounts as $vatRate => $vatAmount) {
    // Calculate the total amount for the current tax rate
    $taxTypeTotalAmount = 0;

    // Iterate over each product and sum up the amounts for the current tax rate
    foreach ($data as $item) {
        $id = $item['id'];
        $quantity = $item['Quantity'];

        $itemKey = 'id';
        $value = $id;
        $order = 'id';
        $product = productController::ctrShowProducts($itemKey, $value, $order);

        // Check if the product's tax rate matches the current vatRate
        if ($product['taxId'] == $vatRate) {
            $price = ($item['salePrice'] - $item['Discount']) * $quantity;
            $taxTypeTotalAmount += $price;
        }
    }

    $html .= '
        <tr>
            <td>' . $vatRate . '%</td>
            <td>' . number_format($taxTypeTotalAmount, 2) . '</td>
            <td>' . number_format($vatAmount, 2) . '</td>
        </tr>';
}


$html .= '</tbody>
    </table>
    <hr style="border: none; border-top: 1px dashed #000;">
    </div>';

    if ($invoices['discount'] > 0) {
        // Only display the product if it has a discount
        $html .= '
            <hr style="border: none; border-top: 1px dashed #000;">
            <strong>DISCOUNT REWARDED</strong>
            <hr style="border: none; border-top: 1px dashed #000;">
            <table>
                <thead>
                    <tr>
                        <td>
                            Item
                        </td>
                        <td>
                            Discount
                        </td>
                    </tr>
                   
                </thead>
                <tbody>';
    
        foreach ($data as $item) {
            $id = $item['id'];
            $productName = $item['productName'];
            $quantity = $item['Quantity'];
            $discount = $item['Discount'];
            $totaldiscount=$discount*$quantity;
            if ($discount > 0) {
                $price2 = ($item['salePrice'] - $discount) * $quantity;
    
                $itemKey = 'id';
                $value = $id;
                $order = 'id';
                $product = productController::ctrShowProducts($itemKey, $value, $order);
    
                $html .= '
                    <tr>
                        <td style="white-space: nowrap;">
                            ' . $productName . '
                        </td>
                        <td>
                            ' . number_format($totaldiscount, 2) . '
                        </td>
                    </tr>';
            }
        }
    
        $html .= '
                <hr style="border: none; border-top: 1px dashed #000;">
                <tr>
                    <td>
                        Total Discount
                    </td>
                    <td>
                        ' . number_format($invoices['discount'], 2) . '
                    </td>
                </tr>
                <hr style="border: none; border-top: 1px dashed #000;">
                </tbody>
            </table>';
    }

    


// Set content and convert HTML to PDF
$pdf->writeHTML($html, true, false, true, false, '');


// Output the PDF content
$pdfContent = $pdf->Output($receiptNumber . '.pdf', 'I');

// Output the PDF content
echo $pdfContent;

?>
 