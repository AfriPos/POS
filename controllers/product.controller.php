<?php

class productController {

	/*=============================================
   SET THE STORE ID
   =============================================*/
	
    static private $storeid;

	public static function initialize() {
		// Start a session if it hasn't already been started
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	
		if (isset($_SESSION['storeid']) && $_SESSION['storeid'] != null) {
			self::$storeid = $_SESSION['storeid'];
		} else {
			echo "<script>
				window.onload = function() {
					Swal.fire({
						title: 'No store is selected',
						text: 'Redirecting to Dashboard',
						icon: 'error',
						showConfirmButton: false,
						timer: 2000 // Display alert for 2 seconds
					}).then(function() {
						// After the alert is closed, redirect to the dashboard
						window.location= 'dashboard';
					});
				};
				</script>";
			exit; // Adding exit to stop further execution after the redirection
		}
	}

	/*=============================================
   CREATE PRODUCT
   =============================================*/

    static public function ctrCreateProducts() {
        self::initialize();
		// fetch all products from the database
		$table = "products";
		$item = 'status';
		$value = 0;
		$order = 'id';
		$Allproducts = productModel::mdlFetchAllProducts($table, $item, $value, $order);
		
		// Count the number of products array
		$numAllproducts = count($Allproducts);
		$validatetable = "customers";
		$element = "product";
		$organisationcode = $_SESSION['organizationcode'];
		$response = packagevalidateController::ctrPackageValidate($element, $validatetable, $numAllproducts, $organisationcode);
		
		if(isset($_POST['addproduct'])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
						
			if ($response) {

				if(preg_match('/^[0-9]+$/', $_POST["txtstock"]) &&	
				preg_match('/^[0-9.]+$/', $_POST["txtpurchase"]) &&
				preg_match('/^[0-9.]+$/', $_POST["txtsale"])){

					/*=============================================
					VALIDATE IMAGE
					=============================================*/

					$route = "views/img/products/default/anonymous.png";
					
					$randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
					$timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
					$current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
					$current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
					$productId = $randomNumber . "-" . $current_time_formatted;

					if (isset($_FILES["txtproductimage"]["tmp_name"])) {

						list($width, $height) = getimagesize($_FILES["txtproductimage"]["tmp_name"]);
					
						$newWidth = 500;
						$newHeight = 500;
					
						/*=============================================
						we create the folder to save the picture if it doesn't exist
						=============================================*/
					
						$folder = "views/img/products/" . $_POST["txtbarcode"];
					
						if (!is_dir($folder)) {
							mkdir($folder, 0755);
						}
					
						/*=============================================
						WE APPLY DEFAULT PHP FUNCTIONS ACCORDING TO THE IMAGE FORMAT
						=============================================*/
					
						if ($_FILES["txtproductimage"]["type"] == "image/jpeg") {
					
							/*=============================================
							WE SAVE THE IMAGE IN THE FOLDER
							=============================================*/
					
							$random = mt_rand(100, 999);
					
							$route = "views/img/products/" . $_POST["txtbarcode"] . "/" . $random . ".jpg";
					
							$origin = imagecreatefromjpeg($_FILES["txtproductimage"]["tmp_name"]);
					
							$destiny = imagecreatetruecolor($newWidth, $newHeight);
					
							imagecopyresized($destiny, $origin, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					
							imagejpeg($destiny, $route);
						}
					
						if ($_FILES["txtproductimage"]["type"] == "image/png") {
					
							/*=============================================
							WE SAVE THE IMAGE IN THE FOLDER
							=============================================*/
					
							$random = mt_rand(100, 999);
					
							$route = "views/img/products/" . $_POST["txtbarcode"] . "/" . $random . ".png";
					
							$origin = imagecreatefrompng($_FILES["txtproductimage"]["tmp_name"]);
					
							$destiny = imagecreatetruecolor($newWidth, $newHeight);
					
							imagecopyresized($destiny, $origin, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					
							imagepng($destiny, $route);
						}
					}
					

					$data = array("productid" => $productId,
									"barcode" => $_POST["txtbarcode"],
									"product" => $_POST["txtproductname"],
									"idCategory" => $_POST["txtcategory"],
									"description" => $_POST["txtdescription"],
									"stock" => $_POST["txtstock"],
									"purchaseprice" => $_POST["txtpurchase"],
									"saleprice" => $_POST["txtsale"],
									"image" => $route,
									"taxId" => $_POST["txttaxcat"],
									"status" => 0,
									"storeid" => self::$storeid);

				
					$item = "barcode";
					$value = $_POST["txtbarcode"];
					$order="id";
					$SelectedProducts = productModel::mdlFetchProducts($table, $item, $value, $order);

					if ($SelectedProducts && $SelectedProducts['status'] == 1 ) {
						$answer = productModel::mdlEditProduct($table, $data);
					} else {
						$answer = productModel::mdlAddProduct($table, $data);
					}
					if($answer == "ok"){
						if ($_SESSION['userId'] != 404) {
							// Create an array with the data for the activity log entry
							$logdata = array(
								'UserID' => $_SESSION['userId'],
								'ActivityType' => 'Product',
								'ActivityDescription' => 'User ' . $_SESSION['username'] . ' added product ' .$data['product']. '.',
								'storeid' => self::$storeid,
								'TimeStamp' => $dateTimeStr
							);
							// Call the ctrCreateActivityLog() function
							activitylogController::ctrCreateActivityLog($logdata);
						}

						echo'<script>

						Swal.fire({
								icon: "success",
								title: "Product '.$data['product'].' has been added to the inventory list.",
								showConfirmButton: false,
								timer: 2000 // Auto close after 2 seconds
							}).then(function () {
								// Code to execute after the alert is closed
								window.location = "products";
							});

							</script>';

					}

				}else{

					echo'<script>

					Swal.fire({
						icon: "error",
						title: "Invalid parameters passed!",
						showConfirmButton: false,
						timer: 2000  // 2 seconds
					}).then(function(result) {
						// This function will be called when the alert is closed
						window.location = "products";
					});
					

					</script>';
				}

			} else {

				echo'<script>
				
				Swal.fire({
					icon: "warning",
					title: "Cannot Add Product",
					text: "You cannot add more products with your current package. Consider upgrading your package.",
					button: "OK"
				});

				</script>';

			}

		}

	}
	/*=============================================
	SHOW PRODUCTS
	=============================================*/

	static public function ctrShowProducts($item, $value, $order, $fetchAll = false){

		$table = "products";

		if ($fetchAll) {
			$answer = productModel::mdlFetchAllProducts($table, $item, $value, $order);
		} else {
			$answer = productModel::mdlFetchProducts($table, $item, $value, $order);
		}
		

		// $answer = productModel::mdlShowProducts($table, $item, $value, $order);

		return $answer;
		
	}

	
	/*=============================================
	EDIT PRODUCT
	=============================================*/

	static public function ctrEditProduct(){
        self::initialize();

		if(isset($_POST["editbarcode"])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
			
			if(preg_match('/^[0-9]+$/', $_POST["editstock"]) &&	
			   preg_match('/^[0-9.]+$/', $_POST["editpurchaseprice"]) &&
			   preg_match('/^[0-9.]+$/', $_POST["editsaleprice"])){

		   		/*=============================================
				VALIDATE IMAGE
				=============================================*/

			   	$route = $_POST["currentImage"];

			   	if(isset($_FILES["editImage"]["tmp_name"]) && !empty($_FILES["editImage"]["tmp_name"])){

					list($width, $height) = getimagesize($_FILES["editImage"]["tmp_name"]);

					$newWidth = 500;
					$newHeight = 500;

					/*=============================================
					WE CREATE THE FOLDER WHERE WE WILL SAVE THE PRODUCT IMAGE
					=============================================*/

					$folder = "views/img/products/".$_POST["editbarcode"];

					/*=============================================
					WE ASK IF WE HAVE ANOTHER PICTURE IN THE DB
					=============================================*/

					if(!empty($_POST["currentImage"]) && $_POST["currentImage"] != "views/img/products/default/anonymous.png"){

						unlink($_POST["currentImage"]);

					}else{

						mkdir($folder, 0755);	
					
					}
					
					/*=============================================
					WE APPLY DEFAULT PHP FUNCTIONS ACCORDING TO THE IMAGE FORMAT
					=============================================*/

					if($_FILES["editImage"]["type"] == "image/jpeg"){

						/*=============================================
						WE SAVE THE IMAGE IN THE FOLDER
						=============================================*/

						$random = mt_rand(100,999);

						$route = "views/img/products/".$_POST["editbarcode"]."/".$random.".jpg";

						$origin = imagecreatefromjpeg($_FILES["editImage"]["tmp_name"]);						

						$destiny = imagecreatetruecolor($newWidth, $newHeight);

						imagecopyresized($destiny, $origin, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

						imagejpeg($destiny, $route);

					}

					if($_FILES["editImage"]["type"] == "image/png"){

						/*=============================================
						WE SAVE THE IMAGE IN THE FOLDER
						=============================================*/

						$random = mt_rand(100,999);

						$route = "views/img/products/".$_POST["editbarcode"]."/".$random.".png";

						$origin = imagecreatefrompng($_FILES["editImage"]["tmp_name"]);

						$destiny = imagecreatetruecolor($newWidth, $newHeight);

						imagecopyresized($destiny, $origin, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

						imagepng($destiny, $route);

					}

				}

				$table = "products";
				$status = 0;

				$data = array("idCategory" => $_POST["editcategory"],
							   "barcode" => $_POST["editbarcode"],
							   "product" => $_POST["editproductname"],
                               "description" => $_POST["editdescription"],
							   "stock" => $_POST["editstock"],
							   "purchaseprice" => $_POST["editpurchaseprice"],
							   "saleprice" => $_POST["editsaleprice"],
							   "taxcat" => $_POST["edittaxcat"],
							   "image" => $route,
							   "status" => $status,
							   'storeid' => self::$storeid);
							   
				$item = "barcode";
				$barcode = $data['barcode'];
				$order = "id";
				$oldItem = productModel::mdlFetchProducts($table, $item, $barcode, $order);

				$changedInfo = ''; // Initialize the changed information string

				foreach ($data as $property => $value) {
					if ($property !== 'barcode' && $property !== 'storeid' && $property !== 'status' &&$oldItem[$property] !== $value) {
						$changedInfo .= "Property $property changed from {$oldItem[$property]} to $value. ";
					}
				}
				
				// If any properties were changed, use the changed information as the log message
				if (!empty($changedInfo)) {
					$logMessage = $changedInfo;
				} else {
					$logMessage = 'User ' . $_SESSION['username'] . ' tried to edit product ' . $oldItem['product'] . '.';
				}

				$answer = productModel::mdlEditProduct($table, $data);

				if($answer == "ok" && !empty($changedInfo)){
					
					if ($_SESSION['userId'] != 404) {
						// Create an array with the data for the activity log entry
						$data = array(
							'UserID' => $_SESSION['userId'],
							'ActivityType' => 'Product',
							'ActivityDescription' => $logMessage,
							'itemID' => $barcode,
							'storeid' => self::$storeid,
							'TimeStamp' => $dateTimeStr
						);
						// Call the ctrCreateActivityLog() function
						activitylogController::ctrCreateActivityLog($data);
					}

					echo'<script>

							Swal.fire({
									icon: "success",
									title: "The product has been edited",
									showConfirmButton: false,
									timer: 2000 // Auto close after 2 seconds
								  }).then(function () {
									// Code to execute after the alert is closed
									window.location = "products";
								  });

						</script>';

				}else {

					if ($_SESSION['userId'] != 404) {
						// Create an array with the data for the activity log entry
						$logdata = array(
							'UserID' => $_SESSION['userId'],
							'ActivityType' => 'Category',
							'ActivityDescription' => $logMessage,
							'itemID' => $value,
							'storeid' => self::$storeid,
							'TimeStamp' => $dateTimeStr
						);
						// Call the ctrCreateActivityLog() function
						activitylogController::ctrCreateActivityLog($logdata);
					}
	
					echo'<script>
							Swal.fire({
								icon: "success",
								title: "No changes were made",
								showConfirmButton: false,
								timer: 2000 // Timer set to 2 seconds (2000 milliseconds)
							}).then(function () {
								// Code to execute when the alert is closed (after 2 seconds)
								window.location = "products";
							});
					</script>';
					
				}


			}else{

				echo'<script>

				Swal.fire({
						  icon: "error",
						  title: "Invalid parameters. Please check your values",
							showConfirmButton: false,
							timer: 2000 // Auto close after 2 seconds
						  }).then(function () {
							// Code to execute after the alert is closed
							window.location = "products";
						  });

			  	</script>';
			}

		}

	}

	/*=============================================
	DELETE PRODUCT
	=============================================*/
	static public function ctrDeleteProduct(){
        self::initialize();

		if(isset($_GET["product-id"])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');			

			$table ="products";
			$barcode = $_GET['product-id'];
			$data = array(
				'status' => 1,
				'barcode' => $barcode
			);
			if (isset($_GET["image"]) && $_GET["image"] != "" && $_GET["image"] != "views/img/products/default/anonymous.png") {
				// Delete the specified image
				unlink($_GET["image"]);
			
				// Check if the directory contains more images
				$productimageDir = 'views/img/products/'.$_GET["product-id"];
				$filesInDir = glob($productimageDir . "/*");
			
				if (count($filesInDir) === 1) {
					// Remove the directory if it contains only one image
					rmdir($productimageDir);
				}
			}
			
			
			$item = "id";
			$value = $_GET["product-id"];
			$order = "id";
			$loganswer = productModel::mdlFetchProducts($table, $item, $value, $order);
			$product = $loganswer['product'];

			// $answer = productModel::mdlDeleteProduct($table, $data);
			$answer = productModel::mdlDeleteProduct($table, $data);

			if($answer == "ok"){
				if ($_SESSION['userId'] != 404) {
					// Create an array with the data for the activity log entry
					$logdata = array(
						'UserID' => $_SESSION['userId'],
						'ActivityType' => 'Product',
						'ActivityDescription' => 'User ' . $_SESSION['username'] . ' deleted product ' .$product. '.',
						'itemID' => $value,
						'storeid' => self::$storeid,
						'TimeStamp' => $dateTimeStr
					);
					// Call the ctrCreateActivityLog() function
					activitylogController::ctrCreateActivityLog($logdata);
				}

				echo'<script>

				Swal.fire({
					  icon: "success",
					  title: "Product '.$loganswer['product'].' has been  deleted.",
					  showConfirmButton: false,
					  timer: 2000 // Auto close after 2 seconds
					}).then(function () {
					  // Code to execute after the alert is closed
					  window.location = "products";
					});

				</script>';

			}		
		
		}

	}



	/*=============================================
	Adding TOTAL sales
	=============================================*/

	public static function ctrAddingTotalSales(){

		$table = "products";

		$answer = productModel::mdlAddingTotalSales($table);

		return $answer;

	}
	
	/*=============================================
	Adding to stock
	=============================================*/
	public static function ctrAddingStock(){
        self::initialize();

		if (isset($_POST["addStock"])) {
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
			
			
	
			$table = "products";
			$quantity = $_POST['astock'] ?? '';
			$barcode = $_POST["product"] ?? '';
	
			if (empty($quantity) || empty($barcode)) {
				echo '<script>
				Swal.fire({
					icon: "warning",
					title: "Please fill in all required fields",
					showConfirmButton: false,
					timer: 2000 // Auto close after 2 seconds
				  })
				</script>';
				return;
			}
	
			if (isset($_GET['DCM']) && $_GET['DCM'] == "basic") {
				$answer = productModel::mdlAddingStock($table, $quantity, $barcode);
			} else {
				$randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
				$current_time_formatted = $dateTime->format("YmdHis"); // Format the current time in hours, minutes, and seconds
				$batchId = $randomNumber . "-" . $current_time_formatted;

				$batchitems = $_POST['productList'];

				// Decode the JSON string into a PHP array
				$data = json_decode($batchitems, true);

				// Count the number of objects in the array
				$batchquantity = count($data);
				$productId = $_POST["product"];

				$table = "batches";
				$batchData = array(
					'batch_id' => $batchId,
					'quantity' => $batchquantity,
					'orderId' => null,
					'product_id' => $productId,
					'datecreated' => $dateTimeStr
					// Add other batch-related data here
				);
				var_dump($batchData);
				
					
				$query = "SELECT * FROM batches WHERE product_id = :product_id AND store_id = :store_id AND quantity = :quantity AND datecreated >= :datecreated - INTERVAL 5 MINUTE";
				$params = array(':product_id' => $productId, 'store_id' => self::$storeid, 'quantity' => $batchquantity, 'datecreated' => $dateTimeStr);
				$existingBatch = OrdersModel::ctrCustomQuery($query, $params);
				if (!$existingBatch) {
					// Create the batch
					$batch = OrdersModel::mdlCreateBatch($table, $batchData); // Replace with your actual model function
		
					// update product quantity
					$table = "products";
					$item1 = 'stock';
					$incrementBy = $batchquantity;
					$value = $productId;
					
					$query = "UPDATE $table SET $item1 = $item1 + :incrementBy WHERE id = :id";
					$params = array(':id' => $value, 'incrementBy' => $incrementBy);
					$updatestock = OrdersModel::ctrCustomQuery($query, $params);
		
		
					if ($batchitems) {
						if ($batch == "ok") {
							$table = "batch_items";

							// Decode the JSON data
							$productArray = json_decode($batchitems, true);

							// Assuming $table and mdlAddBatchitems are defined elsewhere
							foreach ($productArray as $product) {
								$batchItemData = array(
									'batch_id' => $batchId,
									'serial_number' => $product['sNumber'], // Adjust the key based on your JSON structure
									'manufacturing_date' => $product['mfDate'], // Adjust the key based on your JSON structure
									'expiry_date' => $product['exDate'], // Adjust the key based on your JSON structure
									// Add other batch item-related data here
								);
								var_dump($batchItemData);

								// Add the batch item
								OrdersModel::mdlAddBatchitems($table, $batchItemData); // Replace with your actual model function
							}
		
						}
		
					}
					
				}
				
			}
				
	
			if ($answer == "ok"){
				if ($_SESSION['userId'] != 404) {
					// Create an array with the data for the activity log entry
					$logdata = array(
						'UserID' => $_SESSION['userId'],
						'ActivityType' => 'Product',
						'ActivityDescription' =>  'User ' . $_SESSION['username'] . ' added ' .$quantity. ' units to a product\'s stock.',
						'itemID' => $barcode,
						'storeid' => self::$storeid,
						'TimeStamp' => $dateTimeStr
					);
					// Call the ctrCreateActivityLog() function
					activitylogController::ctrCreateActivityLog($logdata);
				}

				echo '<script>
				Swal.fire({
					icon: "success",
					title: "'.$quantity.' units have been added to the current stock quantity",
							showConfirmButton: false,
							timer: 2000 // Auto close after 2 seconds
						  }).then(function () {
							// Code to execute after the alert is closed
							window.location = "stock";
						  });
				</script>';
			}	

		}

	}

	// public static function fetchSalesData() {
	// 	self::initialize();
	// 	$pdo = connection::connect();
	// 	$store=self::$storeid;
	// 	// Prepare and execute the SQL query to fetch sales data
	// 	$query = "SELECT DateCreated, products FROM invoices WHERE store_id = :store";
	// 	$statement = $pdo->prepare($query);
	// 	$statement->bindParam(':store', $store, PDO::PARAM_STR);
	// 	$statement->execute();
		
		
	// 	$salesData = array();
		
	// 	while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
	// 		$startDate = $row['startdate'];
	// 		$productsJson = $row['products'];
			
	// 		// Decode the JSON data
	// 		$products = json_decode($productsJson, true);
			
	// 		// Loop through products in the invoice and add them to the sales data array
	// 		foreach ($products as $product) {
	// 			$salesData[] = array(
	// 				'startdate' => $startDate,
	// 				'productName' => $product['productName'],
	// 				'Quantity' => $product['Quantity'],
	// 				'salePrice' => $product['salePrice'],
	// 				'Discount' => $product['Discount'],
	// 			);
	// 		}
	// 	}
		
	// 	return $salesData;
	// }

}
?>
