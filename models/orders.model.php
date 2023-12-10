<?php


require_once "connection.php";

class OrdersModel{

    /*=============================================
	CUSTOM QUERY
	=============================================*/
	static public function ctrCustomQuery($query, $params = array()){
		$stmt = connection::connect()->prepare($query);
	
		if ($stmt->execute($params)) {
	
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
			// Close the statement and set it to null
			$stmt->closeCursor();
			$stmt = null;
	
			return $result;
	
		} else {
	
			// Close the statement and set it to null
			$stmt->closeCursor();
			$stmt = null;
	
			return false; // or handle the error as needed
	
		}
	}
    /*=============================================
	CREATE BATCH
	=============================================*/	
	static public function mdlCreateBatch($table, $data){

		session_start();
		$stmt = connection::connect()->prepare("INSERT INTO $table(batch_id, quantity, store_id, product_id, order_id, datecreated) VALUES(:batch_id, :quantity, :store_id, :product_id, :order_id, :datecreated)");

		$stmt->bindParam(":batch_id", $data["batch_id"],PDO::PARAM_STR);
		$stmt->bindParam(":quantity", $data["quantity"],PDO::PARAM_INT);
		$stmt->bindParam(":order_id", $data["orderId"],PDO::PARAM_STR);
		$stmt -> bindParam(":store_id", $_SESSION['storeid'], PDO::PARAM_STR);
		$stmt->bindParam(":product_id", $data["product_id"],PDO::PARAM_STR);
		$stmt->bindParam(":datecreated", $data["datecreated"],PDO::PARAM_STR);

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
	CREATE BATCH ITEMS
	=============================================*/
	static public function mdlAddBatchitems($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(batch_id, manufacturing_date, expiry_date, serialNumber) VALUES(:batch_id, :manufacturing_date, :expiry_date, :serialNumber)");

		$stmt->bindParam(":batch_id", $data["batch_id"],PDO::PARAM_STR);
		$stmt->bindParam(":serialNumber", $data["serial_number"],PDO::PARAM_STR);
		$stmt->bindParam(":manufacturing_date", $data["manufacturing_date"],PDO::PARAM_STR);
		$stmt -> bindParam(":expiry_date", $data['expiry_date'], PDO::PARAM_STR);

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
	CREATE ORDER
	=============================================*/

	static public function mdlAddOrder($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(orderid, supplier, date, total, status, store_id) VALUES(:orderid, :supplier, :date, :total, :status, :store_id)");

		$stmt->bindParam(":orderid", $data["orderid"],PDO::PARAM_STR);
		$stmt->bindParam(":supplier", $data["supplier"],PDO::PARAM_STR);
		$stmt->bindParam(":total", $data["total"],PDO::PARAM_STR);
		$stmt->bindParam(":status", $data["status"],PDO::PARAM_INT);
		$stmt -> bindParam(":store_id", $data['storeid'], PDO::PARAM_STR);
		$stmt -> bindParam(":date", $data['date'], PDO::PARAM_STR);

		if ($stmt->execute()) {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return $data["orderid"];
			
		} else {

			// Close the statement and set it to null
			$stmt->closeCursor();

			$stmt = null;

			return 'error';

		}
		
	}
    /*=============================================
	ADD ORDER ITEMS
	=============================================*/
	static public function mdlOrderitems($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table (order_id, product_id, quantity) VALUES(:order_id, :product_id, :quantity)");

		$stmt->bindParam(":order_id", $data["order_id"],PDO::PARAM_STR);
		$stmt->bindParam(":product_id", $data["product_id"],PDO::PARAM_STR);
		$stmt->bindParam(":quantity", $data["quantity"],PDO::PARAM_INT);

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
	SHOW BATCH DETAILS
	=============================================*/
	
    static public function mdlShowBatch($table, $item, $value, $options){

		if ($options == "duplicates") {
			
			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item[0] = :item1 AND $item[1] = :item2");

			$stmt -> bindParam(":item1", $value[0], PDO::PARAM_STR);
			$stmt -> bindParam(":item2", $value[1], PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

			$stmt->closeCursor();

			$stmt = null;

		} elseif($options == null){
			if ($item == "store_id"){ 

				$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");
	
				$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);
	
				$stmt -> execute();
	
				return $stmt -> fetchAll();
				
			} elseif($item != null){
	
				$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");
	
				$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);
	
				$stmt -> execute();
	
				return $stmt -> fetchAll();
	
				$stmt->closeCursor();
	
				$stmt = null;
				
			}
			else{
				$stmt = connection::connect()->prepare("SELECT * FROM $table");
	
				$stmt -> execute();
	
				return $stmt -> fetchAll();
	
				$stmt->closeCursor();
	
				$stmt = null;
				
			}
		} else {
			
		}
		

	}
    /*=============================================
	SHOW ORDERS
	=============================================*/
	
    static public function mdlShowOrders($table, $item, $value){

		if ($item == "store_id"){ 

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();
			
		} elseif($item != null){

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

			$stmt->closeCursor();

			$stmt = null;
			
		}
		else{
			$stmt = connection::connect()->prepare("SELECT * FROM $table");

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt->closeCursor();

			$stmt = null;
			
		}
	}

	/*=============================================
	SHOW ORDER ITEMS
	=============================================*/
	
    static public function mdlShowOrderitems($table, $item, $value, $fetchAll=false){

		if ($fetchAll) {

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt->closeCursor();

			$stmt = null;

		}elseif($item != null){

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

			$stmt->closeCursor();

			$stmt = null;
			
		}
		else{
			$stmt = connection::connect()->prepare("SELECT * FROM $table");

			$stmt -> execute();

			return $stmt -> fetchAll();

			$stmt->closeCursor();

			$stmt = null;
			
		}
	}

    /*=============================================
    EDIT ORDERS
    =============================================*/

	static public function mdlEditOrder($table, $data){

		$stmt = connection::connect()->prepare("UPDATE $table SET status = :status WHERE orderid = :id");

		$stmt->bindParam(":status", $data["status"], PDO::PARAM_STR);
		$stmt->bindParam(":id", $data["id"],PDO::PARAM_STR);

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