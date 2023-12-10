<?php

require_once "connection.php";
class activitylogModel{

    /*=============================================
    CREATE ACTIVITYLOG
    =============================================*/
    public static function mdlCreateActivityLog($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(UserID, ActivityType, ActivityDescription, itemID, store_id, TimeStamp) VALUES (:UserID, :ActivityType, :ActivityDescription, :itemID, :storeid, :TimeStamp)");

		$stmt -> bindParam(":UserID", $data['UserID'], PDO::PARAM_STR);
		$stmt -> bindParam(":ActivityType", $data['ActivityType'], PDO::PARAM_STR);
		$stmt -> bindParam(":ActivityDescription", $data['ActivityDescription'], PDO::PARAM_STR);
		$stmt -> bindParam(":itemID", $data['itemID'], PDO::PARAM_STR);
		$stmt -> bindParam(":storeid", $data['storeid'], PDO::PARAM_STR);
		$stmt -> bindParam(":TimeStamp", $data['TimeStamp'], PDO::PARAM_STR);

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
    FETCH ACTIVITYLOG
    =============================================*/
	public static function mdlFetchActivityLog($table, $item, $value){

		if($item != null){

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

			$stmt -> closeCursor();

			$stmt = null;

		}
		else{
			$stmt = connection::connect()->prepare("SELECT * FROM $table ORDER BY Timestamp DESC;");

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt -> closeCursor();

			$stmt = null;

			
		}

	}

}