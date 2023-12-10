<?php
    session_start();

    // Retrieve session data
    $sessionId = $_SESSION['userId'];
    $sessionRole = $_SESSION['role'];
    $sessionUsername = $_SESSION['username'];
    $sessionStoreid = $_SESSION['storeid'];

    // Create an associative array to hold the session data
    $sessionData = array(
        'sessionId' => $sessionId,
        'sessionRole' => $sessionRole,
        'sessionUsername' => $sessionUsername,
        'sessionStoreid' => $sessionStoreid
    );

    // Convert the array to JSON and echo the response
    echo json_encode($sessionData);

if (isset($_POST['store_id'])) {
    // Sanitize and validate the store ID (you may need to perform additional checks based on your requirements)
    $storeID = $_POST['store_id'];
    
    // Update the session variable with the new store ID
    $_SESSION['storeid'] = $storeID;

}
if (isset($_POST['exit_store'])) {
    
    // Update the session variable with the new store ID
    $_SESSION['storeid'] = null;
    
}
?>
