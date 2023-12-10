<?php

require_once "../controllers/customer.controller.php";
require_once "../models/customer.model.php";

class AjaxCustomer{

	/*=============================================
	EDIT CUSTOMER
	=============================================*/	

	public $customerid;

	public function ajaxEditCustomer(){

		$item = "customer_id";
		$value = $this->customerid;

		$answer = customerController::ctrShowCustomers($item, $value);

		echo json_encode($answer);

	}

}

/*=============================================
EDIT CUSTOMER
=============================================*/	
	
if(isset($_POST["customerid"])){

    $editcustomer = new AjaxCustomer();
    $editcustomer -> customerid = $_POST["customerid"];
    $editcustomer -> ajaxEditCustomer();
}
