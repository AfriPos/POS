<?php

require_once "../controllers/notifications.controller.php";
require_once "../models/notifications.model.php";

/*=============================================
HANDLE NOTIFICATIONS
=============================================*/	
class AjaxNotifications {

    public $data;
    public $sessionStoreid;

    public function ajaxMarkReadNotification() {

        $table = "notifications";
        $data = $this->data;

        $answer = notificationModel::mdlMarkNotificationsRead($table, $data);
        // Handle the response as needed
    }

    public function ajaxMarkReadNotificationRejected() {

        $notificationData = $this->data;

        date_default_timezone_set('africa/nairobi');
        $currentDateTime = date('Y-m-d H:i:s');
        $data = array(
            "message" => "Admins have rejected your deletion of " . $notificationData['name'],
            "datetime" => $currentDateTime,
            "name" => "System",
            "type" => "Admin feedback," . $notificationData['user']
        );
        $notification = notificationController::ctrCreateNotification($data);
        // Handle the notification creation as needed
    }

    public function ajaxShowAllNotifications() {
        
        $item = null;
        $value = null;
        $notifications = notificationController::ctrShowNotifications($item, $value);
        
		echo json_encode($notifications);

    }
    
    public function ajaxShowNotifications() {
        
        $item = "store_id";
        $value = $this->sessionStoreid;
        $notifications = notificationController::ctrShowNotifications($item, $value);
        
		echo json_encode($notifications);

    }

}
    if (isset($_POST["sessionStoreid"]) && isset($_POST["sessionRole"]) && $_POST["sessionRole"] == "Administrator") {

        $notification = new AjaxNotifications();
        
        $notification->ajaxShowAllNotifications();
        
    }
    if (isset($_POST["sessionStoreid"]) && isset($_POST["sessionRole"]) && $_POST["sessionRole"] != "Administrator") {

        $notification = new AjaxNotifications();
        $notification->sessionStoreid = $_POST["sessionStoreid"]; 
        $notification->ajaxShowNotifications();

    }

    if (isset($_POST["sessionid"]) && isset($_POST["notificationId"])) {

        $notification = new AjaxNotifications();
        $notification->data = array(
            'sessionid' => $_POST["sessionid"],
            'notificationId' => $_POST["notificationId"]
        );
        $notification->ajaxMarkReadNotification();
    }

    if (isset($_POST["session"]) && isset($_POST["type"]) && isset($_POST["status"]) && isset($_POST["name"]) && isset($_POST["user"])) {

        $notification = new AjaxNotifications();
        $notification->data = array(
            'id' => $_POST["session"],
            'status' => $_POST["status"],
            'name' => $_POST["name"],
            'user' => $_POST["user"],
            'type' => $_POST["type"]
        );
        $notification->ajaxMarkReadNotificationRejected();

    }
