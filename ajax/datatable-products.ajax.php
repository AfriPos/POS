<?php

require_once "../controllers/product.controller.php";
require_once "../models/product.model.php";

require_once "../controllers/categories.controller.php";
require_once "../models/categories.models.php";

class productsTable {
    /*=============================================
     SHOW PRODUCTS TABLE
    =============================================*/
    public function showProductsTable() {
        $item = null;
        $value = null;
        $order='id';

        $products = productController::ctrShowProducts($item, $value, $order);

        if (count($products) == 0) {
            $jsonData = '{"data":[]}';
            echo $jsonData;
            return;
        }

        $jsonData = '{
            "data":[';

        foreach ($products as $product) {
            /*=============================================
            We bring the image
            =============================================*/
            $image = "<img src='".$product["image"]."' width='40px'>";

            /*=============================================
            We bring the category
            =============================================*/
            $item = "id";
            $value = $product["idCategory"];
            $categories = categoriesController::ctrShowCategories($item, $value);

            /*=============================================
            Stock
            =============================================*/
            if ($product["stock"] <= 10) {
                $stock = "<button class='btn btn-danger'>".$product["stock"]."</button>";
            } elseif ($product["stock"] > 11 && $product["stock"] <= 15) {
                $stock = "<button class='btn btn-warning'>".$product["stock"]."</button>";
            } else {
                $stock = "<button class='btn btn-success'>".$product["stock"]."</button>";
            }

            /*=============================================
            ACTION BUTTONS
            =============================================*/
            $buttons = "<div class='btn-group'><button class='btn btn-warning btnEditProduct' id='".$product["barcode"]."' data-toggle='modal' data-target='#modalEditProduct'><i class='fa fa-edit'></i></button><button class='btn btn-danger btnDeleteProduct' id='".$product["id"]."' barcode='".$product["barcode"]."' image='".$product["image"]."'><i class='fa fa-times'></i></button></div>";

            $jsonData .= '[
                "'.$product["id"].'",
                "'.$product["barcode"].'",
                "'.$product["product"].'",
                "'.$categories["Category"].'",
                "'.$product["description"].'",
                "'.$stock.'",
                "ksh '.$product["purchaseprice"].'",
                "ksh '.$product["saleprice"].'",
                "'.$image.'",
                "'.$product["date"].'",
                "'.$buttons.'"
            ],';
        }

        $jsonData = rtrim($jsonData, ',');
        $jsonData .= ']}';

        echo $jsonData;
    }
}


/*=============================================
ACTIVATE PRODUCTS TABLE
=============================================*/ 
$activateProducts = new productsTable();
$activateProducts -> showProductsTable();
