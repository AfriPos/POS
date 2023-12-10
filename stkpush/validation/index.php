<?php
$data = file_get_contents('php://input');
$handle = fopen('validation.txt', 'w');
fwrite($handle, $data);
fclose($handle);

$json_decode = json_decode($data, true);

// Check if JSON decoding was successful
if ($json_decode === null && json_last_error() !== JSON_ERROR_NONE) {
    // Handle JSON decoding error
    $error_message = "Invalid JSON data";
    logError('validation_error.txt', $error_message);
    $response = array(
        "ResultCode" => 1, // Use an appropriate error code
        "ResultDesc" => $error_message,
    );
} else {
    // JSON decoding was successful
    $amount = isset($json_decode['TransAmount']) ? $json_decode['TransAmount'] : null;

    if ($amount > 0) {
        $response = array(
            "ResultCode" => 0,
            "ResultDesc" => "Accepted",
        );
    } else {
        $error_message = "Invalid transaction amount";
        logError('validation_error.txt', $error_message);
        $response = array(
            "ResultCode" => 1, // Use an appropriate error code
            "ResultDesc" => $error_message,
        );
    }
}

$json_response = json_encode($response);

header("Content-type: application/json");
echo $json_response;

function logError($filename, $message) {
    $error_handle = fopen($filename, 'a');
    fwrite($error_handle, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL);
    fclose($error_handle);
}
?>
