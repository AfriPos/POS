<?php

require_once "connection.php";

class LoyaltyModel{
	/*=============================================
	SHOW LOYALTY VALUE
	=============================================*/

	static public function mdlShowLoyaltyValue($table){

        $stmt = connection::connect()->prepare("SELECT * FROM $table");

        $stmt -> execute();

        return $stmt -> fetchAll();

        $stmt -> closeCursor();

        $stmt = null;

    }

	/*=============================================
	ADD LOYALTY POINTs
	=============================================*/

	static public function mdlAddLoyaltyPoints($table, $data){

        $stmt = connection::connect()->prepare("INSERT INTO `$table` (pointId, customer_id, PointsEarned, PointsRedeemed) VALUES (:loyaltyid, :customer_id, :PointsEarned, :PointsRedeemed)");

        $stmt->bindParam(':loyaltyid', $data['loyaltyid']);
        $stmt->bindParam(':customer_id', $data['customer_id']);
        $stmt->bindParam(':PointsEarned', $data['PointsEarned']);
        $stmt->bindParam(':PointsRedeemed', $data['PointsRedeemed']);

		if ($stmt->execute()) {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return true;
			
		} else {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return false;

		}

    }


	/*=============================================
	SHOW LOYALTY POINTs
	=============================================*/
	static public function mdlShowLoyaltyPoints($table, $item, $value){

        $stmt = connection::connect()->prepare("SELECT * FROM `$table` WHERE $item =:$item");

		$stmt->bindParam(':'.$item, $value, PDO::PARAM_INT);

        $stmt -> execute();

        return $stmt -> fetch();

        $stmt -> closeCursor();

        $stmt = null;

    }
    static public function mdlShowAllLoyaltyPoints($table, $item, $value){

        $stmt = connection::connect()->prepare("SELECT * FROM `$table` WHERE $item =:$item");

		$stmt->bindParam(':'.$item, $value, PDO::PARAM_INT);

        $stmt -> execute();

        return $stmt -> fetchAll();

        $stmt -> closeCursor();

        $stmt = null;

    }

	/*=============================================
	SHOW LOYALTY POINT CONVERSION VALUE
	=============================================*/
	static public function mdlShowLoyaltyPointConversionValue($table, $item, $value){

        $stmt = connection::connect()->prepare("SELECT * FROM `$table` WHERE $item =:$item");

		$stmt->bindParam(':'.$item, $value, PDO::PARAM_STR);

        $stmt -> execute();

        return $stmt -> fetch();

        $stmt -> closeCursor();

        $stmt = null;

	}

	/*=============================================
	CHANGE LOYALTY SETTINGS
	=============================================*/
	static public function mdlchangeLoyaltySettings($table, $data){

		$stmt = connection::connect()->prepare("UPDATE loyaltysettings SET SettingValue = :value WHERE SettingName = :item");

		// Update the first row
		$item1 = "LoyaltyPointValue";
		$value1 = $data['LoyaltyPointValue'];
		$stmt->bindParam(':value', $value1, PDO::PARAM_INT);
		$stmt->bindParam(':item', $item1, PDO::PARAM_STR);
		if ($stmt->execute()) {
		
			// Update the second row
			$item2 = "LoyaltyValueConversion";
			$value2 = $data['LoyaltyValueConversion'];
			$stmt->bindParam(':value', $value2, PDO::PARAM_INT);
			$stmt->bindParam(':item', $item2, PDO::PARAM_STR);
			if ($stmt->execute()) {
				return "ok";
			} else {
				return "Error";
			}
		} else {
			return "Error";
	}
		
		$stmt->closeCursor();
		$stmt = null;
		

	}

}
