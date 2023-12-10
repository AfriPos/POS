<?php
require_once '../../vendor/autoload.php';

// Create a new TCPDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Receipt');
$pdf->SetSubject('Receipt');
$pdf->SetKeywords('Receipt, TCPDF, example');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

// Set auto page breaks
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Start a new page
$pdf->AddPage();

// Set the font and font size
$pdf->SetFont('helvetica', 'B', 12);

// Output the company name and address
$pdf->Cell(0, 10, 'Your Company Name', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, 'Your Company Address', 0, 1, 'C');

// Output the receipt details
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Receipt', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 10, 'Order ID:', 0, 0);
$pdf->Cell(0, 10, '123456789', 0, 1);

$pdf->Cell(40, 10, 'Date:', 0, 0);
$pdf->Cell(0, 10, date('Y-m-d H:i:s'), 0, 1);

$pdf->Ln(5);

// Output the table headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(50, 10, 'Product', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(40, 10, 'Price', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total', 1, 1, 'C');

// Output the table rows
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 10, 'Product 1', 1, 0, 'C');
$pdf->Cell(30, 10, '2', 1, 0, 'C');
$pdf->Cell(40, 10, '10.00', 1, 0, 'C');
$pdf->Cell(40, 10, '20.00', 1, 1, 'C');

$pdf->Cell(50, 10, 'Product 2', 1, 0, 'C');
$pdf->Cell(30, 10, '1', 1, 0, 'C');
$pdf->Cell(40, 10, '15.00', 1, 0, 'C');
$pdf->Cell(40, 10, '15.00', 1, 1, 'C');

// Output the total
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(120, 10, 'Total:', 1, 0, 'R');
$pdf->Cell(40, 10, '35.00', 1, 1, 'C');

// Close and output the PDF
$pdf->Output('receipt.pdf', 'I');
