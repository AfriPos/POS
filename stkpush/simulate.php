<?php
require 'access_token.php';

$ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, 1);

// Define the POST data as an associative array
$postData = array(
    "ShortCode" => 600984,
    "CommandID" => "CustomerPayBillOnline",
    "Amount" => "1",
    "Msisdn" => "254708374149",
    "BillRefNumber" => "testing"
);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); // Encode POST data as JSON
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // Decode the response
    $decodedResponse = json_decode($response);

    // Check if the simulation was successful
    if (isset($decodedResponse->ResponseCode) && $decodedResponse->ResponseCode == "0") {
        echo "Transaction simulation successful!";
    } else {
        echo "Transaction simulation failed. Response: " . $response;
    }
}

curl_close($ch);
?>
