<?php
require_once "../controllers/expenses.controller.php";
require_once "../models/expenses.model.php";

class AjaxExpenses{

	/*=============================================
	EDIT expenses
	=============================================*/	

	public $expenseId;

	public function ajaxEditExpenses(){
		$item = "id";
		$value = $this->expenseId;

		$expenseController = new ExpenseController();
		$answer = $expenseController->ctrShowExpenses($item, $value);
		echo json_encode($answer);

	}
}

/*=============================================
EDIT EXPENSES
=============================================*/	
if(isset($_POST["expenseId"])){

	$expenses = new AjaxExpenses();
	$expenses->expenseId = $_POST["expenseId"];
	$expenses->ajaxEditExpenses();
}
?>
