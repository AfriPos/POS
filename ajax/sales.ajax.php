<?php
    require_once "../models/connection.php";
    if (isset($_POST['InvoiceID'])) {

        $stmt = connection::connect()->prepare("SELECT p.product, p.barcode, p.saleprice, si.Quantity, si.discountid FROM saleitems si JOIN products p ON si.ProductID = p.id JOIN invoices i ON si.SaleID = i.InvoiceID WHERE i.InvoiceID = :InvoiceID");

        $stmt->bindParam(":InvoiceID", $_POST['InvoiceID'], PDO::PARAM_STR);

        $stmt->execute();
        
        $result = $stmt->fetchAll();
        
        echo json_encode($result);
    }

    if (isset($_POST['customerid'])) {

        $stmt = connection::connect()->prepare("SELECT name FROM customers WHERE customer_id = :customer_id");

        $stmt->bindParam(":customer_id", $_POST['customerid'], PDO::PARAM_STR);

        $stmt->execute();
        
        $result = $stmt->fetchAll();
        
        echo json_encode($result);
    }