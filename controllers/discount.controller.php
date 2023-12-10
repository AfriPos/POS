<?php

 class discountController{

	/*=============================================
   SET THE STORE ID
   =============================================*/
	
    static private $storeid;

	public static function initialize() {
		if ($_SESSION['storeid'] != null) {
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
	CREATE CATEGORY
	=============================================*/
	
	static public function ctrCreateDiscount(){
        self::initialize();

		if(isset($_POST['adddiscount'])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
			
            $table = 'discount';

			$data = array("product" => $_POST["discountproduct"],
                            "discount" => $_POST["discountname"],
                            "amount" => $_POST["discountamount"],
                            "startdate" => $_POST["startdate"],
                            "enddate" => $_POST["enddate"],
							"storeid" => self::$storeid);

							// var_dump($data);

            $answer = DiscountModel::mdlAddDiscount($table, $data);
            // var_dump($answer);

            if($answer == 'ok'){
				if ($_SESSION['userId'] != 404) {
					// Create an array with the data for the activity log entry
					$logdata = array(
						'UserID' => $_SESSION['userId'],
						'ActivityType' => 'Discount',
						'ActivityDescription' => 'User ' . $_SESSION['username'] . ' created discount ' .$data['discount']. ' with sum ' .$data['amount']. ' for product ' .$data['product']. '.',
						'storeid' => self::$storeid,
						'TimeStamp' => $dateTimeStr
					);
					// Call the ctrCreateActivityLog() function
					activitylogController::ctrCreateActivityLog($logdata);
				}

                echo '<script>
                    
                Swal.fire({
                        icon: "success",
                        title: "Discount has been successfully saved ",
						showConfirmButton: false,
						timer: 2000 // Auto close after 2 seconds
					  }).then(function () {
						// Code to execute after the alert is closed
						window.location = "discount";
					  });
                    
                </script>';
            }
				
		}
	}

	/*=============================================
	SHOW CATEGORIES
	=============================================*/

	static public function ctrShowDiscount($item, $value){

		$table = "discount";

		$answer = DiscountModel::mdlShowDiscount($table, $item, $value);

		return $answer;
	}

	/*=============================================
	EDIT DISCOUNT
	=============================================*/

	static public function ctrEditDiscount(){
        self::initialize();

		if (isset($_POST["editdiscount"])) {
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
						
			$table = "discount";
			$discountId = $_POST["discountid"];
			$oldItem = DiscountModel::mdlShowDiscount($table, "disId", $discountId); // Get the old discount details
			$data = array(
				"product" => $_POST["barcode"],
				"discountid" => $discountId,
				"discount" => $_POST["editdiscountname"],
				"amount" => $_POST["editdiscountamount"],
				"startdate" => $_POST["editstartdate"],
				"enddate" => $_POST["editenddate"],
			);
			
			$changedInfo = ''; // Initialize the changed information string

			foreach ($data as $property => $value) {
				if ($property !== 'discountid' && $oldItem[$property] !== $value) {
					$changedInfo .= "Property '$property' changed from '{$oldItem[$property]}' to '$value'. ";
				}
			}
			
			// If any properties were changed, use the changed information as the log message
			if (!empty($changedInfo)) {
				$logMessage = $changedInfo;
			} else {
				$logMessage = "Discount has been edited.";
			}
			
			// Update the item in the database
			$answer = DiscountModel::mdlEditDiscount($table, $data);
			
			if ($answer == "ok") {
				if ($_SESSION['userId'] != 404) {
					$logdata = array(
						'UserID' => $_SESSION['userId'],
						'ActivityType' => 'Discounts',
						'ActivityDescription' => $logMessage,
						'itemID' => $discountId,
						'storeid' => self::$storeid,
						'TimeStamp' => $dateTimeStr
					);
					// Call the method to create the activity log in the model or any other appropriate function
					activitylogController::ctrCreateActivityLog($logdata);
				}
				echo '<script>
					Swal.fire({
						icon: "success",
						title: "Discount has been successfully saved",
						showConfirmButton: false,
						timer: 2000 // Auto close after 2 seconds
					  }).then(function () {
						// Code to execute after the alert is closed
						window.location = "discount";
					  });
				</script>';
			}
		}
	}

	/*=============================================
	DELETE CATEGORY
	=============================================*/

	static public function ctrDeleteDiscount(){
        self::initialize();

		if(isset($_GET["idDiscount"])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
			
			$table ="discount";
			$data = $_GET["idDiscount"];

			$item = 'disId';
			$value = $data;
			$loganswer = DiscountModel::mdlShowDiscount($table, $item, $value);

			$discountname = $loganswer['discount'];
			$discountproductcode = $loganswer['product'];

			$answer = DiscountModel::mdlDeleteDiscount($table, $data);

			if($answer == "ok"){
				if ($_SESSION['userId'] != 404) {
					$logdata = array(
						'UserID' => $_SESSION['userId'],
						'ActivityType' => 'Discounts',
						'ActivityDescription' => 'User ' . $_SESSION['username'] . ' deleted discount ' .$discountname.' for product ' .$discountproductcode. '.'. '.',
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
						  title: "The discount has been successfully deleted",
						  showConfirmButton: false,
						  timer: 2000 // Auto close after 2 seconds
						}).then(function () {
						  // Code to execute after the alert is closed
						  window.location = "discount";
						});

					</script>';
			}
		
		}
		
	}

}