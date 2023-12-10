<?php

class notificationController{
	/*=============================================
	CREATE NOTIFICATIONS
	=============================================*/
    static public function ctrCreateNotification($data){

        $table = "notifications";

        $answer = notificationModel::mdlAddNotification($table, $data);
        
    }
	/*=============================================
	MAKE A REQUEST TO THE ADMINS
	=============================================*/
    static public function ctrMakeRequest(){

        if (isset($_POST['makeRequest'])) {

            $table = "notifications";
            date_default_timezone_set('africa/nairobi');
            $currentDateTime = date('Y-m-d H:i:s');
            $username = $_SESSION['username'];
            $storeid = $_SESSION['storeid'];
    
            $data = array("message" => $_POST["reason"],
                            "datetime" => $currentDateTime,
                            "name" => $username,
                            "type" => $_POST["type"] . "," . $_POST["id"],
                            "storeid" => $storeid);
    
            $answer = notificationModel::mdlAddNotification($table, $data);
    
            if($answer == "ok"){
    
                echo'<script>
    
                Swal.fire({
                          icon: "success",
                          title: "Success",
                          title: "Request sent to the admins",
                          timer: 2000,
                          showConfirmButton: false
                          })
                    </script>';
    
            }

        }
        
    }
	/*=============================================
	MARK A NOTIFICATIONS READ
	=============================================*/
    static public function ctrMarkNotificationsRead(){

        if (isset($_GET['id'])) {

            $table = "notifications";
            $id = $_GET['id'];
            $type = $_GET['type'];
            $data = array(
                "id" => $id,
                "type" => $type,
            );

            $answer = notificationModel::mdlMarkNotificationsRead($table, $data);
    
            if($answer == "ok"){

                // Get the current URL
                $currentUrl = $_SERVER['REQUEST_URI'];
                
                // Parse the URL to retrieve the route parameter
                $queryString = parse_url($currentUrl, PHP_URL_QUERY);
                parse_str($queryString, $queryParams);
                $route = $queryParams['route'];

				echo'
                <script>

                    window.location = "'.$route.'";

				</script>';
                
            }

        }

    }
	/*=============================================
	SHOW NOTIFICATIONS
	=============================================*/

	static public function ctrShowNotifications($item, $value){

		$table = "notifications";

		$answer = notificationModel::mdlShowNotifications($table, $item, $value);

		return $answer;
	}

}