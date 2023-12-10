<?php
class storeController{
    
    /*=============================================
    ADD STORE
    =============================================*/
    static public function ctrCreateStore(){
        
        $table = "store";
        $item = null;
        $value = null;
        $Allstores = storeModel::mdlShowStores($table, $item, $value);

		// Count the number of products array
		$numAllproducts = count($Allstores);
		$validatetable = "customers";
		$element = "stores";
		$organisationcode = $_SESSION['organizationcode'];
		$response = packagevalidateController::ctrPackageValidate($element, $validatetable, $numAllproducts, $organisationcode);

        if(isset($_POST['addStore'])){
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
                        
			if ($response) {

                $route = "views/img/store/default/store.png";

                $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
                $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                $storeId = "STORE-" . $randomNumber . "-" . $current_time_formatted;

                if(isset($_FILES["storeLogo"]["tmp_name"])){

                    list($width, $height) = getimagesize($_FILES["storeLogo"]["tmp_name"]);

                    $newWidth = 500;
                    $newHeight = 500;

                    /*=============================================
                    we create the folder to save the picture
                    =============================================*/

                    $folder = "views/img/store/".$storeId;

                    mkdir($folder, 0755);

                    /*=============================================
                    WE APPLY DEFAULT PHP FUNCTIONS ACCORDING TO THE IMAGE FORMAT
                    =============================================*/

                    if($_FILES["storeLogo"]["type"] == "image/jpeg"){

                        /*=============================================
                        WE SAVE THE IMAGE IN THE FOLDER
                        =============================================*/

                        $random = mt_rand(100,999);

                        $route = "views/img/store/".$storeId."/".$random.".jpg";

                        $origin = imagecreatefromjpeg($_FILES["storeLogo"]["tmp_name"]);						

                        $destiny = imagecreatetruecolor($newWidth, $newHeight);

                        imagecopyresized($destiny, $origin, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                        imagejpeg($destiny, $route);

                    }

                    if($_FILES["storeLogo"]["type"] == "image/png"){

                        /*=============================================
                        WE SAVE THE IMAGE IN THE FOLDER
                        =============================================*/

                        $random = mt_rand(100,999);

                        $route = "views/img/store/".$storeId."/".$random.".png";

                        $origin = imagecreatefrompng($_FILES["storeLogo"]["tmp_name"]);						

                        $destiny = imagecreatetruecolor($newWidth, $newHeight);

                        imagecopyresized($destiny, $origin, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                        imagepng($destiny, $route);

                    }

                }
                    // Set the desired time zone
                    date_default_timezone_set('Africa/Nairobi');

                    // Get the current time and date
                    $currentDateTime = date('Y-m-d H:i:s');
                    $data = array(
                    "store_id" => $storeId,
                    "store_name" => $_POST["storeName"],
                    "store_address" => $_POST["storeAddress"],
                    "contact_number" => $_POST["contactNumber"],
                    "email" => $_POST["storeEmail"],
                    "store_manager" => $_POST["storeManager"],
                    "opening" => $_POST["openingTime"],
                    "closing" => $_POST["closingTime"],
                    "logo" => $route,
                    "created_at" => $currentDateTime
                );
                
                
                $table = "store";
                $answer = storeModel::mdlAddstore($table, $data);

                if($answer == "ok"){
                    if ($_SESSION['userId'] != 404) {
                        // Create an array with the data for the activity log entry
                        $data = array(
                            'UserID' => $_SESSION['userId'],
                            'ActivityType' => 'Store',
                            'ActivityDescription' => 'User ' . $_SESSION['username'] . ' created store ' .$_POST['storeName']. '.',
                            'TimeStamp' => $dateTimeStr
                        );
                        // Call the ctrCreateActivityLog() function
                        activitylogController::ctrCreateActivityLog($data);
                    }

                    echo'<script>

                    Swal.fire({
                            icon: "success",
                            title: "Store '.$_POST['storeName'].' has been created",
                            showConfirmButton: true,
                            confirmButtonText: "Close"
                            }).then(function(result){
                                        if (result.value) {

                                        window.location = "manage-stores";

                                        }
                                    })

                        </script>';

                }

            } else {

				echo'<script>
				
				Swal.fire({
					icon: "warning",
					title: "Cannot Add Store",
					text: "You cannot add more stores with your current package. Consider upgrading your package.",
					button: "OK"
				});

				</script>';

			}

        }

    }

    /*=============================================
	EDIT STORE
	=============================================*/

	static public function ctrEditStore(){

        if(isset($_POST['editStore'])){
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');            

            $route = $_POST["currentImage"];
            $storeId = $_POST["editstoreId"];

            if(isset($_FILES["storeLogo"]["tmp_name"]) && !empty($_FILES["storeLogo"]["tmp_name"])){

                list($width, $height) = getimagesize($_FILES["storeLogo"]["tmp_name"]);

                $newWidth = 500;
                $newHeight = 500;

                /*=============================================
                WE CREATE THE FOLDER WHERE WE WILL SAVE THE PRODUCT IMAGE
                =============================================*/

                $folder = "views/img/store/".$storeId;

                /*=============================================
                WE ASK IF WE HAVE ANOTHER PICTURE IN THE DB
                =============================================*/

                if(!empty($_POST["currentImage"]) && $_POST["currentImage"] != "views/img/store/default/store.png"){

                    unlink($_POST["currentImage"]);

                }else{

                    mkdir($folder, 0755);	
                
                }
                
                /*=============================================
                WE APPLY DEFAULT PHP FUNCTIONS ACCORDING TO THE IMAGE FORMAT
                =============================================*/

                if($_FILES["storeLogo"]["type"] == "image/jpeg"){

                    /*=============================================
                    WE SAVE THE IMAGE IN THE FOLDER
                    =============================================*/

                    $random = mt_rand(100,999);

                    $route = "views/img/store/".$storeId."/".$random.".jpg";

                    $origin = imagecreatefromjpeg($_FILES["storeLogo"]["tmp_name"]);						

                    $destiny = imagecreatetruecolor($newWidth, $newHeight);

                    imagecopyresized($destiny, $origin, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    imagejpeg($destiny, $route);

                }

                if($_FILES["storeLogo"]["type"] == "image/png"){

                    /*=============================================
                    WE SAVE THE IMAGE IN THE FOLDER
                    =============================================*/

                    $random = mt_rand(100,999);

                    $route = "views/img/store/".$storeId."/".$random.".png";

                    $origin = imagecreatefrompng($_FILES["storeLogo"]["tmp_name"]);

                    $destiny = imagecreatetruecolor($newWidth, $newHeight);

                    imagecopyresized($destiny, $origin, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    imagepng($destiny, $route);

                }

            }

            $table = "store";
            
            $data = array(
                "store_id" => $storeId,
                "store_name" => $_POST["editstoreName"],
                "store_address" => $_POST["editstoreAddress"],
                "contact_number" => $_POST["editcontactNumber"],
                "email" => $_POST["editstoreEmail"],
                "store_manager" => $_POST["editstoreManager"],
                "opening" => $_POST["editopeningTime"],
                "closing" => $_POST["editclosingTime"],
                "logo" => $route
            );
            
            $item = "store_id";
            $store = $storeId;
            $oldItem = storeModel::mdlShowStores($table, $item, $store);
            
            $changedInfo = ''; // Initialize the changed information string
            
            foreach ($data as $property => $value) {
                if ($property !== 'store_id' && $oldItem[$property] !== $value) {
                    $changedInfo .= "Property $property changed from {$oldItem[$property]} to $value. ";
                }
            }
            
            // If any properties were changed, use the changed information as the log message
            if (!empty($changedInfo)) {
                $logMessage = $changedInfo;
            } else {
                $logMessage = "Store has been edited.";
            }
            
            $answer = storeModel::mdlEditstore($table,$data);

            if($answer == "ok"){
                
				if ($_SESSION['userId'] != 404) {
                    // Create an array with the data for the activity log entry
                    $data = array(
                        'UserID' => $_SESSION['userId'],
                        'ActivityType' => 'Store',
                        'ActivityDescription' => $logMessage,
                        'itemID' => $store,
                        'TimeStamp' => $dateTimeStr
                    );
                    // Call the ctrCreateActivityLog() function
                    activitylogController::ctrCreateActivityLog($data);
                }

                echo'<script>

                Swal.fire({
                        icon: "success",
                        title: "Store '.$_POST['editstoreName'].' has edited",
                        showConfirmButton: true,
                        confirmButtonText: "Close"
                        }).then(function(result){
                                    if (result.value) {

                                    window.location = "manage-stores";

                                    }
                                })

                    </script>';

            }

        }

    }

    /*=============================================
	SHOW STORE
	=============================================*/

	static public function ctrShowStores($item, $value){

		$table = "store";

		$answer = storeModel::mdlShowStores($table, $item, $value);

		return $answer;
	}

	/*=============================================
	DELETE STORE
	=============================================*/
	static public function ctrDeleteStore(){
        // Set the default timezone to Nairobi
        date_default_timezone_set('Africa/Nairobi');
        
        // Create a DateTime object with the current date and time in Nairobi timezone
        $dateTime = new DateTime();
        
        // Format the DateTime as a string
        $dateTimeStr = $dateTime->format('Y-m-d H:i:s');        

        function isStoreIdReferenced($storeId, $exemptTable) {
            $referenced = false;
        
            // Get the list of tables in the database
            $stmt = Connection::connect()->prepare("SHOW TABLES");
            $stmt->execute();
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
            foreach ($tables as $table) {
                // Skip the exempted table
                if ($table === $exemptTable) {
                    continue;
                }
        
                // Check if the store ID is referenced in the current table
                $stmt = Connection::connect()->prepare("SHOW COLUMNS FROM `$table` LIKE 'store_id'");
                $stmt->execute();
                $columnExists = $stmt->rowCount() > 0;
        
                if ($columnExists) {
                    $stmt = Connection::connect()->prepare("SELECT COUNT(*) FROM `$table` WHERE store_id = :store_id");
                    $stmt->bindParam(":store_id", $storeId, PDO::PARAM_STR);
                    $stmt->execute();
                    $count = $stmt->fetchColumn();
        
                    if ($count > 0) {
                        $referenced = true;
                        break;
                    }
                }
            }
        
            return $referenced;
        }        

		if(isset($_GET["id"])){
            $referenced = isStoreIdReferenced($_GET["id"], 'store');

            $table ="store";
            $data =  $_GET['id'];
            $item = "store_id";
            $value = $_GET["id"];
            $loganswer = storeModel::mdlShowStores($table, $item, $value);
            $store = $loganswer[0]['store_name'];

            if ($referenced) {
                
				if ($_SESSION['userId'] != 404) {
                    // Create an array with the data for the activity log entry
                    $logdata = array(
                        'UserID' => $_SESSION['userId'],
                        'ActivityType' => 'store',
                        'ActivityDescription' => 'User ' . $_SESSION['username'] . ' tried to delete store ' .$store. '.',
                        'itemID' => $value,
                        'TimeStamp' => $dateTimeStr
                    );
                    // Call the ctrCreateActivityLog() function
                    activitylogController::ctrCreateActivityLog($logdata);
                }

                echo'<script>
    
                Swal.fire({
                    icon: "error",
                    title: "The store is in use",
                    showConfirmButton: true,
                    confirmButtonText: "Close"
                    }).then(function(result){
                        if (result.value) {

                        window.location = "manage-stores";

                        }
                    })
    
                </script>';
    
            }else {
               
                if($_GET["image"] != "" && $_GET["image"] != "views/img/store/default/store.png"){

                    unlink($_GET["image"]);
                    rmdir('views/img/store/'.$_GET["id"]);

                }
                

                // $answer = productModel::mdlDeleteProduct($table, $data);
                $answer = storeModel::mdlDeleteStore($table, $data);

                if($answer == "ok"){
                    if ($_SESSION['userId'] != 404) {
                        // Create an array with the data for the activity log entry
                        $logdata = array(
                            'UserID' => $_SESSION['userId'],
                            'ActivityType' => 'store',
                            'ActivityDescription' => 'User ' . $_SESSION['username'] . ' deleted store ' .$store. '.',
                            'itemID' => $value,
                            'TimeStamp' => $dateTimeStr
                        );
                        // Call the ctrCreateActivityLog() function
                        activitylogController::ctrCreateActivityLog($logdata);
                    }

                    echo'<script>

                    Swal.fire({
                        icon: "success",
                        title: "Store '.$store.' has been deleted",
                        showConfirmButton: true,
                        confirmButtonText: "Close"
                        }).then(function(result){
                                    if (result.value) {

                                    window.location = "manage-stores";

                                    }
                                })

                    </script>';

                }
                
            }
		
		}

	}

}