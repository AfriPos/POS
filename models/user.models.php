<?php
require_once "connection.php";

class userModel{

	/*=============================================
	SHOW USER 
	=============================================*/

	static public function mdlShowUser($tableUsers, $item, $value){

		if($item != null){

			$stmt = connection::connect()->prepare("SELECT * FROM $tableUsers WHERE $item = :value AND deleted = 0");

			$stmt -> bindParam(":value", $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

			$stmt -> closeCursor();

			$stmt = null;
	
		} else{

			$stmt = connection::connect()->prepare("SELECT * FROM $tableUsers WHERE deleted = 0");

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt -> closeCursor();

			$stmt = null;
			
		}

	}
	static public function mdlShowAllUser($tableUsers, $item, $value){

		$stmt = connection::connect()->prepare("SELECT * FROM $tableUsers WHERE $item = :$item");

		$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

		$stmt -> execute();

		return $stmt -> fetchAll();

		$stmt -> closeCursor();

		$stmt = null;

	}
	static public function mdlShowUsers($tableUsers, $item, $value, $role){
		if ($role != null) {

			$stmt = connection::connect()->prepare("SELECT * FROM $tableUsers WHERE $item = :$item AND deleted = 0 AND role != :role");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);
			$stmt -> bindParam(":role", $role, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt -> closeCursor();

			$stmt = null;
			
		} else {

			$stmt = connection::connect()->prepare("SELECT * FROM $tableUsers WHERE $item = :$item AND deleted = 0");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt -> closeCursor();

			$stmt = null;
			
		}

	}


	/*=============================================
	ADD USER 
	=============================================*/	

	static public function mdlCreateUser($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(name, username, userpassword, role, userphoto, store_id, email, organizationcode) VALUES (:name, :username, :userpassword, :role, :userphoto, :store_id, :email, :organizationcode)");

		$stmt -> bindParam(":name", $data["name"], PDO::PARAM_STR);
		$stmt -> bindParam(":username", $data["username"], PDO::PARAM_STR);
		$stmt -> bindParam(":userpassword", $data["userpassword"], PDO::PARAM_STR);
		$stmt -> bindParam(":role", $data["role"], PDO::PARAM_STR);
		$stmt -> bindParam(":userphoto", $data["userphoto"], PDO::PARAM_STR);
		$stmt -> bindParam(":store_id", $data["store_id"], PDO::PARAM_STR);
		$stmt -> bindParam(":email", $data["email"], PDO::PARAM_STR);
		$stmt -> bindParam(":organizationcode", $data["organizationcode"], PDO::PARAM_STR);

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
	EDIT USER 
	=============================================*/

	static public function mdlEditUser($table, $data){

		$stmt = connection::connect()->prepare("UPDATE $table set name = :name, userpassword = :userpassword, role = :role, store_id = :store_id, userphoto = :userphoto, deleted = :deleted, email=:email WHERE username = :username");

		$stmt -> bindParam(":name", $data["name"], PDO::PARAM_STR);
		$stmt -> bindParam(":username", $data["username"], PDO::PARAM_STR);
		$stmt -> bindParam(":userpassword", $data["userpassword"], PDO::PARAM_STR);
		$stmt -> bindParam(":role", $data["role"], PDO::PARAM_STR);
		$stmt -> bindParam(":store_id", $data["store_id"], PDO::PARAM_STR);
		$stmt -> bindParam(":userphoto", $data["userphoto"], PDO::PARAM_STR);
		$stmt -> bindParam(":deleted", $data["deleted"], PDO::PARAM_STR);
		$stmt -> bindParam(":email", $data["email"], PDO::PARAM_STR);

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
	UPDATE USER 
	=============================================*/

	static public function mdlUpdateUser($table, $item1, $value1, $item2, $value2){

		$stmt = connection::connect()->prepare("UPDATE $table set $item1 = :$item1 WHERE $item2 = :$item2");

		$stmt -> bindParam(":".$item1, $value1, PDO::PARAM_STR);
		$stmt -> bindParam(":".$item2, $value2, PDO::PARAM_STR);

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
	DELETE USER 
	=============================================*/	

	static public function mdlDeleteUser($table, $data){

		// $stmt = connection::connect()->prepare("DELETE FROM $table WHERE userId = :userId");

		// $stmt -> bindParam(":userId", $data, PDO::PARAM_STR);
		
		$stmt = connection::connect()->prepare("UPDATE $table SET deleted = :status WHERE userId = :userId");

		$stmt -> bindParam(":status", $data['status'], PDO::PARAM_INT);
		$stmt -> bindParam(":userId", $data['userId'], PDO::PARAM_STR);

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