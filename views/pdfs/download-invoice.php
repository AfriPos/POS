<?php
// require_once('tcpdf/tcpdf.php');
require_once "../../controllers/payments.controller.php";
require_once "../../models/payment.model.php";
require_once "../../controllers/product.controller.php";
require_once "../../models/product.model.php";
require_once '../../controllers/customer.controller.php';
require_once '../../models/customer.model.php';

$item = 'invoiceId';
$value = $_GET['invoiceId'];

$invoices = PaymentController::ctrShowInvoices($item, $value);

$item = "customer_id";
$value1 = $invoices["customer_id"];
$customer = customerController::ctrShowCustomers($item, $value1);

require "../../vendor/autoload.php";
// Create a new TCPDF instance

// Create a new TCPDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

// Set document information
// $pdf->SetCreator('Your Company');
// $pdf->SetAuthor('Your Name');
$pdf->SetTitle('Download Invoice');
// $pdf->SetSubject('Invoice');
// $pdf->SetKeywords('Invoice, TCPDF, PHP');

// Set default header data
$pdf->SetHeaderData('', 0, 'Invoice', 'Invoice', array(0, 0, 0), array(255, 255, 255));
$pdf->setHeaderFont(array('helvetica', '', 12));
$pdf->setFooterFont(array('helvetica', '', 10));
$pdf->setPrintFooter(false); // Disable printing footer


// Set default monospaced font
$pdf->SetDefaultMonospacedFont('helvetica');

// Set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(true, 10);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font styles
$pdf->SetFont('helvetica', 'B', 14);

// Add the title
$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');

// Invoice details
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 8, 'Number: ' . $invoices['invoiceId'], 0, 1, 'R');
$pdf->Cell(0, 8, 'Date: ' . $invoices['startdate'], 0, 1, 'R');
$pdf->Cell(0, 8, 'Due Date: ' . $invoices['duedate'], 0, 1, 'R');
$pdf->Cell(0, 8, 'Total due: Ksh ' . $invoices['total'], 0, 1, 'R');
$pdf->Ln(10);

// Set font styles for addresses
$pdf->SetFont('helvetica', '', 10);

// Company Address
$companyAddress = '
Your Company
123 Company Street
City, State, ZIP
Country
Phone: 123-456-7890
Email: info@company.com
';
$pdf->MultiCell(0, 10, $companyAddress, 0, 'L');

// Customer Address
$customerAddress = '
'.$customer['name'].'
'.$customer['phone'].'
'.$customer['address'].'
';
$pdf->MultiCell(0, 10, $customerAddress, 0, 'R');

// Set font styles for table header
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(2, 117, 216);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(5);
// Set table header row
$pdf->Cell(10, 10, 'N.o', 0, 0, 'C', 1);
$pdf->Cell(40, 10, 'Product', 0, 0, 'C', 1);
$pdf->Cell(40, 10, 'Product N.o', 0, 0, 'C', 1);
$pdf->Cell(10, 10, 'Qty', 0, 0, 'C', 1);
$pdf->Cell(50, 10, 'Price', 0, 0, 'C', 1);
$pdf->Cell(30, 10, 'Total', 0, 1, 'C', 1);

// Set font styles for table data
$pdf->SetFont('helvetica', '', 10);

// Add table rows
$isEvenRow = true;

$jsonArray = $invoices["products"];
// Decode the JSON array
$data = json_decode($jsonArray, true);

// Iterate over each object and fetch the productName and quantity
for ($i = 0; $i < count($data); $i++) {
    $id = $data[$i]['id'];
    $productName = $data[$i]['productName'];
    $quantity = $data[$i]['Quantity'];

    $price = ($data[$i]['salePrice'] - $data[$i]['Discount']) * $quantity;

    $item = 'id';
    $value = $id;
    $order='id';
    $product = productController::ctrShowProducts($item, $value, $order);

    // Set background color for striped effect
    if ($isEvenRow) {
        $pdf->SetFillColor(239, 239, 239);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }

    // Add table data
    $pdf->Cell(10, 10, $i, 0, 0, 'C', 1);
    $pdf->Cell(40, 10, $productName, 0, 0, 'C', 1);
    $pdf->Cell(40, 10, $product['barcode'], 0, 0, 'C', 1);
    $pdf->Cell(10, 10, $quantity, 0, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Ksh ' . $data[$i]['salePrice'], 0, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Ksh ' . $price, 0, 1, 'C', 1);

    // Toggle the row background color
    $isEvenRow = !$isEvenRow;
}

// Set font styles for table data
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(5);
// Add table rows for subtotal, tax, discount, and total
$pdf->Cell(20, 10, '', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '', 0, 0, 'C', 1);
$pdf->Cell(50, 10, 'Subtotal', 0, 0, 'R', 1);
$pdf->Cell(30, 10, 'Ksh ' . $invoices['subtotal'], 0, 1, 'C', 1);

$pdf->Cell(20, 10, '', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '', 0, 0, 'C', 1);
$pdf->Cell(50, 10, 'Tax', 0, 0, 'R', 1);
$pdf->Cell(30, 10, 'Ksh ' . $invoices['totaltax'], 0, 1, 'C', 1);

$pdf->Cell(20, 10, '', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '', 0, 0, 'C', 1);
$pdf->Cell(50, 10, 'Discount', 0, 0, 'R', 1);
$pdf->Cell(30, 10, 'Ksh ' . $invoices['discount'], 0, 1, 'C', 1);

$pdf->Cell(20, 10, '', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '', 0, 0, 'C', 1);
$pdf->Cell(40, 10, '', 0, 0, 'C', 1);
$pdf->Cell(50, 10, 'Total', 0, 0, 'R', 1);
$pdf->Cell(30, 10, 'Ksh ' . $invoices['total'], 0, 1, 'C', 1);

// Footer
$pdf->SetY(-20); // Move to the bottom of the page
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, 'Thank you for your business', 0, 0, 'C');

// Generate a unique identifier (e.g., timestamp and random number)
$uniqueIdentifier = time() . '_' . mt_rand(1000, 9999);

// Format the current date
$currentDate = date('Y-m-d');

// Combine the identifier and date to create the PDF filename
$pdfFilename = "invoice_{$currentDate}_{$uniqueIdentifier}.pdf";

// Output the PDF as a file (force download) with the generated filename
$pdf->Output($pdfFilename, 'D');

?>
