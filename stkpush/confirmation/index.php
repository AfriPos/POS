<?php
$data = file_get_contents('php://input');
$handle = fopen('confirmation.txt', 'w');
fwrite($handle, $data);
fclose($handle);

$json_decode = json_decode($data, true);

if ($json_decode !== null && json_last_error() === JSON_ERROR_NONE) {
    // JSON decoding was successful
    $transactionStatus = isset($json_decode['TransactionStatus']) ? $json_decode['TransactionStatus'] : null;

    if ($transactionStatus === 'Completed') {
        // Additional processing for a successful transaction
        // ...

        // Send a response if needed
        $response = array(
            "ResultCode" => 0,
            "ResultDesc" => "Transaction successfully confirmed",
        );
        echo json_encode($response);
    } else {
        $error_message = "Transaction status not Completed";
        logError('confirmation_error.txt', $error_message);
        $response = array(
            "ResultCode" => 1,
            "ResultDesc" => $error_message,
        );
        echo json_encode($response);
    }
} else {
    // Handle invalid JSON data
    $error_message = "Invalid JSON data";
    logError('confirmation_error.txt', $error_message);
    $response = array(
        "ResultCode" => 1,
        "ResultDesc" => $error_message,
    );
    echo json_encode($response);
}

function logError($filename, $message) {
    $error_handle = fopen($filename, 'a');
    fwrite($error_handle, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL);
    fclose($error_handle);
}
?>
