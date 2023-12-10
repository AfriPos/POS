<?php

class userController{

	/*=============================================
	USER LOGIN
	=============================================*/
	
	static public function ctrUserLogin(){

		if (isset($_POST["btn_login"])) {

			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');			

			$encryptpass = crypt($_POST["txt_password"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
			
			$table = 'users';

			$item = 'email';
			$value = $_POST["txt_user"];

			$answer = userModel::mdlShowUser($table, $item, $value);
			var_dump($encryptpass);
			

			$timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
			$current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
			$current_time_formatted = $current_time->format("H:i:s"); // Format the current time in hours, minutes, and seconds

			$item3 = "store_id";
			$value3= $answer['store_id'];
			$stores = storeController::ctrShowStores($item3, $value3);
			// var_dump($stores);
			// var_dump('opening ' .$stores[0]['opening']);
			// var_dump('closing ' .$stores[0]['closing']);
			// var_dump($current_time_formatted);
			// var_dump($current_time_formatted >= $stores[0]['opening']);
			// var_dump($current_time_formatted <= $stores[0]['closing']);
			if (($current_time_formatted >= $stores[0]['opening'] && $current_time_formatted <= $stores[0]['closing']) || $answer["role"] ==  "Administrator" || $answer["role"] == "Supervisor" || $answer["role"] == 404) {

				if($answer && $answer["email"] == $value && $answer["userpassword"] == $encryptpass ){

					if($answer["status"] == 1 && $answer["deleted"] == 0){

						$_SESSION["beginSession"] = "ok";
						$_SESSION["userId"] = $answer["userId"];
						$_SESSION["role"] = $answer["role"];
						$_SESSION["name"] = $answer["name"];
						$_SESSION["username"] = $answer["username"];
						$_SESSION["userphoto"] = $answer["userphoto"];
						$_SESSION["email"] = $answer["email"];
						$_SESSION["organizationcode"] = $answer["organizationcode"];
						if ($answer["role"] == "Administrator" || $answer["role"] == 404){
							$_SESSION['storeid'] = null;
						}else{
							$_SESSION['storeid'] = $answer["store_id"];
						}
    

						/*=============================================
						Register date to know last_login
						=============================================*/

						date_default_timezone_set("Africa/Nairobi");

						$date = date('Y-m-d');
						$hour = date('H:i:s');

						$actualDate = $date.' '.$hour;

						$item1 = "lastLogin";
						$value1 = $actualDate;

						$item2 = "userId";
						$value2 = $answer["userId"];

						$lastlogin = userModel::mdlUpdateUser($table, $item1, $value1, $item2, $value2);
						
						if ($lastlogin == "ok") {
							// Create an array with the data for the activity log entry
							
							if ($_SESSION['userId'] != 404) {
								$data = array(
									'UserID' => $_SESSION['userId'],
									'ActivityType' => 'Login',
									'ActivityDescription' => 'User ' . $_SESSION['username'] . ' logged in.',
									'TimeStamp' => $dateTimeStr
								);
								// Call the ctrCreateActivityLog() function
								activitylogController::ctrCreateActivityLog($data);

							}
							if ($_SESSION["role"] == "Administrator" || $_SESSION["role"] == "Supervisor" || $_SESSION['role'] == 404) {
								echo '<script>

									const Toast = Swal.mixin({
										toast: true,
										position: "top-end",
										showConfirmButton: false,
										timer: 2000,
										timerProgressBar: false,
										didOpen: (toast) => {
										toast.addEventListener("mouseenter", Swal.stopTimer)
										toast.addEventListener("mouseleave", Swal.resumeTimer)
										}
									})
									
									Toast.fire({
										icon: "success",
										title: "Signed in successfully"
									}).then(function () {
										// Code to execute after the alert is closed
										window.location = "dashboard";
									});
									
								</script>';
								// echo '<script>
								// 	window.location = "dashboard"; // Set the route for the Administrator and Supervisor
								// </script>';
							} elseif ($_SESSION["role"] == "Seller") {
								echo '<script>
									window.location = "pos"; // Set the route for the Seller
								</script>';
							} else {
								echo '<script>
									window.location = "stock"; // Set the route for the Store
								</script>';
							}
						}
						

					}else{
                    
						echo '<script>
	
						const Toast = Swal.mixin({
							toast: true,
							position: "top-end",
							showConfirmButton: false,
							timer: 2000,
							timerProgressBar: false,
							didOpen: (toast) => {
							toast.addEventListener("mouseenter", Swal.stopTimer)
							toast.addEventListener("mouseleave", Swal.resumeTimer)
							}
						})
						
						Toast.fire({
							icon: "error",
							title: "User deactivated"
						})
						
						
					</script>';
					
					}

				} else{

					echo '<script>
					
						const Toast = Swal.mixin({
							toast: true,
							position: "top-end",
							showConfirmButton: false,
							timer: 2000,
							timerProgressBar: false,
							didOpen: (toast) => {
							toast.addEventListener("mouseenter", Swal.stopTimer)
							toast.addEventListener("mouseleave", Swal.resumeTimer)
							}
						})
						
						Toast.fire({
							icon: "error",
							title: "Incorrect email or password"
						})
					
					</script>';
				
				}

			} else {
				echo '<script>

					const Toast = Swal.mixin({
						toast: true,
						position: "top-end",
						showConfirmButton: false,
						timer: 2000,
						timerProgressBar: false,
						didOpen: (toast) => {
						toast.addEventListener("mouseenter", Swal.stopTimer)
						toast.addEventListener("mouseleave", Swal.resumeTimer)
						}
					})
					
					Toast.fire({
						icon: "error",
						title: "Your shift is over, try again later!!!"
					})
				</script>';
			}

		}
	
	}


	/*=============================================
	CREATE USER
	=============================================*/
	
	static public function ctrCreateUser(){

		// fetch all products from the database
		$table = "users";
		$item = null;
		$value = null;
		$Allusers = userModel::mdlShowUser($table, $item, $value);
		
		// Count the number of products array
		$numAllusers = count($Allusers);
		$validatetable = "customers";
		$element = "users";
		$organisationcode = $_SESSION['organizationcode'];
		$response = packagevalidateController::ctrPackageValidate($element, $validatetable, $numAllusers, $organisationcode);

		if (isset($_POST["username"])) {
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');			

			if ($response) {
			
				if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["name"])){
	
					/*=============================================
					VALIDATE IMAGE
					=============================================*/
	
					$photo = "";
				
					if (isset($_FILES["userphoto"]["tmp_name"])){
	
						list($width, $height) = getimagesize($_FILES["userphoto"]["tmp_name"]);
						
						$newWidth = 500;
						$newHeight = 500;
	
						/*=============================================
						Let's create the folder for each user
						=============================================*/
	
						$folder = "views/img/users/".$_POST["username"];
	
						mkdir($folder, 0755);
	
						/*=============================================
						PHP functions depending on the image
						=============================================*/
	
						if($_FILES["userphoto"]["type"] == "image/jpeg"){
	
							$randomNumber = mt_rand(100,999);
							
							$photo = "views/img/users/".$_POST["username"]."/".$randomNumber.".jpg";
							
							$srcImage = imagecreatefromjpeg($_FILES["userphoto"]["tmp_name"]);
							
							$destination = imagecreatetruecolor($newWidth, $newHeight);
	
							imagecopyresized($destination, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
	
							imagejpeg($destination, $photo);
	
						}
	
						if ($_FILES["userphoto"]["type"] == "image/png") {
	
							$randomNumber = mt_rand(100,999);
							
							$photo = "views/img/users/".$_POST["username"]."/".$randomNumber.".png";
							
							$srcImage = imagecreatefrompng($_FILES["userphoto"]["tmp_name"]);
							
							$destination = imagecreatetruecolor($newWidth, $newHeight);
	
							imagecopyresized($destination, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
	
							imagepng($destination, $photo);
						}
	
					}
	
					$table = 'users';
	
					$encryptpass = crypt($_POST["userpassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
					
					if ($_SESSION['role'] == 'Administrator') {
						$store = $_POST['Selectstore'];
					}else {
						$store = $_SESSION['storeid'];
					}

					$data = array('name' => $_POST["name"],
								  'username' => $_POST["username"],
									'userpassword' => $encryptpass,
									'role' => $_POST["roleOptions"],
									'email' => $_POST["email"],
									'userphoto' => $photo,
									'store_id' => $store,
									'email' => $_POST['email'],
									'organizationcode' => $_SESSION['organizationcode'],
									'deleted' => 0);
	
	
					$item = 'username';
					$value = $_POST["username"];
	
					$SelectedUser = userModel::mdlShowAllUser($table, $item, $value);
					if ($SelectedUser && $SelectedUser['deleted'] == 1) {
						$answer = userModel::mdlEditUser($table, $data);
					}else {
						$answer = userModel::mdlCreateUser($table, $data);
					}
	
					if ($answer == 'ok') {
						if ($_SESSION['userId'] != 404) {
							// Create an array with the data for the activity log entry
							$logdata = array(
								'UserID' => $_SESSION['userId'],
								'ActivityType' => 'User',
								'ActivityDescription' => 'User ' . $_SESSION['username'] . ' creates user ' .$data['username']. '.',
								'itemID' => $data['username'],
								'TimeStamp' => $dateTimeStr
							);
							// Call the ctrCreateActivityLog() function
							activitylogController::ctrCreateActivityLog($logdata);
						}
	
							echo '<script>
							
							Swal.fire({
								icon: "success",
								title: "User added succesfully!",
								showConfirmButton: true,
								confirmButtonText: "Close"
	
							}).then(function(result){
	
								if(result.value){
	
									window.location = "registration";
								}
	
							});
							
							</script>';
	
					}
				
				}else{
	
					echo '<script>
						
						Swal.fire({
							icon: "error",
							title: "No especial characters or blank fields",
							showConfirmButton: true,
							confirmButtonText: "Close"
				
							}).then(function(result){
	
								if(result.value){
	
									window.location = "registration";
								}
	
							});
						
					</script>';
				}
				
			} else {

				echo'<script>
				
				Swal.fire({
					icon: "warning",
					title: "Cannot Add User",
					text: "You cannot add more users with your current package. Consider upgrading your package.",
					button: "OK"
				});

				</script>';

			}
			
		}

	}

	/*=============================================
	SHOW USER
	=============================================*/

	static public function ctrShowUsers($item, $value){

		$table = "users";

		$answer = userModel::mdlShowUser($table, $item, $value);

		return $answer;
	}
	static public function ctrShowAllUsers($item, $value){

		$table = "users";

		$answer = userModel::mdlShowAllUser($table, $item, $value);

		return $answer;
	}
	static public function ctrShowUser($item, $value, $role){

		$table = "users";

		$answer = userModel::mdlShowUsers($table, $item, $value, $role);

		return $answer;
	}

	/*=============================================
	EDIT USER
	=============================================*/

	static public function ctrEditUser(){

		if (isset($_POST["editName"])) {
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');			
			
			if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editName"])){

	// 			/*=============================================
	// 			VALIDATE IMAGE
	// 			=============================================*/

				$photo = $_POST["actualPhoto"];

				if(isset($_FILES["editUserphoto"]["tmp_name"]) && !empty($_FILES["editUserphoto"]["tmp_name"])){

					list($width, $height) = getimagesize($_FILES["editUserphoto"]["tmp_name"]);
					
					$newWidth = 500;
					$newHeight = 500;

	// 				/*=============================================
	// 				Let's create the folder for each user
	// 				=============================================*/

					$folder = "views/img/users/".$_POST["editUsername"];

	// 				/*=============================================
	// 				we ask first if there's an existing image in the database
	// 				=============================================*/

					if (!empty($_POST["actualPhoto"])){
						
						unlink($_POST["actualPhoto"]);

					}else{

						mkdir($folder, 0755);

					}

	// 				/*=============================================
	// 				PHP functions depending on the image
	// 				=============================================*/

					if($_FILES["editUserphoto"]["type"] == "image/jpeg"){

						/*We save the image in the folder*/

						$randomNumber = mt_rand(100,999);
						
						$photo = "views/img/users/".$_POST["editUsername"]."/".$randomNumber.".jpg";
						
						$srcImage = imagecreatefromjpeg($_FILES["editUserphoto"]["tmp_name"]);
						
						$destination = imagecreatetruecolor($newWidth, $newHeight);

						imagecopyresized($destination, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

						imagejpeg($destination, $photo);

					}
					
					if ($_FILES["editUserphoto"]["type"] == "image/png") {

						/*We save the image in the folder*/

						$randomNumber = mt_rand(100,999);
						
						$photo = "views/img/users/".$_POST["editUsername"]."/".$randomNumber.".png";
						
						$srcImage = imagecreatefrompng($_FILES["editUserphoto"]["tmp_name"]);
						
						$destination = imagecreatetruecolor($newWidth, $newHeight);

						imagecopyresized($destination, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

						imagepng($destination, $photo);
					}

				}

				
				$table = 'users';

				if($_POST["editUserpassword"] != ""){

					if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["editUserpassword"])){

						$encryptpass = crypt($_POST["editUserpassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

					}

					else{

						echo '<script>
					
							Swal.fire({
								icon: "error",
								title: "No especial characters in the password or blank fields",
								showConfirmButton: true,
								confirmButtonText: "Close"

								}).then(function(result){
										
									if (result.value) {
						
										window.location = "registration";

									}
								});
							
						</script>';
					}
				
				}else{

					$encryptpass = $_POST["actualPassword"];
					
				}

				$data = array('name' => $_POST["editName"],
								'username' => $_POST["editUsername"],
								'userpassword' => $encryptpass,
								'role' => $_POST["editRoleOptions"],
								'email' => $_POST["editemail"],
								'store_id' => $_POST["Editstore"],
								'userphoto' => $photo);
            
				$item = "username";
				$userid = $data["username"];
				$oldItem = userModel::mdlShowUser($table, $item, $userid);
				$d = var_dump($oldItem);
				
					
				echo '<script>
					
				Swal.fire({
					icon: "success",
					title: '.$d.',
					showConfirmButton: true,
					confirmButtonText: "Close"

				 })
			
			</script>';
	
				$changedInfo = ''; // Initialize the changed information string

				foreach ($data as $property => $value) {
					if ($oldItem[$property] !== $value) {
						$changedInfo .= "Property $property changed from {$oldItem[$property]} to $value. ";
					}
				}
				
				// If any properties were changed, use the changed information as the log message
				if (!empty($changedInfo)) {
					$logMessage = $changedInfo;
				} else {
					$logMessage = "User details have been edited.";
				}

				$answer = userModel::mdlEditUser($table, $data);

				if ($answer == 'ok') {
					if ($_SESSION['userId'] != 404) {
						// Create an array with the data for the activity log entry
						$logdata = array(
							'UserID' => $_SESSION['userId'],
							'ActivityType' => 'User',
							'ActivityDescription' => $logMessage,
							'itemID' => $data['username'],
							'TimeStamp' => $dateTimeStr
						);
						// Call the ctrCreateActivityLog() function
						activitylogController::ctrCreateActivityLog($logdata);
					}
					
					echo '<script>
					
						Swal.fire({
							icon: "success",
							title: "User edited succesfully!",
							showConfirmButton: true,
							confirmButtonText: "Close"

						 }).then(function(result){
							
							if (result.value) {

								window.location = "registration";
							}

						});
					
					</script>';
				}
				else{
					echo '<script>
						
						Swal.fire({
							icon: "error",
							title: "Error editing the user",	
							showConfirmButton: true,
							confirmButtonText: "Close"
							 }).then(function(result){
									
								if (result.value) {

									window.location = "registration";
								
								}

							});
						
					</script>';
				}
			
			}	
		
		}
	
	}

	/*=============================================
	DELETE USER
	=============================================*/

	static public function ctrDeleteUser(){

		if(isset($_GET["userId"])){
			// Set the default timezone to Nairobi
			date_default_timezone_set('Africa/Nairobi');
			
			// Create a DateTime object with the current date and time in Nairobi timezone
			$dateTime = new DateTime();
			
			// Format the DateTime as a string
			$dateTimeStr = $dateTime->format('Y-m-d H:i:s');			

			$table ="users";
			$userid = $_GET["userId"];
			$data = array(
				"userId" => $userid,
				"status" => 1
			);

			if($_GET["userphoto"] != ""){

				unlink($_GET["userphoto"]);				
				rmdir('views/img/users/'.$_GET["username"]);

			}

			$item = "userId";
			$user = userModel::mdlShowUser($table, $item, $data);

			$answer = userModel::mdlDeleteUser($table, $data);

			if($answer == "ok"){
				if ($_SESSION['userId'] != 404) {
					// Create an array with the data for the activity log entry
					$logdata = array(
						'UserID' => $_SESSION['userId'],
						'ActivityType' => 'User',
						'ActivityDescription' => 'User ' . $_SESSION['username'] . ' deleted user ' .$user['username']. '.',
						'itemID' => $userid,
						'TimeStamp' => $dateTimeStr
					);
					// Call the ctrCreateActivityLog() function
					activitylogController::ctrCreateActivityLog($logdata);
				}

				echo'<script>

				Swal.fire({
					  icon: "success",
					  title: "The user has been succesfully deleted",
					  showConfirmButton: true,
					  confirmButtonText: "Close"

					  }).then(function(result){
					  	
						if (result.value) {

						window.location = "registration";

						}
					})

				</script>';

			}		

		}

	}

}

