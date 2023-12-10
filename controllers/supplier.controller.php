<?php

 class supplierController{

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
	CREATE SUPPLIER
	=============================================*/
	
	static public function ctrCreateSupplier(){
        self::initialize();

		if(isset($_POST['addSupplier'])){
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
            
            

			if (!empty(self::$storeid)) {

                $table = "suppliers";

                $data = array("name" => $_POST["Supplier"],
                            "address" => $_POST["Address"],
                            "email" => $_POST["Email"],
                            "contact" => $_POST["Contact"],
                            "storeid" => self::$storeid);

                $answer = supplierModel::mdlAddsSupplier($table, $data);

                if($answer == "ok"){
                    if ($_SESSION['userId'] != 404) {
                        // Create an array with the data for the activity log entry
                        $logdata = array(
                            'UserID' => $_SESSION['userId'],
                            'ActivityType' => 'Supplier',
                            'ActivityDescription' => 'User ' . $_SESSION['username'] . ' creates supplier ' .$data['name']. '.',
                            'storeid' => self::$storeid,
                            'TimeStamp' => $dateTimeStr
                        );
                        // Call the ctrCreateActivityLog() function
                        activitylogController::ctrCreateActivityLog($logdata);
                    }

                    echo'<script>

                        Swal.fire({
                            icon: "success",
                            title: "Supplier '.$data['name'].' has been created!",
                            showConfirmButton: false,
                            timer: 2000 // 2 seconds
                        }).then(function(result) {
                            window.location = "suppliers";
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
							window.location = "suppliers";
						});
				</script>';
				
			}

        }

    }
    /*=============================================
	SHOW SUPPLIERS
	=============================================*/

	static public function ctrShowSuppliers($item, $value){

		$table = "suppliers";

		$answer = supplierModel::mdlShowSuppliers($table, $item, $value);

		return $answer;
	}

	/*=============================================
	EDIT SUPPLIERS
	=============================================*/

	static public function ctrEditSupplier(){
        self::initialize();

		if(isset($_POST["editsupplier"])){
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');            

            $table = "suppliers";

            $data = array(
                "name" => $_POST["newSupplier"],
                "supplierid" => $_POST["supplierId"],
                "address" => $_POST["newAddress"],
                "email" => $_POST["newEmail"],
                "contact" => $_POST["newContact"]
            );
            
            $item = "supplierid";
            $supplierid = $_POST["supplierId"];
            $oldItem = supplierModel::mdlShowSuppliers($table, $item, $supplierid);

            $changedInfo = ''; // Initialize the changed information string

            foreach ($data as $property => $value) {
                if ($property !== 'barcode' && $oldItem[$property] !== $value) {
                    $changedInfo .= "Property $property changed from {$oldItem[$property]} to $value. ";
                }
            }
            
            // If any properties were changed, use the changed information as the log message
            if (!empty($changedInfo)) {
                $logMessage = $changedInfo;
            } else {
                $logMessage = 'User ' . $_SESSION['username'] . ' tried to edit supplier ' . $oldItem['name'] . '.';
            }           
            

            $answer = supplierModel::mdlEditSupplier($table, $data);

            if($answer == "ok" && !empty($changedInfo)){
				if ($_SESSION['userId'] != 404) {
                    // Create an array with the data for the activity log entry
                    $logdata = array(
                        'UserID' => $_SESSION['userId'],
                        'ActivityType' => 'Supplier',
                        'ActivityDescription' => $logMessage,
                        'itemID' => $supplierid,
                        'storeid' => self::$storeid,
                        'TimeStamp' => $dateTimeStr
                    );
                    // Call the ctrCreateActivityLog() function
                    activitylogController::ctrCreateActivityLog($logdata);
                }

                echo'<script>

                Swal.fire({
                            icon: "success",
                            title: "The supplier has been edited",
                            showConfirmButton: false,
                            timer: 2000 // 2 seconds
                        }).then(function(result) {
                            window.location = "suppliers";
                        });

                    </script>';

            }else {

				if ($_SESSION['userId'] != 404) {
                    // Create an array with the data for the activity log entry
                    $logdata = array(
                        'UserID' => $_SESSION['userId'],
                        'ActivityType' => 'Supplier',
                        'ActivityDescription' => $logMessage,
                        'itemID' => $supplierid,
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
							window.location = "suppliers";
						});
				</script>';
				
			}
           
        }

    }

	/*=============================================
	DELETE SUPPLIER
	=============================================*/
	static public function ctrDeleteSupplier(){
        self::initialize();

		if(isset($_GET["id"])){
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');            

			$table ="suppliers";
			$data = $_GET["id"];
            
            $item = "supplierid";
            $supplierid = $data;
            $oldItem = supplierModel::mdlShowSuppliers($table, $item, $supplierid);

			$answer = supplierModel::mdlDeleteSupplier($table, $data);

			if($answer == "ok"){
				if ($_SESSION['userId'] != 404) {
                    // Create an array with the data for the activity log entry
                    $logdata = array(
                        'UserID' => $_SESSION['userId'],
                        'ActivityType' => 'Supplier',
                        'ActivityDescription' => 'User ' . $_SESSION['username'] . ' deleted supplier ' .$oldItem['name']. '.',
                        'itemID' => $supplierid,
                        'storeid' => self::$storeid,
                        'TimeStamp' => $dateTimeStr
                    );
                    // Call the ctrCreateActivityLog() function
                    activitylogController::ctrCreateActivityLog($logdata);
                }

				echo'<script>

				Swal.fire({
					  icon: "success",
					  title: "Supplier '.$oldItem['name'].' has been successfully deleted",
                      showConfirmButton: false,
                      timer: 2000 // 2 seconds
                  }).then(function(result) {
                      window.location = "suppliers";
                  });

				</script>';

			}	
        }

    }

}