<?php

require_once "../controllers/supplier.controller.php";
require_once "../models/supplier.model.php";

class AjaxSupplier{

	/*=============================================
 	 EDIT SUPPLIER
  	=============================================*/ 

  	public $Supplier;
	
	public function ajaxEditSupplier(){
		
		$item = "supplierid";
		$value = $this->Supplier;
		$answer = supplierController::ctrShowSuppliers($item, $value);
	
		echo json_encode($answer);
	}

}
/*=============================================
EDIT SUPPLIER
=============================================*/ 

if(isset($_POST["Supplier"])){

    $editSupplier = new AjaxSupplier();
    $editSupplier -> Supplier = $_POST["Supplier"];
    $editSupplier -> ajaxEditSupplier();
  
  }
  
