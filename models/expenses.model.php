<?php
require_once "connection.php";
class ExpenseModel {
    /*=============================================
    ADDING EXPENSES
    =============================================*/
    static public function mdlAddExpense($table, $data) {
        $stmt = connection::connect()->prepare("INSERT INTO $table (store_id, expense, expense_type, date, amount, receipt) VALUES (:store_id, :expense, :expense_type, :date, :amount, :receipt)");

        $stmt->bindParam(":expense", $data["expense"], PDO::PARAM_STR);
        $stmt->bindParam(":expense_type", $data["expense_type"], PDO::PARAM_STR);
        $stmt->bindParam(":date", $data["date"], PDO::PARAM_STR);
        $stmt->bindParam(":amount", $data["amount"], PDO::PARAM_STR);
        $stmt->bindParam(":receipt", $data["receipt"], PDO::PARAM_STR);
		$stmt -> bindParam(":store_id", $data['storeid'], PDO::PARAM_STR);

		if ($stmt->execute()) {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'ok';
			
		} else {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'error';

		}
    }
     /*=============================================
    EDITING EXPENSES
    =============================================*/
    static public function mdlEditExpense($table, $data, $expenseId) {
        $stmt = connection::connect()->prepare("UPDATE $table SET expense = :expense, expense_type = :expense_type, date = :date, amount = :amount, receipt = :receipt WHERE id = :expenseId");
        
        $stmt->bindParam(":expense", $data["expense"], PDO::PARAM_STR);
        $stmt->bindParam(":expense_type", $data["expense_type"], PDO::PARAM_STR);
        $stmt->bindParam(":date", $data["date"], PDO::PARAM_STR);
        $stmt->bindParam(":amount", $data["amount"], PDO::PARAM_STR);
        $stmt->bindParam(":receipt", $data["receipt"], PDO::PARAM_STR);
        $stmt->bindParam(":expenseId", $expenseId, PDO::PARAM_INT);

		if ($stmt->execute()) {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'ok';
			
		} else {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'error';

		}
    }
    /*=============================================
    DELETING EXPENSES
    =============================================*/
    static public function mdlDeleteExpense($table, $expenseId) {

        $stmt = connection::connect()->prepare("DELETE FROM $table WHERE id = :expenseId");

        $stmt->bindParam(":expenseId", $expenseId, PDO::PARAM_INT);

		if ($stmt->execute()) {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'ok';
			
		} else {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'error';

		}

    }

    /*=============================================
    GETTING EXPENSE FILENAME
    =============================================*/
    static public function mdlGetExpenseFilename($table, $expenseId) {
        $stmt = connection::connect()->prepare("SELECT receipt FROM $table WHERE id = :expenseId");
        $stmt->bindParam(":expenseId", $expenseId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['receipt'];

        $stmt -> closeCursor();

        $stmt = null;
    }

 
    /*=============================================
    SHOW EXPENSES
    =============================================*/
    static public function mdlShowExpenses($table,$item, $value) {

		if ($item == "store_id"){ 

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt -> closeCursor();

			$stmt = null;
			
		} elseif($item != null){

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

			$stmt -> closeCursor();

			$stmt = null;

		}
		else{
			$stmt = connection::connect()->prepare("SELECT * FROM $table");

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt -> closeCursor();

			$stmt = null;

		}

    }

    /*=============================================
    ADD EXPENSE TYPES
    =============================================*/

    public static function mdlAddExpenseType($table, $data) {
        $stmt = connection::connect()->prepare("INSERT INTO $table (type) VALUES (:expensetype)");

        $stmt->bindParam(":expensetype", $data["expensetype"], PDO::PARAM_STR);

		if ($stmt->execute()) {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'ok';
			
		} else {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'error';

		}
        
    }

}
