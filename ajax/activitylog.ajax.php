<?php
    session_start();
    require_once "../controllers/activitylog.controller.php";
    require_once "../models/activitylog.model.php";


    class AjaxActivityLog {
        public function ajaxCreateLog($barcodeProduct, $productName) {
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');

            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();

            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
            if ($_SESSION['userId'] != 404) {
                // Create an array with the data for the activity log entry
                $logData = array(
                    'UserID' => $_SESSION['userId'],
                    'ActivityType' => 'Product',
                    'ActivityDescription' => 'User ' . $_SESSION['username'] . ' viewed product ' . $productName . '.',
                    'itemID' => $barcodeProduct,
                    'TimeStamp' => $dateTimeStr
                );

                // Call the ctrCreateActivityLog() function
                $answer = activitylogController::ctrCreateActivityLog($logData);
            }

            echo json_encode($answer);
        }
    }

    if (isset($_POST["barcodeProduct"]) && isset($_POST["productname"])) {
        $log = new AjaxActivityLog();
        $barcodeProduct = $_POST["barcodeProduct"];
        $productName = $_POST["productname"];
        $log->ajaxCreateLog($barcodeProduct, $productName);
    }
