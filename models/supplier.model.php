<?php
require_once 'connection.php';

class supplierModel{
    /*=============================================
	ADDING SUPPLIERS
	=============================================*/
	static public function mdlAddsSupplier($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(name,address, email, contact, store_id) VALUES(:name, :address, :email, :contact, :store_id)");

		$stmt->bindParam(":name", $data["name"],PDO::PARAM_STR);
		$stmt->bindParam(":address", $data["address"],PDO::PARAM_STR);
        $stmt->bindParam(":email", $data["email"],PDO::PARAM_STR);
        $stmt->bindParam(":contact", $data["contact"],PDO::PARAM_STR);
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
	SHOW SUPPLIERS
	=============================================*/
	static public function mdlShowSuppliers($table, $item, $value){

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
	EDITING SUPPLIERS
	=============================================*/
	static public function mdlEditSupplier($table, $data){

		$stmt = connection::connect()->prepare("UPDATE $table SET name = :name, address= :address, email = :email, contact = :contact WHERE supplierid = :id");

		$stmt->bindParam(":name", $data["name"], PDO::PARAM_STR);
		$stmt->bindParam(":address", $data["address"], PDO::PARAM_STR);
		$stmt->bindParam(":email", $data["email"],PDO::PARAM_STR);
        $stmt->bindParam(":contact", $data["contact"],PDO::PARAM_STR);
        $stmt->bindParam(":id", $data["supplierid"],PDO::PARAM_INT);

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
	DELETING SUPPLIERS
	=============================================*/

	static public function mdlDeleteSupplier($table, $data){

		$stmt = connection::connect()->prepare("DELETE FROM $table WHERE supplierid = :id");

		$stmt -> bindParam(":id", $data, PDO::PARAM_INT);

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