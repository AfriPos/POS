<?php
// Set the default timezone to Nairobi
date_default_timezone_set('Africa/Nairobi');
require_once "../controllers/orders.controller.php";
require_once "../models/orders.model.php";
require_once "../models/product.model.php";

class AjaxOrders{
    public $orderid;
    public $batchId;
    public $batchitems;
    public $batchquantity;
    public $orderId;
    public $productId;
    public $serialNumber;

    public function ajaxFetchOrder(){
        
        $table = "order_items";
        $item = ["order_id", "product_id"];
        $value = [$this->orderid, $this->productId];
        
        $query = "SELECT * FROM $table WHERE $item[0] = :item1 AND $item[1] = :item2";
        $params = array(":item1" => $value[0], ":item2" => $value[1]);
        $orders = OrdersModel::ctrCustomQuery($query, $params);

		echo json_encode($orders);

    }

    public function ajaxFetchBatchItems(){
        
        $table = "batch_items";
        $item = "serialNumber";
        $value = $this->serialNumber;
        $options = null;

        $batchitems = OrdersModel::mdlShowBatch($table, $item, $value, $options);

		echo json_encode($batchitems);

    }


    public function ajaxFetchOrderproducts(){
        
        $orderid = $this->orderid;
        $query = "SELECT oi.product_id, oi.quantity, p.product FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = :orderid";
        $params = array(':orderid' => $orderid);
        $orderitems = OrdersModel::ctrCustomQuery($query, $params);
		echo json_encode($orderitems);

    }

    public function ajaxCreateBatch(){
        $table = "batches";
        // Create a DateTime object with the current date and time in Nairobi timezone
        $dateTime = new DateTime();
        // Format the DateTime as a string
        $datecreated = $dateTime->format('Y-m-d H:i:s');
        $batchData = array(
            'batch_id' => $this->batchId,
            'quantity' => $this->batchquantity,
            'orderId' => $this->orderId,
            'product_id' => $this->productId,
            'datecreated' => $datecreated
            // Add other batch-related data here
        );
        $item = ["order_id", "product_id"];
        $value = [$this->orderId, $this->productId];
        $options = "duplicates";
        // Check if a batch with the same orderId already exists
        $existingBatch = OrdersModel::mdlShowBatch($table, $item, $value, $options);

        if (!$existingBatch) {
            // Create the batch
            $batch = OrdersModel::mdlCreateBatch($table, $batchData); // Replace with your actual model function

            // update product quantity
            $table = "products";
            $item1 = 'stock';
            $incrementBy = $this->batchquantity;
            $value = $this->productId;
            
            $query = "UPDATE $table SET $item1 = $item1 + :incrementBy WHERE id = :id";
            $params = array(':id' => $value, 'incrementBy' => $incrementBy);
            $updatestock = OrdersModel::ctrCustomQuery($query, $params);

            $batchtable = 'batches';
            $ordertable = 'order_items';
            $item = "order_id";
            $value = $this->orderId;
            $options = null;
            $fetchAll = true;
            $batches = OrdersModel::mdlShowBatch($batchtable, $item, $value, $options);
            $orders = OrdersModel::mdlShowOrderitems($ordertable, $item, $value, $fetchAll);

            if (count($batches) == count($orders)) {
                $table = 'orders';
                $data = array("status" => 1, "id" => $this->orderId);
                OrdersModel::mdlEditOrder($table, $data);
            }


            if ($this->batchitems) {
                if ($batch == "ok") {
                    $table = "batch_items";
                    // Retrieve batchItems from the FormData
                    $batchItemsJson = $this->batchitems;

                    // Decode the JSON string into an array
                    $batchItems = json_decode($batchItemsJson, true);
                    // Batch created successfully, now add batch items
                    foreach ($batchItems as $product) {
                        $batchItemData = array(
                            'batch_id' => $this->batchId,
                            'serial_number' => $product['serialNumber'],
                            'manufacturing_date' => $product['manufacturingDate'],
                            'expiry_date' => $product['expiryDate'],
                            // Add other batch item-related data here
                        );

                        // Add the batch item
                        OrdersModel::mdlAddBatchitems($table, $batchItemData); // Replace with your actual model function
                        
                    }

                    echo json_encode(array('status' => 'success', 'message' => 'Batch created successfully'));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Error creating batch.'));
                }

            } else {
                echo json_encode(array('status' => 'success', 'message' => 'Batch created successfully'));
            }

        } else {
            echo json_encode(array('status' => 'error', 'message' => 'The Batch has already been created.'));
        }

    }
}

if (isset($_POST['orderid']) && isset($_POST['ProductId'])) {
    $fetchOrder = new AjaxOrders();
    $fetchOrder -> orderid = $_POST["orderid"];
    $fetchOrder -> productId = $_POST["ProductId"];
    $fetchOrder -> ajaxFetchOrder();
}

if (isset($_POST['serialNumber'])) {
    $fetchOrder = new AjaxOrders();
    $fetchOrder -> serialNumber = $_POST["serialNumber"];
    $fetchOrder -> ajaxFetchBatchItems();
}

if (isset($_POST["orderId"])) {
    $fetchOrderproducts = new AjaxOrders();
    $fetchOrderproducts -> orderid = $_POST["orderId"];
    $fetchOrderproducts -> ajaxFetchOrderproducts();
}

if (isset($_POST["batchId"]) && isset($_POST["batchItems"]) && isset($_POST["quantity"]) && isset($_POST["OrderId"]) && isset($_POST["productId"])){
    $createBatch = new AjaxOrders();
    $createBatch -> batchId = $_POST["batchId"];
    $createBatch -> batchitems = $_POST["batchItems"];
    $createBatch -> batchquantity = $_POST["quantity"];
    $createBatch -> orderId = $_POST["OrderId"];
    $createBatch -> productId = $_POST["productId"];
    $createBatch -> ajaxCreateBatch();
}

if (isset($_POST["batchId"]) && isset($_POST["quantity"]) && isset($_POST["OrderId"]) && isset($_POST["productId"])){
    $createBatch = new AjaxOrders();
    $createBatch -> batchId = $_POST["batchId"];
    $createBatch -> batchquantity = $_POST["quantity"];
    $createBatch -> orderId = $_POST["OrderId"];
    $createBatch -> productId = $_POST["productId"];
    $createBatch -> ajaxCreateBatch();
}