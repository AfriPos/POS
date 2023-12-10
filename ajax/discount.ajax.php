<?php

require_once "../controllers/discount.controller.php";
require_once "../models/discount.models.php";

class AjaxDiscountd{

	/*=============================================
	EDIT Discount
	=============================================*/	

	public $productid;
	public $barcode;

	public function ajaxEditDiscount(){

		// if ($this->productid != "") {
			
			$item = "product";
			$value = $this->productid;
	
			$answer = discountController::ctrShowDiscount($item, $value);
	
			echo json_encode($answer);

		// } else {

		// 	$item = "product";
		// 	$value = $this->barcode;
	
		// 	$answer = discountController::ctrShowDiscount($item, $value);
	
		// 	echo json_encode($answer);
			
		// }
		



	}
}

/*=============================================
EDIT Discount
=============================================*/	
if(isset($_POST["productid"])){

	$Discount = new AjaxDiscountd();
	$Discount -> productid = $_POST["productid"];
	$Discount -> ajaxEditDiscount();
}

// if(isset($_GET["barcode"])){

// 	$getDiscount = new AjaxDiscountd();
// 	$getDiscount -> barcode = $_GET["barcode"];
// 	$getDiscount -> ajaxEditDiscount();
// }
