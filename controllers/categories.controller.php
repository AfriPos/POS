<?php
 class categoriesController{

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
	static public function ctrCreateCategory(){
        self::initialize();
		// fetch all the categories in the database
		$table = "categories";
		$item = null;
		$value = null;
		$Allcategories = CategoriesModel::mdlShowCategories($table, $item, $value);
		// var_dump($Allcategories);
		
		// Count the number of categories array
		$numAllcategories = count($Allcategories);
		$validatetable = "customers";
		$element = "categories";
		$organisationcode = $_SESSION['organizationcode'];
		$response = packagevalidateController::ctrPackageValidate($element, $validatetable, $numAllcategories, $organisationcode);

		if(isset($_POST['newCategory'])){

			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');

			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();

			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
			
			if ($response) {
				if (!empty(self::$storeid)) {

					if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["newCategory"])){

						$data = array(
							"category" => $_POST['newCategory'],
							"storeid" => self::$storeid
						);

						$answer = CategoriesModel::mdlAddCategory($table, $data);
						// var_dump($answer);

						if($answer == 'ok'){
			
							
							if ($_SESSION['userId'] != 404) {
								// Create an array with the data for the activity log entry
								$logdata = array(
									'UserID' => $_SESSION['userId'],
									'ActivityType' => 'Category',
									'ActivityDescription' => 'User ' . $_SESSION['username'] . ' created category ' . $data['category'] . '.',
									'storeid' => self::$storeid,
									'TimeStamp' => $dateTimeStr
								);
				
								// Call the ctrCreateActivityLog() function
								activitylogController::ctrCreateActivityLog($logdata);
							}

							echo '<script>
									Swal.fire({
									icon: "success",
									title: "Category '.$data['category'].' has been created.",
									showConfirmButton: false,
									timer: 2000 // 2 seconds
									}).then((result) => {
									// Code to execute after the alert is closed
									window.location = "category";
									});
								
							</script>';
						}
						

					}else{

						echo '<script>
								Swal.fire({
									icon: "error",
									title: "No especial characters or blank fields.",
									showConfirmButton: false,
									timer: 2000 // Auto close after 2 seconds
								}).then(function () {
									// The function inside "then" will be executed when the SweetAlert is closed
									window.location = "category";
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
								window.location = "category";
							});
					</script>';
					
				}

			} else {

				echo'<script>
				
				Swal.fire({
					icon: "warning",
					title: "Cannot Add Category",
					text: "You cannot add more categories with your current package. Consider upgrading your package.",
					button: "OK"
				});

				</script>';
				
			}
				
		}

	}

	/*=============================================
	SHOW CATEGORIES
	=============================================*/

	static public function ctrShowCategories($item, $value){

		$table = "categories";

		$answer = CategoriesModel::mdlShowCategories($table, $item, $value);

		return $answer;
	}

	/*=============================================
	EDIT CATEGORY
	=============================================*/

	static public function ctrEditCategory(){
		// Set the default timezone to Nairobi
		date_default_timezone_set('Africa/Nairobi');
		
		// Create a DateTime object with the current date and time in Nairobi timezone
		$dateTime = new DateTime();
		
		// Format the DateTime as a string
		$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
		
		
        self::initialize();

		if(isset($_POST["editCategory"])){

			$table = "categories";

			$data = array("Category"=>$_POST["editCategory"],
							"id"=>$_POST["idCategory"]);
			
			$item = "id";
			$value = $data['id'];
			$oldItem = CategoriesModel::mdlShowCategories($table, $item, $value);

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
                $logMessage = 'User ' . $_SESSION['username'] . ' tried to edit category ' . $oldItem['Category'] . '.';
            }

			$answer = CategoriesModel::mdlEditCategory($table, $data);

			if($answer == "ok" && !empty($changedInfo)){
				
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
							title: "Category '.$oldItem['category'].' changes have been saved.",
							showConfirmButton: false,
							timer: 2000 // Timer set to 2 seconds (2000 milliseconds)
						}).then(function () {
							// Code to execute when the alert is closed (after 2 seconds)
							window.location = "category";
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
							window.location = "category";
						});
				</script>';
				
			}

		}

	}

	/*=============================================
	DELETE CATEGORY
	=============================================*/

	static public function ctrDeleteCategory(){
        self::initialize();

		if(isset($_GET["idCategory"])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');
			
			

			$table ="categories";
			$data = $_GET["idCategory"];

			$item = "id";
			$value = $data;
			$loganswer = CategoriesModel::mdlShowCategories($table, $item, $value);

			$category = $loganswer['Category'];

			$table2 = "products";
			$item2 = "idCategory";
			$value2 = $data;
			$order = "id";
			$products = productModel::mdlFetchProducts($table2, $item2, $value2, $order);
				
			if($products){

				echo'<script>
				
						Swal.fire({
							icon: "warning",
							title: "The category contains existing products.",
							showConfirmButton: false,
							timer: 2000 // Close the alert after 2 seconds
						}).then(function() {
							window.location = "category";
						});

					</script>';

			}else {
				
				$answer = CategoriesModel::mdlDeleteCategory($table, $data);

				if($answer == "ok"){
					
					if ($_SESSION['userId'] != 404) {
						// Create an array with the data for the activity log entry
						$logdata = array(
							'UserID' => $_SESSION['userId'],
							'ActivityType' => 'Category',
							'ActivityDescription' => 'User ' . $_SESSION['username'] . ' deleted category ' .$category. '.',
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
								title: "The category '.$category.' has been successfully deleted",
								showConfirmButton: false,
								timer: 2000 // Set the timer to 2 seconds
							}).then(function () {
								// This will execute after the SweetAlert closes
								window.location = "category";
							});
					  
						</script>';
				}

			}
		
		}
		
	}

}