<?php

require_once "../controllers/product.controller.php";
require_once "../models/product.model.php";

class AjaxProducts{

	// /*=============================================
	// GENERATE CODE FROM ID CATEGORY
	// =============================================*/	

	// public $idCategory;

	// public function ajaxCreateProductCode(){

	// 	$item = "idCategory";
	// 	$value = $this->idCategory;
	// 	$order='id';

	// 	$answer = productController::ctrShowProducts($item, $value, $order);

	// 	echo json_encode($answer);

	// }

	/*=============================================
 	 EDIT PRODUCT
  	=============================================*/ 

  	public $barcodeProduct;
	public $data;
	public $productid;
	
	public function ajaxEditProduct(){
		// Get product by id
		$item = "id";
		$value = $this->barcodeProduct;
		$order='id';
		$answer = productController::ctrShowProducts($item, $value, $order, false);
	
		echo json_encode($answer);
	}

	public function ajaxShowproducts(){
		// Get product by id
        $item = $this->data['item'];
        $value = $this->data['value'];
        $order= $this->data['order'];
        $answer = productController::ctrShowProducts($item, $value, $order, true);
    
        echo json_encode($answer);
	}

	public function ajaxFetchProduct(){
        $item = "id";
        $value = $this->productid;
        $order= "id";
		$product = productController::ctrShowProducts($item, $value, $order, false);
        echo json_encode($product);
	}

}

/*=============================================
EDIT PRODUCT
=============================================*/ 

	if (isset($_POST['productId'])) {
		$fetchProduct = new AjaxProducts();
		$fetchProduct -> productid = $_POST["productId"];
		$fetchProduct -> ajaxFetchProduct();
	}

    if (isset($_POST["item"]) && isset($_POST["value"]) && isset($_POST["order"])) {

        $products = new AjaxProducts();
        $products->data = array(
            'item' => $_POST["item"],
            'value' => $_POST["value"],
            'order' => $_POST["order"]
        );
        $products->ajaxShowproducts();
    }
	
	if(isset($_POST["barcodeProduct"])){

		$editProduct = new AjaxProducts();
		$editProduct -> barcodeProduct = $_POST["barcodeProduct"];
		$editProduct -> ajaxEditProduct();
	
	}

	// ajax-fetch-data.php
	if (isset($_POST['selectedYear']) && isset($_POST['selectedMonth'])) {
			// Fetch sales data from the database
		$salesData = productController::fetchSalesData(); // Implement the function to fetch sales data
		
		// Get the selected year and month from the AJAX request
		$selectedYear = $_POST['selectedYear'];
		$selectedMonth = $_POST['selectedMonth'];
		
		// Initialize variables to keep track of product quantities and cash amounts
		$productQuantities = array();
		$productCashAmounts = array();
		
		// Filter data for the selected year and month
		$filteredData = array_filter($salesData, function ($row) use ($selectedYear, $selectedMonth) {
			$startDate = strtotime($row['startdate']);
			return date('Y', $startDate) == $selectedYear && date('n', $startDate) == $selectedMonth;
		});
		
		foreach ($filteredData as $index => $row) {
			$productName = $row['productName'];
			$quantity = $row['Quantity'];
			$salePrice = $row['salePrice'];
			$discount = $row['Discount'];
		
			// Calculate the total quantity sold and total cash amount for each product
			if (!isset($productQuantities[$productName])) {
				$productQuantities[$productName] = 0;
				$productCashAmounts[$productName] = 0;
			}
			$productQuantities[$productName] += $quantity;
			$productCashAmounts[$productName] += ($quantity * $salePrice) - $discount;
		}
		
		// Prepare the updated table rows
		$tableRows = "";
		$counter = 1;
		foreach ($productQuantities as $productName => $totalQuantity) {
			$totalCashAmount = $productCashAmounts[$productName];
			$tableRows .= "<tr>";
			$tableRows .= "<td>" . $counter . "</td>";
			$tableRows .= "<td>" . date("F", mktime(0, 0, 0, $selectedMonth, 1)) . " $selectedYear</td>";
			$tableRows .= "<td>" . $productName . "</td>";
			$tableRows .= "<td>" . $totalQuantity . "</td>";
			$tableRows .= "<td>" . number_format($totalCashAmount) ."</td>";
			$tableRows .= "</tr>";
			$counter++;
		}
		
		echo $tableRows;
	}
	
?>
	