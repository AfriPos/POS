<?php
include 'access_token.php';

$DynamicQRUrl = "https://sandbox.safaricom.co.ke/mpesa/qrcode/v1/generate";
$MerchantName = "AfriPOS";
$AccountNumber = "254703623699";
$BusinessShortCode = "600996";
$payload = array(
    'MerchantName' => $MerchantName,
    'RefNo' =>  $AccountNumber,
    'Amount' => '10000',
    'TrxCode' => 'PB',
    'CPI' => $BusinessShortCode,
    'Size' => '300',
);

$ch = curl_init();
curl_setopt_array(
    $ch,
    array(
        CURLOPT_URL => $DynamicQRUrl,
        CURLOPT_HTTPHEADER =>  array('Content-Type: application/json', 'Authorization:Bearer ' . $access_token),
        CURLOPT_POST =>  true,
        CURLOPT_POSTFIELDS =>  json_encode($payload),
        CURLOPT_RETURNTRANSFER =>  true,
        CURLOPT_SSL_VERIFYPEER =>  false,
        CURLOPT_SSL_VERIFYHOST =>  false
    )
);

$response = curl_exec($ch);
$resp = json_decode($response);

if (isset($resp->QRCode)) {
    $data =  $resp->QRCode;
    $qrImage = "data:image/jpeg;base64, {$resp->QRCode}";

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'qrImage' => $qrImage,
    ]);
} else {
    // Return JSON response for error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later.',
    ]);
}
?>
