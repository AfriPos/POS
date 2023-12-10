<?php


require_once "connection.php";

class CategoriesModel{

	/*=============================================
	CREATE CATEGORY
	=============================================*/

	static public function mdlAddCategory($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(category, store_id) VALUES (:category, :store_id)");

		$stmt -> bindParam(":category", $data['category'], PDO::PARAM_STR);
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
	SHOW CATEGORY 
	=============================================*/
	
	static public function mdlShowCategories($table, $item, $value){

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
	EDIT CATEGORY
	=============================================*/

	static public function mdlEditCategory($table, $data){

		$stmt = connection::connect()->prepare("UPDATE $table SET Category = :Category WHERE id = :id");

		$stmt -> bindParam(":Category", $data["Category"], PDO::PARAM_STR);
		$stmt -> bindParam(":id", $data["id"], PDO::PARAM_INT);

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
	DELETE CATEGORY
	=============================================*/

	static public function mdlDeleteCategory($table, $data){

		$stmt = connection::connect()->prepare("DELETE FROM $table WHERE id = :Id");

		$stmt -> bindParam(":Id", $data, PDO::PARAM_INT);

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