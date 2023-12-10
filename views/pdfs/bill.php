<?php
session_start();

require_once "../../controllers/user.controller.php";
require_once "../../models/user.models.php";

require_once "../../controllers/product.controller.php";
require_once "../../models/product.model.php";

require_once "../../controllers/payments.controller.php";
require_once "../../models/payment.model.php";


class printBill{

public $code;

public function getBillPrinting(){

//WE BRING THE INFORMATION OF THE SALE

// $itemSale = "invoiceId";
// $valueSale = $this->code;

// $answerSale = PaymentController::ctrShowInvoices($itemSale, $valueSale);

// $saledate = $answerSale["startdate"];
// // $products = json_decode($answerSale["products"], true);
// $netPrice = number_format($answerSale["subtotal"],2);
// $tax = number_format($answerSale["totaltax"],2);
// $totalPrice = number_format($answerSale["total"],2);

// //TRAEMOS LA INFORMACIÓN DEL Customer

// // $itemCustomer = "id";
// // $valueCustomer = $answerSale["idCustomer"];

// // $answerCustomer = ControllerCustomers::ctrShowCustomers($itemCustomer, $valueCustomer);

// //TRAEMOS LA INFORMACIÓN DEL Seller

// $itemSeller = $_SESSION["userId"];
// $valueSeller = $answerSale["userId"];

// $answerSeller = userController::ctrShowUsers($itemSeller, $valueSeller);

//REQUERIMOS LA CLASE TCPDF

require "../../vendor/autoload.php";

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage('P', 'A7');

//---------------------------------------------------------

$block1 = <<<EOF

<table style="font-size:9px; text-align:center">

	<tr>
		
		<td style="width:160px;">
	
			<div>
			
				Date: 

				<br><br>
				Inventory System
				
				<br>
				NIT: 71.759.963-9

				<br>
				Address: 5th Ave. Miami, Fl

				<br>
				Phone: 300 786 52 49

				<br>
				Invoice N.$valueSale

				<br><br>					
				Customer: $answerSale[customername]

				<br>
				Seller: $answerSeller[name]

				<br>

			</div>

		</td>

	</tr>


</table>

EOF;

$pdf->writeHTML($block1, false, false, false, false, '');

// ---------------------------------------------------------


$jsonArray = $answerSale["products"];
// Decode the JSON array
$data = json_decode($jsonArray, true);

// Iterate over each object and fetch the productName and quantity
for ($i = 0; $i < count($data); $i++) {
    $id = $data[$i]['id'];
    $productName = $data[$i]['productName'];
    $quantity = $data[$i]['Quantity'];
	$saleprice=$data[$i]['salePrice'];

    $price = ($saleprice - $data[$i]['Discount']) * $quantity;

    $item = 'id';
    $value = $id;
	$order='id';
    $product = productController::ctrShowProducts($item, $value, $order);

$block2 = <<<EOF

<table style="font-size:9px;">

	<tr>
	
		<td style="width:160px; text-align:left">
		$productName
		</td>

	</tr>

	<tr>
	
		<td style="width:160px; text-align:right">
		$ $quantity Units * $saleprice  = $ $price
		<br>
		</td>

	</tr>

</table>

EOF;

$pdf->writeHTML($block2, false, false, false, false, '');

}

// ---------------------------------------------------------

$block3 = <<<EOF

<table style="font-size:9px; text-align:right">

	<tr>
	
		<td style="width:80px;">
			 NET: 
		</td>

		<td style="width:80px;">
			$ $netPrice
		</td>

	</tr>

	<tr>
	
		<td style="width:80px;">
			 TAX: 
		</td>

		<td style="width:80px;">
			$ $tax
		</td>

	</tr>

	<tr>
	
		<td style="width:160px;">
			 --------------------------
		</td>

	</tr>

	<tr>
	
		<td style="width:80px;">
			 TOTAL: 
		</td>

		<td style="width:80px;">
			$ $totalPrice
		</td>

	</tr>

	<tr>
	
		<td style="width:160px;">
			<br>
			<br>
			Thank you for your purchase
		</td>

	</tr>

</table>



EOF;

$pdf->writeHTML($block3, false, false, false, false, '');


// ---------------------------------------------------------
//SALIDA DEL ARCHIVO 

// $pdf->Output('bill.pdf', 'D');

$pdf->Output('bill.pdf');

}

}

$bill = new printBill();
$bill -> code = $_GET["invoiceId"];
$bill -> getBillPrinting();

?>