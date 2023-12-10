<?php

require_once "connection.php";

class customerModel{
    
    /*=============================================
	CREATE CUSTOMER
	=============================================*/

	static public function mdlCreateCustomer($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(name, address, phone, email, store_id) VALUES (:name, :address, :phone, :email, :store_id)");

		$stmt -> bindParam(":name", $data['CustomerName'], PDO::PARAM_STR);
		$stmt -> bindParam(":address", $data['CustomerAddress'], PDO::PARAM_STR);
		$stmt -> bindParam(":phone", $data['contactNumber'], PDO::PARAM_STR);
		$stmt -> bindParam(":email", $data['CustomerEmail'], PDO::PARAM_STR);
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
	EDIT CUSTOMER
	=============================================*/

	static public function mdlEditCustomer($table, $data){

		$stmt = connection::connect()->prepare("UPDATE $table SET name = :name, address = :address, phone = :phone, email = :email WHERE customer_id = :customer_id");

		$stmt -> bindParam(":name", $data['name'], PDO::PARAM_STR);
		$stmt -> bindParam(":address", $data['address'], PDO::PARAM_STR);
		$stmt -> bindParam(":phone", $data['phone'], PDO::PARAM_STR);
		$stmt -> bindParam(":email", $data['email'], PDO::PARAM_STR);
		$stmt -> bindParam(":customer_id", $data['customer_id'], PDO::PARAM_STR);

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
	SHOW CUSTOMER
	=============================================*/
	static public function mdlShowCustomers($table, $item, $value){

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
}