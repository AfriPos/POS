<?php
require_once 'connection.php';

class storeModel{
    /*=============================================
	ADDING STORE
	=============================================*/
	static public function mdlAddstore($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(store_id, store_name, store_address, contact_number, email, store_manager, opening, closing, logo, created_at) VALUES(:store_id, :store_name, :store_address, :contact_number, :email, :store_manager, :opening, :closing, :logo, :created_at)");

		$stmt->bindParam(":store_id", $data["store_id"],PDO::PARAM_STR);
		$stmt->bindParam(":store_name", $data["store_name"],PDO::PARAM_STR);
        $stmt->bindParam(":store_address", $data["store_address"],PDO::PARAM_STR);
        $stmt->bindParam(":contact_number", $data["contact_number"],PDO::PARAM_STR);
        $stmt->bindParam(":email", $data["email"],PDO::PARAM_STR);
        $stmt->bindParam(":store_manager", $data["store_manager"],PDO::PARAM_STR);
        $stmt->bindParam(":opening", $data["opening"],PDO::PARAM_STR);
        $stmt->bindParam(":closing", $data["closing"],PDO::PARAM_STR);
        $stmt->bindParam(":logo", $data["logo"],PDO::PARAM_STR);
        $stmt->bindParam(":created_at", $data["created_at"],PDO::PARAM_STR);

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
	EDITING STORE
	=============================================*/
	static public function mdlEditstore($table, $data){

		$stmt = connection::connect()->prepare("UPDATE $table SET store_id = :store_id, store_name= :store_name, store_address = :store_address, contact_number = :contact_number, email = :email, store_manager = :store_manager, opening = :opening, closing = :closing, logo = :logo WHERE store_id = :store_id");

		$stmt->bindParam(":store_id", $data["store_id"],PDO::PARAM_STR);
		$stmt->bindParam(":store_name", $data["store_name"],PDO::PARAM_STR);
        $stmt->bindParam(":store_address", $data["store_address"],PDO::PARAM_STR);
        $stmt->bindParam(":contact_number", $data["contact_number"],PDO::PARAM_STR);
        $stmt->bindParam(":email", $data["email"],PDO::PARAM_STR);
        $stmt->bindParam(":store_manager", $data["store_manager"],PDO::PARAM_STR);
        $stmt->bindParam(":opening", $data["opening"],PDO::PARAM_STR);
        $stmt->bindParam(":closing", $data["closing"],PDO::PARAM_STR);
        $stmt->bindParam(":logo", $data["logo"],PDO::PARAM_STR);

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
	SHOW STORE
	=============================================*/
	static public function mdlShowStores($table, $item, $value){

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
	DELETING STORE
	=============================================*/

	static public function mdlDeleteStore($table, $data){

		$stmt = connection::connect()->prepare("DELETE FROM $table WHERE store_id = :id");

		$stmt -> bindParam(":id", $data, PDO::PARAM_STR);

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