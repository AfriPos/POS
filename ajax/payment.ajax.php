<?php

require_once "../controllers/payments.controller.php";
require_once "../controllers/packagevalidate.controller.php";
require_once "../models/payment.model.php";
// require_once "../models/connection.php";
require_once "../models/packagevalidate.model.php";

class AjaxInvoices{

	/*=============================================
	GENERATE CODE FROM ID CATEGORY
	=============================================*/	

	public $idInvoice;
	public $invoiceid;
	public $receiptNumber;
	public $paymentid;
	public $organizationcode;
	public $amount;
	public $timestamp;

	public function ajaxAddPayment(){

		$item = "invoiceId";
		$value = $this->idInvoice;

		$answer = PaymentController::ctrShowInvoices($item, $value);

		echo json_encode($answer);

	}


	public function ajaxFetchPayments(){

		$item = "InvoiceID";
		$value = $this->invoiceid;

		$answer = PaymentController::ctrfetchGroupedPayments($item, $value);

		echo json_encode($answer);

	}

	public function ajaxFetchPayment(){

		$item = "receiptNumber";
		$value = $this->receiptNumber;

		$answer = PaymentController::ctrShowPayments($item, $value);

		echo json_encode($answer);

	}

	public function ajaxFetchCustomerdetails(){

		$element = "paymentvalidation";
		$table = "customers";
		$countAll = null;
		$organisationcode = $this->organizationcode;

		$answer = packagevalidateController::ctrPackageValidate($element, $table, $countAll, $organisationcode);

		echo json_encode($answer);

	}

	public function Fetchpayment(){

		$stmt = connection::connect()->prepare("SELECT * FROM payments WHERE Amount = :amount AND PaymentDate >= :dateTimeStr");

		$stmt->bindParam(":amount", $this->amount);
		$stmt->bindParam(":dateTimeStr", $this->timestamp);

		$stmt->execute();
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		echo json_encode($result);

		$stmt->closeCursor();

		$stmt = null;

	}


}

/*=============================================
MAKE PAYMENT
=============================================*/ 

if(isset($_POST["idInvoice"])){

    $addPayment = new AjaxInvoices();
    $addPayment -> idInvoice = $_POST["idInvoice"];
    $addPayment -> ajaxAddPayment();
  
}

if(isset($_POST["invoiceid"])){

    $fetchPayment = new AjaxInvoices();
    $fetchPayment -> invoiceid = $_POST["invoiceid"];
    $fetchPayment -> ajaxFetchPayments();
  
}

if(isset($_POST["receiptNumber"])){

    $fetchPayment = new AjaxInvoices();
    $fetchPayment -> receiptNumber = $_POST["receiptNumber"];
    $fetchPayment -> ajaxFetchPayment();
  
}

if(isset($_POST["organizationcode"])){

    $fetchCustomerDets = new AjaxInvoices();
    $fetchCustomerDets -> organizationcode = $_POST["organizationcode"];
    $fetchCustomerDets -> ajaxFetchCustomerdetails();
  
}

if (isset($_POST['amount']) && isset($_POST['timestamp'])) {
    $fetchpayment = new AjaxInvoices();
    $fetchpayment -> amount = $_POST["amount"];
    $fetchpayment -> timestamp = $_POST["timestamp"];
    $fetchpayment -> Fetchpayment();	
}

  
