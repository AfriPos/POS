<?php
session_start();
require_once "../controllers/renewal.controller.php";
require_once "../controllers/packagevalidate.controller.php";
require_once "../models/packagevalidate.model.php";


// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the "payment" button was clicked
        // Retrieve any data sent from the form, for example:
        $organizationCode = $_POST["organizationcode"];

        // Call your PHP functions here
        $pay = new serviceRenewal();
        $result = $pay->renewService(); // Capture the result of renewService

        // You can prepare a response to send back to the client
        $response = [
            "success" => true,
            "message" => "Service renewed successfully.", // You can customize the message
            "data" => $result // Include the result in the response data
        ];

        // Send the response as JSON
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
}

// If the request is not valid, return an error response
$response = [
    "success" => false,
    "message" => "Invalid request."
];

header("Content-Type: application/json");
echo json_encode($response);
