<?php

require_once "../controllers/categories.controller.php";
require_once "../models/categories.models.php";

class AjaxCategories{

	/*=============================================
	EDIT CATEGORY
	=============================================*/	

	public $idCategory;
	public $data;

	public function ajaxEditCategory(){

		$item = "id";
		$value = $this->idCategory;

		$answer = categoriesController::ctrShowCategories($item, $value);

		echo json_encode($answer);

	}
	public function ajaxShowCategories(){
		$item = $this->data['item'];
        $value = $this->data['value'];

		$answer = categoriesController::ctrShowCategories($item, $value);

		echo json_encode($answer);
	}
}

/*=============================================
EDITAR CATEGORÍA
=============================================*/	


    if (isset($_POST["item"]) && isset($_POST["value"])) {

        $categories = new AjaxCategories();
        $categories->data = array(
            'item' => $_POST["item"],
            'value' => $_POST["value"]
        );
        $categories->ajaxShowCategories();
    }

	
	if(isset($_POST["idCategory"])){

		$category = new AjaxCategories();
		$category -> idCategory = $_POST["idCategory"];
		$category -> ajaxEditCategory();
	}
