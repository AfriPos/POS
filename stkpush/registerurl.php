<?php
require 'access_token.php'; // Assuming your access token file is named access_token.php

$registerurl = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
$BusinessShortCode = '600984';
$confirmationUrl = 'https://test1.afripos.co.ke/stkpush/confirmation';
$validationUrl = 'https://test1.afripos.co.ke/stkpush/validation';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $registerurl);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
));
$data = array(
    'ShortCode' => $BusinessShortCode,
    'ResponseType' => 'Completed',
    'ConfirmationURL' => $confirmationUrl,
    'ValidationURL' => $validationUrl
);
$data_string = json_encode($data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$curl_response = curl_exec($curl);

// Check for errors
if ($curl_response === false) {
    $error_message = curl_error($curl);
    echo "cURL Error: " . $error_message;
} else {
    // Decode the response
    $response = json_decode($curl_response);

    // Check if registration was successful
    if (isset($response->ResponseCode) && $response->ResponseCode == "0") {
        echo "Callback URLs registered successfully!";
    } else {
        echo "Callback URLs registration failed. Response: " . $curl_response;
    }
}

curl_close($curl);
?>
