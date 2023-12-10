<?php

require_once "../controllers/loyalty.controller.php";
require_once "../models/loyalty.model.php";

class AjaxLoyalty{
    public $customer_id;
    public $LoyaltyConversionName;

    public function ajaxFetchPoints(){
        $customer_id = $this->customer_id;
        $item = "customer_id";

        $answer = loyaltyController::ctrShowLoyaltyPoints($item, $customer_id, true);

		echo json_encode($answer);
    }
    
    public function ajaxFetchPointConversionValue(){
        $LoyaltyConversionName = $this->LoyaltyConversionName;
        $item = "SettingName";

        $answer = loyaltyController::ctrShowLoyaltyPointConversionValue($item, $LoyaltyConversionName);

		echo json_encode($answer);
    }

}
	if(isset($_POST["customer_id"])){

		$loyalty = new AjaxLoyalty();
		$loyalty -> customer_id = $_POST["customer_id"];
		$loyalty -> ajaxFetchPoints();

	}

    if(isset($_POST["LoyaltyConversionName"])){

		$loyalty = new AjaxLoyalty();
		$loyalty -> LoyaltyConversionName = $_POST["LoyaltyConversionName"];
		$loyalty -> ajaxFetchPointConversionValue();

	}