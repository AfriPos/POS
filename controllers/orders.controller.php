<?php

class orderController{

	/*=============================================
   SET THE STORE ID
   =============================================*/
	
    static private $storeid;

	public static function initialize() {
		if ($_SESSION['storeid'] != null) {
			self::$storeid = $_SESSION['storeid'];
		} else {
			echo "<script>
				window.onload = function() {
					Swal.fire({
						title: 'No store is selected',
						text: 'Redirecting to Dashboard',
						icon: 'error',
						showConfirmButton: false,
						timer: 2000 // Display alert for 2 seconds
					}).then(function() {
						// After the alert is closed, redirect to the dashboard
						window.location= 'dashboard';
					});
				};
				</script>";
			exit; // Adding exit to stop further execution after the redirection
		}
	}
    

    /*=============================================
    CREATE ORDER
    =============================================*/
    static public function ctrCreateOrder(){
        self::initialize();

        if(isset($_POST['addproducts'])){
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
        
            $table = 'orders';
				
            $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
            $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
            $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
            $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
            $orderid = $randomNumber . "-" . $current_time_formatted;

            $data = array("orderid" => $orderid,
                            "supplier" => $_POST["supplier"],
                            "products" => $_POST["products"],
                            "date" => $dateTimeStr,
                            "status" => 0,
                            "total" => $_POST["total"],
                            "storeid" => self::$storeid);
            
            $answer = OrdersModel::mdlAddOrder($table, $data);
        
            $suppliertable = "suppliers";
            $item = "supplierid";
            $value = $data['supplier'];
		    $supplier = supplierModel::mdlShowSuppliers($suppliertable, $item, $value);
            
            if($answer){
                $jsonData = $_POST["products"];
                $dataArray = json_decode($jsonData, true);
                
                $table = "order_items";
                $orderId = $answer; // Assuming $answer holds the order ID
                
                foreach ($dataArray as $product) {
                    $productId = $product['id'];
                    $quantity = $product['Quantity'];
                    
                    $data = array(
                        "order_id" => $orderId,
                        "product_id" => $productId,
                        "quantity" => $quantity
                    );
                
                    $order = OrdersModel::mdlOrderitems($table, $data);
                }
                if ($order == "ok") {
                    if ($_SESSION['userId'] != 404) {
                        // Create an array with the data for the activity log entry
                        $logdata = array(
                            'UserID' => $_SESSION['userId'],
                            'ActivityType' => 'Order',
                            'ActivityDescription' => 'User ' . $_SESSION['username'] . ' created an order to supplier ' .$supplier['name']. ' for the following products; ' . $_POST['products'] . '.',
                            'storeid' => self::$storeid,
                            'TimeStamp' => $dateTimeStr
                        );
                        // Call the ctrCreateActivityLog() function
                        activitylogController::ctrCreateActivityLog($logdata);
                    }

                    echo '<script>
                        
                    Swal.fire({
                        icon: "success",
                        title: "Order has been successfully made.",
                        showConfirmButton: false, // Hide the confirm button
                        timer: 2000, // Set the timer to 2000 milliseconds (2 seconds)
                    
                    }).then(function (result) {
                        // This part will be triggered after the Swal is automatically closed
                        window.location = "orders";
                    });
                    
                        
                    </script>';
                }
    
            }

        }

    }

    /*=============================================
    SHOW ORDERS
    =============================================*/
    static public function ctrShowOrders($item, $value){
        
        $table = 'orders';
        
        $data = OrdersModel::mdlShowOrders($table, $item, $value);
        
        return $data;
    }

    /*=============================================
    EDIT ORDERS
    =============================================*/
    static public function ctrEditOrders(){
        self::initialize();
        
        if (isset($_POST["editStatus"])){
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
            
            $table = 'orders';

            $data = array("status" => $_POST["status"],
                            "id" => $_POST["order_id"]);
            
            if ($data["status"] == 1) {
                $status = "Delivered";
            }elseif ($data["status"] == 2) {
                $status = "Canceled";
            }
    
            
            $item = "orderid";
            $value = $_POST["order_id"];

            $orders = orderController::ctrShowOrders($item, $value);

            $table = "order_items";
            $item = "order_id";
            $value = $_POST["order_id"];
            $fetchAll=true;

            $orderitems = OrdersModel::mdlShowOrderitems($table, $item, $value, $fetchAll);
            var_dump($orderitems);
            
            foreach ($orderitems[0] as $order) {
                // Accessing manufacturing and expiring dates from each row
                // $manufacturingDate = $order['manufacturing_date'];
                // $expiringDate = $order['expiring_date'];
                
                // Your other code here...

                // Adding JavaScript for Sweet Alert
                echo "
                    <script>
                        // Assuming you've included Sweet Alert script in your project

                        // Function to show Sweet Alert and collect dates
                        function showDateAlert() {
                            Swal.fire({
                                title: 'Enter Manufacturing and Expiring Dates',
                                html: `<label for='manufacturingDate'>Manufacturing Date</label>
                                    <input type='date' id='manufacturingDate' class='swal2-input'>
                                    <label for='expiringDate'>Expiring Date</label>
                                    <input type='date' id='expiringDate' class='swal2-input'>`,
                                confirmButtonText: 'Save',
                                preConfirm: () => {
                                    // Accessing user input
                                    const manufacturingDateInput = document.getElementById('manufacturingDate').value;
                                    const expiringDateInput = document.getElementById('expiringDate').value;

                                    // You can send this data back to the server using AJAX or handle it as needed
                                    // For now, let's just log the values
                                    console.log('Manufacturing Date:', manufacturingDateInput);
                                    console.log('Expiring Date:', expiringDateInput);
                                }
                            });
                        }

                        // Call the function when needed, for example, on button click
                        // You can customize this based on your UI
                        showDateAlert();
                    </script>
                ";
            }
            // $answer = OrdersModel::mdlEditOrder($table, $data);
            
    
            // if($answer == "ok"){
			// 	if ($_SESSION['userId'] != 404) {
            //         // Create an array with the data for the activity log entry
            //         $logdata = array(
            //             'UserID' => $_SESSION['userId'],
            //             'ActivityType' => 'Order',
            //             'ActivityDescription' => 'User ' . $_SESSION['username'] . ' changed the status of order ' .$data['id']. ' to ' . $status . '.',
            //             'itemID' => $data['id'],
            //             'storeid' => self::$storeid,
            //             'TimeStamp' => $dateTimeStr
            //         );
            //         // Call the ctrCreateActivityLog() function
            //         activitylogController::ctrCreateActivityLog($logdata);
            //     }
    
            //     echo'<script>
    
            //     Swal.fire({
            //                 icon: "success",
            //                 title: "Status changed to ' . $status . ' for order number <br/>" + \'' . $data['id'] . '\',
            //                 showConfirmButton: false, // Hide the confirm button
            //                 timer: 2000, // Set the timer to 2000 milliseconds (2 seconds)
                        
            //             }).then(function (result) {
            //                 // This part will be triggered after the Swal is automatically closed
            //                 window.location = "vieworders";
            //             });
    
            //         </script>';
    
            // }

        }

    }
    
}