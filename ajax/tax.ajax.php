<?php

require_once "../controllers/tax.controller.php";
require_once "../models/tax.models.php";

class Ajaxtax{

	/*=============================================
	EDIT CATEGORY
	=============================================*/	

	public $idtax;

	public function ajaxEditTax(){

		$item = "taxId";
		$valor = $this->idtax;

		$answer = taxController::ctrShowTax($item, $valor);

		echo json_encode($answer);

	}
}

/*=============================================
EDITAR CATEGORÍA
=============================================*/	
if(isset($_POST["idtax"])){

	$tax = new Ajaxtax();
	$tax -> idtax = $_POST["idtax"];
	$tax -> ajaxEditTax();
}
