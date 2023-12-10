<?php
    session_start();
    
    require_once '../models/connection.php';

    $barcode = $_GET['barcode'];
    $table = 'products';

    $pdo = connection::connect();

    $select = $pdo->prepare("SELECT * FROM $table WHERE barcode = :barcode AND store_id = :store_id AND status = 0");
    $select->bindParam(':barcode', $barcode);
    $select->bindParam(':store_id', $_SESSION['storeid']);
    $select->execute();

    $row = $select->fetch(PDO::FETCH_ASSOC);

    $response = $row;

    header('Content-Type: application/json');
    echo json_encode($response);
?>