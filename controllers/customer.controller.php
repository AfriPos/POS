<?php
class customerController{
    

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
	CREATE CUSTOMER
	=============================================*/
	static public function ctrCreateCustomer(){
        self::initialize();
		// fetch all the categories in the database
		$table = "customers";
		$item = null;
		$value = null;
		$Allcustomers = customerModel::mdlShowCustomers($table, $item, $value);
		// var_dump($Allcategories);
		
		// Count the number of categories array
		$numAllcategories = count($Allcustomers);
		$validatetable = "customers";
		$element = "customers";
		$organisationcode = $_SESSION['organizationcode'];
		$response = packagevalidateController::ctrPackageValidate($element, $validatetable, $numAllcategories, $organisationcode);

		if(isset($_POST['addCustomer'])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');			

			if ($response) {
				if (!empty(self::$storeid)) {

                    $data = array(
                        "CustomerName" => $_POST['CustomerName'],
                        "CustomerAddress" => $_POST['CustomerAddress'],
                        "contactNumber" => $_POST['contactNumber'],
                        "CustomerEmail" => $_POST['CustomerEmail'],
                        "storeid" => self::$storeid
                    );

                    $answer = customerModel::mdlCreateCustomer($table, $data);

                    if($answer == 'ok'){
        
                        
                        if ($_SESSION['userId'] != 404) {
                            // Create an array with the data for the activity log entry
                            $logdata = array(
                                'UserID' => $_SESSION['userId'],
                                'ActivityType' => 'Customer',
                                'ActivityDescription' => 'User ' . $_SESSION['username'] . ' created customer ' . $data['category'] . '.',
                                'storeid' => self::$storeid,
								'TimeStamp' => $dateTimeStr
                            );
            
                            // Call the ctrCreateActivityLog() function
                            activitylogController::ctrCreateActivityLog($logdata);
                        }

                        echo '<script>
                                Swal.fire({
                                icon: "success",
                                title: "Customer has been created.",
                                showConfirmButton: false,
                                timer: 2000 // 2 seconds
                                }).then((result) => {
                                // Code to execute after the alert is closed
                                window.location = "customers";
                                });
                            
                        </script>';
                    }
                    

				}else {

					echo '<script>
							Swal.fire({
								icon: "error",
								title: "Select a Store first.",
								showConfirmButton: false,
								timer: 2000 // Auto close after 2 seconds
							}).then(function () {
								// The function inside "then" will be executed when the SweetAlert is closed
								window.location = "customers";
							});
					</script>';
					
				}

			} else {

				echo'<script>
				
				Swal.fire({
					icon: "warning",
					title: "Cannot Add Category",
					text: "You cannot add more customers with your current package. Consider upgrading your package.",
					button: "OK"
				});

				</script>';
				
			}
				
		}

	}

    
	/*=============================================
	EDIT CUSTOMER
	=============================================*/

	static public function ctrEditCustomer(){
        self::initialize();

		if(isset($_POST["editcustomer"])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
			
			$table = "customers";

			$data = array(
                "customer_id" => $_POST['editcustomerId'],
                "name" => $_POST['editcustomerName'],
                "address" => $_POST['editcustomerAddress'],
                "phone" => $_POST['editcontactNumber'],
                "email" => $_POST['editcustomerEmail'],
            );
			
			$item = "customer_id";
			$value = $data['customer_id'];
			$oldItem = customerModel::mdlShowCustomers($table, $item, $value);

            $changedInfo = ''; // Initialize the changed information string

            foreach ($data as $property => $value) {
                if ($property !== 'id' && $oldItem[$property] !== $value) {
                    $changedInfo .= "Property $property changed from {$oldItem[$property]} to $value. ";
                }
            }
            
            // If any properties were changed, use the changed information as the log message
            if (!empty($changedInfo)) {
                $logMessage = $changedInfo;
            } else {
                $logMessage = 'User ' . $_SESSION['username'] . ' tried to edit customer ' . $oldItem['name'] . '.';
            }

			$answer = customerModel::mdlEditCustomer($table, $data);

			if($answer == "ok" && !empty($changedInfo)){
				
				if ($_SESSION['userId'] != 404) {
					// Create an array with the data for the activity log entry
					$logdata = array(
						'UserID' => $_SESSION['userId'],
						'ActivityType' => 'Customer',
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
							title: "Changes have been saved.",
							showConfirmButton: false,
							timer: 2000 // Timer set to 2 seconds (2000 milliseconds)
						}).then(function () {
							// Code to execute when the alert is closed (after 2 seconds)
							window.location = "customers";
						});
				</script>';

			}else {

				if ($_SESSION['userId'] != 404) {
					// Create an array with the data for the activity log entry
					$logdata = array(
						'UserID' => $_SESSION['userId'],
						'ActivityType' => 'Customer',
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
							window.location = "customers";
						});
				</script>';
				
			}

		}

	}

    /*=============================================
	SHOW CUSTOMER
	=============================================*/

	static public function ctrShowCustomers($item, $value){

		$table = "customers";

		$answer = customerModel::mdlShowCustomers($table, $item, $value);

		return $answer;
	}
}
