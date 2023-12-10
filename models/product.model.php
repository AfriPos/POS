<?php
require_once 'connection.php';

class productModel{
    /*=============================================
	ADDING PRODUCT
	=============================================*/
	static public function mdlAddProduct($table, $data){

		$stmt = connection::connect()->prepare("INSERT INTO $table(id, barcode,idCategory, product, description, stock, purchaseprice, saleprice, image ,taxId, status, store_id) VALUES(:productid, :barcode, :idCategory, :product,  :description, :stock, :purchaseprice, :saleprice, :image , :taxId, :status, :store_id)");

		$stmt->bindParam(":productid", $data["productid"],PDO::PARAM_STR);
		$stmt->bindParam(":barcode", $data["barcode"],PDO::PARAM_STR);
		$stmt->bindParam(":idCategory", $data["idCategory"],PDO::PARAM_STR);
        $stmt->bindParam(":product", $data["product"],PDO::PARAM_STR);
        $stmt->bindParam(":description", $data["description"],PDO::PARAM_STR);
        $stmt->bindParam(":stock", $data["stock"],PDO::PARAM_STR);
        $stmt->bindParam(":purchaseprice", $data["purchaseprice"],PDO::PARAM_STR);
        $stmt->bindParam(":saleprice", $data["saleprice"],PDO::PARAM_STR);
        $stmt->bindParam(":image", $data["image"],PDO::PARAM_STR);
        $stmt->bindParam(":taxId", $data["taxId"],PDO::PARAM_STR);
        $stmt->bindParam(":status", $data["status"],PDO::PARAM_INT);
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
	SHOW PRODUCT
	=============================================*/
	static public function mdlFetchAllProducts($table, $item, $value, $order){

		if ($item === 'store_id'){
			// $products2 = productModel::mdlShowProducts('products_table', 'storeid', $store_id);

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE store_id = :storeid AND status = 0 ORDER BY $order DESC");

			$stmt -> bindParam(":storeid", $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();
		
			$stmt -> closeCursor();
	
			$stmt = null;
			
		} elseif($item !== null ){

			// $products1 = productModel::mdlShowProducts('products_table', 'item_name', 'item_value');

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :value ORDER BY $order DESC");

			$stmt -> bindParam(":value", $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();
		
			$stmt -> closeCursor();
	
			$stmt = null;

		} elseif ($item === 'status') {
			// $products3 = productModel::mdlShowProducts('products_table', 'status', 0);

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE status = :status ORDER BY $order DESC");

			$stmt -> bindParam(":status", $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();
		
			$stmt -> closeCursor();
	
			$stmt = null;
			
		}

	}
	static public function mdlFetchProducts($table, $item, $value, $order) {

		if ($item !== null) {

			$status = 0; // Set the status value to 0

			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE status = :status AND $item = :value  ORDER BY $order DESC");
			
			$stmt->bindValue(":value", $value, PDO::PARAM_INT);
			$stmt->bindValue(":status", $status, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		
			$stmt -> closeCursor();
	
			$stmt = null;
				
		} else {
			
			$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :value");

			$stmt -> bindParam(":value", $value, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();
		
			$stmt -> closeCursor();
	
			$stmt = null;

		}
		
	}


	// static public function mdlShowProducts($table, $item, $value, $order){

	// 	if ($item == "store_id"){ 

	// 		$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item AND status = 0");

	// 		$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

	// 		$stmt -> execute();

	// 		return $stmt -> fetchAll();
			
	// 	} elseif($item != null){

	// 		$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item AND status = 0");

	// 		$stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

	// 		$stmt -> execute();

	// 		return $stmt -> fetch();

	// 	} else{
	// 		$status = 0; // Set the status value to 0

	// 		$stmt = connection::connect()->prepare("SELECT * FROM $table WHERE status = :status ORDER BY $order DESC");

	// 		$stmt->bindValue(":status", $status, PDO::PARAM_INT);

	// 		$stmt->execute();

	// 		return $stmt->fetchAll();

	// 	}

	// 	$stmt -> close();

	// 	$stmt = null;

	// }
	
	// static public function mdlShowAllProducts($table, $item, $value){

	// 	// $stmt = connection::connect()->prepare("SELECT * FROM $table WHERE $item = :$item");

	// 	// $stmt -> bindParam(":".$item, $value, PDO::PARAM_STR);

	// 	// $stmt -> execute();

	// 	// return $stmt -> fetch();
		
	// 	// $stmt -> close();

	// 	// $stmt = null;

	// }
	/*=============================================
	EDITING PRODUCT
	=============================================*/
	static public function mdlEditProduct($table, $data){

		$stmt = connection::connect()->prepare("UPDATE $table SET idCategory = :idCategory, product= :product, description = :description, image = :image, stock = :stock, purchaseprice = :purchaseprice, saleprice = :saleprice, status = :status, taxId = :taxId WHERE barcode = :barcode  AND store_id = :store_id");

		$stmt->bindParam(":idCategory", $data["idCategory"], PDO::PARAM_INT);
		$stmt->bindParam(":barcode", $data["barcode"], PDO::PARAM_STR);
		$stmt->bindParam(":product", $data["product"],PDO::PARAM_STR);
        $stmt->bindParam(":description", $data["description"],PDO::PARAM_STR);
        $stmt->bindParam(":stock", $data["stock"],PDO::PARAM_STR);
        $stmt->bindParam(":purchaseprice", $data["purchaseprice"],PDO::PARAM_STR);
        $stmt->bindParam(":saleprice", $data["saleprice"],PDO::PARAM_STR);
        $stmt->bindParam(":image", $data["image"],PDO::PARAM_STR);
        $stmt->bindParam(":status", $data["status"],PDO::PARAM_INT);
		$stmt -> bindParam(":store_id", $data['storeid'], PDO::PARAM_STR);
		$stmt -> bindParam(":taxId", $data['taxcat'], PDO::PARAM_STR);

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
	DELETING PRODUCT
	=============================================*/

	static public function mdlDeleteProduct($table, $data){

		// $stmt = connection::connect()->prepare("DELETE FROM $table WHERE barcode = :id");

		// $stmt -> bindParam(":id", $data, PDO::PARAM_INT);

		$stmt = connection::connect()->prepare("UPDATE $table SET status = :status WHERE id = :barcode");

		$stmt -> bindParam(":status", $data['status'], PDO::PARAM_STR);
		$stmt -> bindParam(":barcode", $data['barcode'], PDO::PARAM_INT);

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
	UPDATE PRODUCT
	=============================================*/

	static public function mdlUpdateProduct($table, $item1, $value1, $value){

		$stmt = Connection::connect()->prepare("UPDATE $table SET $item1 = :$item1 WHERE id = :id");

		$stmt -> bindParam(":".$item1, $value1, PDO::PARAM_STR);
		$stmt -> bindParam(":id", $value, PDO::PARAM_STR);

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
	SHOW ADDING OF THE SALES
	=============================================*/	

	static public function mdlAddingTotalSales($table){

		$stmt = connection::connect()->prepare("SELECT SUM(sales) as total FROM $table");

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> closeCursor();

		$stmt = null;

	}

	/*=============================================
	Adding to stock
	=============================================*/	

	static public function mdlAddingStock($table ,$quantity, $barcode){

		$stmt = connection::connect()->prepare("UPDATE $table SET stock = stock + :quantity WHERE id = :barcode");
		
		$stmt -> bindParam(":quantity", $quantity, PDO::PARAM_INT);
		$stmt -> bindParam(":barcode", $barcode, PDO::PARAM_INT);

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
?>