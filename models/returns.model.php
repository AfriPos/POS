<?php
class ReturnProductModel {
    /*=============================================
    ADDING RETURN PRODUCTS
    =============================================*/
    static public function mdlAddReturnProduct($table, $data) {
        $stmt = Connection::connect()->prepare("INSERT INTO $table (product, quantity, return_date, reason, return_type, supplier, store_id) VALUES (:product, :quantity, :return_date, :reason, :return_type, :supplier, :store_id)");

        $stmt->bindParam(":product", $data["product"], PDO::PARAM_STR);
        $stmt->bindParam(":quantity", $data["quantity"], PDO::PARAM_INT);
        $stmt->bindParam(":return_date", $data["return_date"], PDO::PARAM_STR);
        $stmt->bindParam(":supplier", $data["supplier"], PDO::PARAM_STR);
        $stmt->bindParam(":reason", $data["reason"], PDO::PARAM_STR);
        $stmt->bindParam(":return_type", $data["return_type"], PDO::PARAM_STR);
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
    SHOW RETURNS
    =============================================*/
    static public function mdlShowReturnProducts($table, $item, $value) {

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
