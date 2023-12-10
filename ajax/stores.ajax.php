<?php

require_once "../controllers/store.controller.php";
require_once "../models/store.model.php";


class AjaxStore{
    public $Storeid;
	
	public function ajaxEditStore(){
		// Get product by id
		$item = "store_id";
		$value = $this->Storeid;
		$answer = storeController::ctrShowStores($item, $value);
	
		echo json_encode($answer[0]);
	}

}

if(isset($_POST["Storeid"])){

    $editStore = new AjaxStore();
    $editStore -> Storeid = $_POST["Storeid"];
    $editStore -> ajaxEditStore();

}