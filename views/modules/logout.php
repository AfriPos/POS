<?php

if ($_SESSION['userId'] != 404) {
    // Set the default timezone to Nairobi
    date_default_timezone_set('Africa/Nairobi');
    
    // Create a DateTime object with the current date and time in Nairobi timezone
    $dateTime = new DateTime();
    
    // Format the DateTime as a string
    $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
    
    // Create an array with the data for the activity log entry
    $data = array(
        'UserID' => $_SESSION['userId'],
        'ActivityType' => 'Logout',
        'ActivityDescription' => 'User ' . $_SESSION['username'] . ' logged out.',
        'TimeStamp' => $dateTimeStr
    );
    // Call the ctrCreateActivityLog() function
    activitylogController::ctrCreateActivityLog($data);
}

session_destroy();

echo '<script>
 window.location= "login"
 </script>';

?>
'

?>