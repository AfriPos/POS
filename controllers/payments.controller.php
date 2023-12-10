<?php

class PaymentController
{

    public static function generateReceiptHtml($paymentId)
    {
        include_once './models/connection.php';
        // Adjusted receipt HTML
        $receiptHtml = "
            <html>
                <head>
                    <title>$paymentId</title>
                </head>
                <body>";
        $table = 'payments';
        $item = 'paymentid';
        $value = $paymentId;
        $paymentData = PaymentModel::mdlShowPayments($table, $item, $value);

        // var_dump($paymentData);

        $table2 = 'invoices';
        $item2 = 'InvoiceID';
        $value2 = $paymentData['InvoiceID'];
        $order = 'id';
        $invoices = InvoiceModel::mdlShowInvoices($table2, $item2, $value2);

        $table = 'sales';
        $item = 'SaleID';
        $value = $paymentData['SaleID'];
        $sale = InvoiceModel::mdlShowSales($table, $item, $value);

        $table3 = "store";
        $item3 = "store_id";
        $value3 = $paymentData['StoreID'];
        $stores = storeModel::mdlShowStores($table3, $item3, $value3);

        // var_dump($stores);

        $table = 'taxes';
        $item2 = 'null';
        $value2 = 'taxId';
        $tax = TaxModel::mdlShowTax($table, $item2, $value2);

        $table = 'saleitems';
        $item = 'SaleID';
        $value = $sale['SaleID'];
        $saleitems = InvoiceModel::mdlShowSalesItems($table, $item, $value);

        $table = 'itemtaxes';
        $item = 'TaxID';
        $value = $sale['taxid'];
        $itemtaxes = InvoiceModel::mdlShowItemTaxes($table, $item, $value);


        // Get the current timestamp in your timezone
        $timestamp = time();

        // Generate a random 4-digit number
        $randomNumber = mt_rand(1000, 9999);

        // Generate the receipt number based on the current timestamp and random number
        $receiptNumber = date('YmdHis', $timestamp) . $randomNumber;
        // Prepare the SQL query with a placeholder


        $receiptHtml .= '
                        <div style="text-align:center;">
                        <h2>' . $stores[0]['store_name'] . '</h2>
                        <p>' . $stores[0]['store_address'] . '<br>' . $stores[0]['email'] . '<br>' . $stores[0]['contact_number'] . '</p>
                    </div>
                    <p>Receipt Number: ' . $receiptNumber . '</p>
                    <p>Date: ' . $paymentData['PaymentDate'] . '</p>
                    <p>Store: ' . $stores[0]['store_name'] . '</p>

                    <hr style="border: none; border-top: 1px dashed #000;">
                    
                        <table>
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Each</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>';
        $totalAmount = 0;
        $vatAmounts = array();
        foreach ($saleitems as $item) {
            $id = $item['ProductID'];

            $itemKey = 'id';
            $value = $id;
            $order = 'id';
            $product = productController::ctrShowProducts($itemKey, $value, $order);

            $productName = $product['product'];
            $quantity = $item['Quantity'];
            // Get the tax rate from the product
            $vatRate = $product['taxId'];

            $table = 'itemdiscounts';
            $itemkey2 = 'DiscountID';
            $valuekey2 = $item['discountid'];
            $itemdiscount = InvoiceModel::mdlShowItemDiscounts($table, $itemkey2, $valuekey2);

            $discountValue = $itemdiscount[0]['DiscountValue'];
            $price = ($item['Subtotal'] - $discountValue);

            $totalAmount += $price;

            $receiptHtml .= '
            <tr>
                <td style="white-space: nowrap;">' . $productName . '</td>
            </tr>
            <tr>
                <td style="font-size: 5px;">' . $product['barcode'] . '</td>
                <td>' . $quantity . '</td>
                <td>' . number_format($product['saleprice'], 2) . '</td>
                <td>' . number_format($price, 2) . '</td>
            </tr>';
            // Calculate the VAT amount based on the product's VAT rate
            $subamount = $price / (($vatRate / 100) + 1);
            $vatAmount = $price - $subamount;

            // Accumulate VAT amounts for each VAT rate
            if (isset($vatAmounts[$vatRate])) {
                $vatAmounts[$vatRate] += $vatAmount;
            } else {
                $vatAmounts[$vatRate] = $vatAmount;
            }
        }

        $receiptHtml .= '</tbody>
        </table>
        ';
        $cashPaid = 0;
        $mpesaPay = 0;
        $pointPay = 0;
        $change = 0;

        if (($paymentData['InvoiceID'])) {
            $amount = $paymentData['Amount'];
            if ($paymentData['PaymentMethod'] == 'Cash') {
                $cashPaid += $amount;
            }
            if ($paymentData['PaymentMethod'] == 'mpesa') {
                $mpesaPay += $amount;
            }
            if ($paymentData['PaymentMethod'] == 'points') {
                $pointPay += $amount;
            }
        }
        // Calculate the change
        if ($cashPaid > $totalAmount) {
            $change = $cashPaid - $totalAmount;
        }

        $receiptHtml .= '  <hr style="border: none; border-top: 1px dashed #000;">
            <div class="paydetails" style="margin-top:5px;">
        <table class="Totals">
            <tbody>
                
                <tr>
                    <td>Total:</td>
                    <td>' . number_format($totalAmount, 2) . '</td>
                </tr>
                 <tr>
                    <td>Cash Paid:</td>
                    <td>' . number_format($cashPaid, 2) . '</td>
                </tr>
                <tr>
                    <td>Mpesa Pay:</td>
                    <td>' . number_format($mpesaPay, 2) . '</td>
                </tr>';

        // Add the "Points" row only when $pointspay is greater than zero
        if ($pointPay > 0) {
            $receiptHtml .= '<tr>
                    <td>Points:</td>
                    <td>' . number_format($pointPay, 2) . '</td>
                </tr>';
        }

        $receiptHtml .= '    <tr>
                    <td>Change:</td>
                    <td>' . number_format($change, 2) . '</td>
                </tr>
            </tbody>
        </table>
        <hr style="border: none; border-top: 1px dashed #000;">
        </div>
   ';

        $itemSeller = 'userId';
        $valueSeller = $_SESSION["userId"];
        $seller = userController::ctrShowUsers($itemSeller, $valueSeller);
        $totalQty = 0;
        foreach ($saleitems as $item) {
            $quantity = $item['Quantity'];
            $totalQty += $quantity;
        }
        $receiptHtml .= '
    <table class="servedby">
    <tbody>
    <tr>
    <td>
    Total Qty: ' . $totalQty . ' units
    </td>
    </tr>
    <tr>
    <td>
    Served by: ' . $seller['name'] . '
    </td>
    </tr>
    </tbody>
    </table>
    
';

        $mpesaDetailsHtml = '';
        if ($paymentData['PaymentMethod'] == 'mpesa') {
            $mpesaDetailsHtml = '
            <hr style="border: none; border-top: 1px dashed #000;">
        <div style="border: 1px solid #000;">
            <table>
                <tbody>
                    <tr>
                       <td><strong>Mpesa Details:</strong></td>
                    </tr>
                    <tr>
                       <td>Name:</td>
                    </tr>
                    <tr>
                       <td>Mobile No:</td>
                    </tr>
                    <tr>
                       <td>Mpesa Id:</td>
                    </tr>
                    <tr>
                       <td>Ref No:</td>
                    </tr>
                    <tr>
                       <td>Amount:</td>
                    </tr>
                </tbody>
            </table>
        </div>';
        }
        $receiptHtml .= $mpesaDetailsHtml;
        $receiptHtml .= '
                <div class="taxes">
                <table class="taxation-table">
                    <thead>
                    <tr>
                        <th>Rate</th>
                        <th>Amt</th>
                        <th>VAT Amt</th>
                    </tr>
                    </thead>
                    <hr style="border: none; border-top: 1px dashed #000;">
                    <tbody>';
        foreach ($vatAmounts as $vatRate => $vatAmount) {
            // Calculate the total amount for the current tax rate
            $taxTypeTotalAmount = 0;

            // Iterate over each product and sum up the amounts for the current tax rate
            foreach ($saleitems as $item) {
                $id = $item['ProductID'];
                $quantity = $item['Quantity'];

                $itemKey = 'id';
                $value = $id;
                $order = 'id';
                $product = productController::ctrShowProducts($itemKey, $value, $order);

                // Check if the product's tax rate matches the current vatRate
                if ($product['taxId'] == $vatRate) {
                    $discountValue = $itemdiscount[0]['DiscountValue'];
                    $price = ($item['Subtotal'] - $discountValue);

                    $taxTypeTotalAmount += $price;
                }
            }

            $receiptHtml .= '
                        <tr>
                            <td>' . $vatRate . '%</td>
                            <td>' . number_format($taxTypeTotalAmount, 2) . '</td>
                            <td>' . number_format($vatAmount, 2) . '</td>
                        </tr>';
        }
        $receiptHtml .= '</tbody>
            </table>
            <hr style="border: none; border-top: 1px dashed #000;">
            </div>';

        // Flag to check if any items have a discount
        $hasDiscount = false;
        $totalDiscountAmount = 0;

        foreach ($saleitems as $item) {
            $id = $item['ProductID'];
            $itemKey = 'id';
            $value = $id;
            $order = 'id';
            $product = productController::ctrShowProducts($itemKey, $value, $order);
            $productName = $product['product'];
            $quantity = $item['Quantity'];

            $table = 'itemdiscounts';
            $itemkey2 = 'DiscountID';
            $valuekey2 = $item['discountid'];
            $itemdiscount = InvoiceModel::mdlShowItemDiscounts($table, $itemkey2, $valuekey2);

            // Check if the discount record exists
            if (!empty($itemdiscount)) {
                $discountValue = $itemdiscount[0]['DiscountValue'];

                $discount = $discountValue;
                $totaldiscount = $discount * $quantity;

                if ($discount > 0) {
                    // Set the flag to true if any items have a discount
                    $hasDiscount = true;

                    // Accumulate the total discount amount for each item
                    $totalDiscountAmount += $totaldiscount;

                    $receiptHtml .= '
                <strong>DISCOUNT REWARDED</strong>
                <hr style="border: none; border-top: 1px dashed #000;">
                <table>
                    <thead>
                        <tr>
                            <td>
                                Item
                            </td>
                            <td>
                                Discount
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                ' . $productName . '
                            </td>
                            <td>
                                ' . number_format($totaldiscount, 2) . '
                            </td>
                        </tr>';
                }
            }
        }

        // Display discount information only if there is at least one item with a discount and the total discount is greater than zero
        if ($hasDiscount && $totalDiscountAmount > 0) {
            $receiptHtml .= '
                        <tr>
                            <td colspan="2">
                                <hr style="border: none; border-top: 1px dashed #000; width: 100%;">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Total Discount:
                            </td>
                            <td>
                                ' . number_format($totalDiscountAmount, 2) . '
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                ';
        }

        $points = '';
        $item7 = 'pointId';
        $value7 = $paymentData['LoyaltyID'];
        $loyalty = loyaltyController::ctrShowLoyaltyPoints($item7, $value7);
        // if($loyalty){
        $item6 = 'customer_id';
        $value6 = $loyalty['customer_id'];
        $initialloyalty = loyaltyController::ctrShowLoyaltyPoints($item6, $value6, true);
        if (($loyalty['customer_id'])) {

            $totalPointsEarned = 0;
            $totalredeemedPoints = 0;
            foreach ($initialloyalty as $loyaltyData) {
                // Fetch PointsEarned from each element in the array
                $pointsEarned = floatval($loyaltyData['PointsEarned']);

                // Accumulate the PointsEarned values to calculate the total
                $totalPointsEarned += $pointsEarned;

                // Fetch PointsRedeemed from each element in the array
                $redeemedPoints = floatval($loyaltyData['PointsRedeemed']);

                // Accumulate the PointsEarned values to calculate the total
                $totalredeemedPoints += $redeemedPoints;
            }

            $totalPoints = $totalPointsEarned - $totalredeemedPoints;
            $initialTotalPointsEarned = $totalPoints - $loyalty['PointsEarned'];
            $totalAvailablePoints = $initialTotalPointsEarned + $loyalty['PointsEarned'];


            // }
            // $totalLoyalty=$totalLoyaltyAmount+$loyalty['PointsEarned'];

            $points = '
            <hr style="border: none; border-top: 1px dashed #000;">
                <h4 style="text-align:center;">LOYALTY POINTS</h4>
             <hr style="border: none; border-top: 1px dashed #000;">
            
            <table>
                    <tbody>
                    <tr>
                        <td style="white-space: nowrap;">
                            Loyalty Code
                        </td>
                        <td>
                            : ' . $paymentData['LoyaltyID'] . '
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Prev. Points Bal
                        </td>
                        <td>
                        : ' . $initialTotalPointsEarned . ' POINTS
                        </td>
                    </tr>
                    <tr>
                    <td>
                        Points Earned Now
                    </td>
                    <td>
                    : ' . $loyalty['PointsEarned'] . ' POINTS
                    </td>
                </tr>
                    <tr>
                    <td>
                        Total 
                    </td>
                    <td>
                        : ' . $totalAvailablePoints . ' POINTS
                    </td>
                </tr>
                    </tbody>
                </table>
                ';
        }

        $receiptHtml .= $points;



        $receiptHtml .= "
        </body>
    </html>
";

        // var_dump($paymentId);
        $sql = "UPDATE payments SET ReceiptNumber = :receiptNumber WHERE PaymentID =:paymentId";


        try {
            // Connect to the database and prepare the statement
            $stmt = connection::connect()->prepare($sql);

            // Bind the values to the placeholders
            $stmt->bindParam(':receiptNumber', $receiptNumber, PDO::PARAM_STR);
            $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_STR);

            // Execute the statement
            $stmt->execute();

            // echo "Update successful";
        } catch (PDOException $e) {
            echo "Update failed: " . $e->getMessage();
        }



        return $receiptHtml;
    }




    /*=============================================
   SET THE STORE ID
   =============================================*/

    static private $storeid;

    public static function initialize()
    {
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
	ADD PAYMENT AND INVOICE
	=============================================*/
    public function addPayment()
    {

        self::initialize();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');

            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();

            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');

            if (isset($_POST['saveorder'])) {
                date_default_timezone_set('africa/nairobi');

                // Use the payment ID to insert into the invoices table
                $invoiceModel = new InvoiceModel();

                $productsList = json_decode($_POST["productsList"], true);
                // echo json_encode($productsList);

                $totalPurchasedProducts = array();

                foreach ($productsList as $key => $value) {

                    array_push($totalPurchasedProducts, $value["Quantity"]);

                    $tableProducts = "products";

                    $item = "id";
                    $valueProductId = $value["id"];
                    $order = "id";

                    $getProduct = productModel::mdlFetchProducts($tableProducts, $item, $valueProductId, $order);

                    $item1a = "sales";
                    $value1a = $value["Quantity"] + $getProduct["sales"];

                    $newSales = productModel::mdlUpdateProduct($tableProducts, $item1a, $value1a, $valueProductId);

                    $item1b = "stock";
                    $value1b = $getProduct["stock"] - $value["Quantity"];

                    $newStock = productModel::mdlUpdateProduct($tableProducts, $item1b, $value1b, $valueProductId);
                    if ($newStock == "ok") {

                        $order = "id";
                        $table = "products";
                        $stock = productModel::mdlFetchProducts($table, $item, $valueProductId, $order);
                        if ($stock && $stock["stock"] <= 15) {
                            $currentDateTime = date('Y-m-d H:i:s');
                            // $storeid = self::$storeid;
                            $data = array(
                                "message" => $stock["product"] . " is running low on stock",
                                "datetime" => $currentDateTime,
                                "name" => "System",
                                "type" => "Stock notification," . $valueProductId,
                                "storeid" => self::$storeid,
                                "userid" => $_SESSION['userId'],
                            );
                            $element = "others";
                            $table = "customers";
                            $countAll = null;
                            $organisationcode = $_SESSION['organizationcode'];
                            $package = packagevalidateController::ctrPackageValidate($element, $table, $countAll, $organisationcode);
                            if ($package) {
                                $notification = notificationController::ctrCreateNotification($data);
                            }
                        }
                    }
                }

                $loyaltySetting = loyaltyController::ctrShowLoyaltyValue();
                $CustomerDetailsValue = $loyaltySetting[4]['SettingValue'];
                $LoyaltypointsValue = $loyaltySetting[3]['SettingValue'];

                $currentDate = date('Y-m-d H:s:i');
                $future_date = date("Y-m-d H:s:i", strtotime($currentDate . " +15 days"));

                // Convert 'dueamount' to a float
                $dueAmount = $_POST['dueamount'];

                if ($dueAmount >= 0) {
                    $invoiceDueAmount = 0;
                    $invoiceBalace = $_POST['dueamount'];
                } elseif ($dueAmount < 0) {
                    $invoiceDueAmount = $_POST['dueamount'];
                    $invoiceBalace = 0;
                }

                $paymentMethod = $_POST['r3'];
                if (isset($_POST['selectcustomer'])) {
                    if ($CustomerDetailsValue == 1) {
                        if ($paymentMethod == "Cash") {
                            $invoiceCustomerid = $_POST['selectcustomer'];
                        } elseif ($paymentMethod == "points") {
                            $invoiceCustomerid = $_POST['pselectcustomer'];
                        }
                    }
                } else {
                    $invoiceCustomerid = 0;
                }

                $productsList = $_POST['productsList'];
                $invoiceStartDate = $currentDate;
                $invoiceDueDate = $future_date;
                $invoiceTotalTax = $_POST['totaltax'];
                $invoiceSubtotal = $_POST['subtotal'];
                $invoiceTotal = $_POST['total'];
                $invoiceDiscount = $_POST['totaldiscount'];
                $invoiceUserId = $_SESSION['userId'];
                $storeid = self::$storeid;
                $datecreated = date('Y-m-d H:i:s');

                // Add taxes to taxitem table
                $taxitemrandom = rand(1000, 9999);
                $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                $taxId = "TX-" . $taxitemrandom . $current_time_formatted;
                $invoiceModel->insertItemsTaxes($taxId, $invoiceTotalTax);

                $productspurchased = json_decode($_POST["productsList"], true);

                // Add sales to sales table
                $salerandom = rand(1000, 9999);
                $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                $SaleId = "Sale-" . $salerandom . $current_time_formatted;
                $invoiceModel->insertSales($SaleId, $invoiceStartDate, $invoiceTotal, $invoiceCustomerid, $taxId, $invoiceUserId, $storeid);

                foreach ($productspurchased as $key => $value) {
                    $productId = $value['id'];
                    $discountValue = $value['Discount'];
                    $quantity = $value['Quantity'];
                    $price = $value['salePrice'];
                    $subtotal = $quantity * $price;

                    // Add discounts to taxdiscount table
                    $discountitemrandom = rand(1000, 9999);
                    $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                    $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                    $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                    $discountId = "DS-" . $discountitemrandom . $current_time_formatted;
                    $invoiceModel->insertItemsDiscounts($discountId, $discountValue);

                    // Add sales to sales table
                    $saleitemrandom = rand(1000, 9999);
                    $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                    $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                    $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                    $itemid = "S.ITEM-" . $saleitemrandom . $current_time_formatted;

                    $invoiceModel->insertSalesItems($itemid, $SaleId, $productId, $quantity, $subtotal, $discountId);
                }
                // Generate the invoice ID
                $nextNumericPart = $invoiceModel->getNextInvoiceNumericPart(); // Get the next available numeric part
                $invoiceId = "INVC-" . str_pad($nextNumericPart, 8, '0', STR_PAD_LEFT);

                // Insert invoice data into the invoices table, linking it with the payment
                $invoiceModel->insertInvoice($invoiceId, $invoiceStartDate, $invoiceDueDate, $invoiceCustomerid, $invoiceTotal, $invoiceSubtotal, $invoiceBalace, $invoiceDueAmount, $invoiceUserId, $storeid);

                // Retrieve payment data from the form or request parameters
                $amount = $_POST['txtpaid'];
                // $topupPaymentMethod = $_POST['topup'];
                $PointsRedeemed = 0;

                // Create an instance of the PaymentModel
                $paymentModel = new PaymentModel();

                $element = "others";
                $table = "customers";
                $countAll = null;
                $organisationcode = $_SESSION['organizationcode'];
                $package = packagevalidateController::ctrPackageValidate($element, $table, $countAll, $organisationcode);

                if ($LoyaltypointsValue == 1 && $package) {
                    if ($paymentMethod == "Cash") {
                        $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
                        $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                        $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                        $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                        $loyaltyid = $randomNumber . $current_time_formatted;

                        // fetch the loyalty value
                        $table = "loyaltysettings";
                        $loyaltyvalue = LoyaltyModel::mdlShowLoyaltyValue($table);

                        $LoyaltyPointsAwarded = $invoiceTotal / intval($loyaltyvalue[0]['SettingValue']);
                        if (isset($_POST['selectcustomer'])) {
                            $customer_id = $_POST['selectcustomer'];
                        } else {
                            $customer_id = 0;
                        }


                        $loyaltydata = array(
                            "loyaltyid" => $loyaltyid,
                            "customer_id" => $customer_id,
                            "PointsEarned" => $LoyaltyPointsAwarded,
                            "PointsRedeemed" => $PointsRedeemed
                        );


                        $loyaltyPoint = loyaltyController::ctrAddLoyaltyPoints($loyaltydata);

                        // Generate the payment ID manually
                        $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
                        $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                        $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                        $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                        $paymentId = "CASH-" . $randomNumber . "-" . $current_time_formatted;


                        // Insert payment data into the payments table
                        $paymentModel->insertPayment($paymentId, $amount, $paymentMethod, $invoiceId, $storeid, $loyaltyid, $SaleId);
                    } elseif ($paymentMethod == "points") {
                        if (isset($_POST['topup'])) {
                            if ($_POST['topup'] == "topupCash") {
                                $totalForPoints = $_POST['topUpamount'];
                                $paymentMethod2 = "points";
                                $PointsRedeemed = $_POST['redeemedpoints'];

                                $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
                                $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                                $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                                $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                                $loyaltyid = $randomNumber . $current_time_formatted;
                                $loyaltyid2 = null;

                                // fetch the loyalty value
                                $table = "loyaltysettings";
                                $loyaltyvalue = LoyaltyModel::mdlShowLoyaltyValue($table);
                                // var_dump($loyaltyvalue);

                                $LoyaltyPointsAwarded = $totalForPoints / $loyaltyvalue[0]['SettingValue'];
                                $customer_id = $_POST['pselectcustomer'];

                                $loyaltydata = array(
                                    "loyaltyid" => $loyaltyid,
                                    "customer_id" => $customer_id,
                                    "PointsEarned" => $LoyaltyPointsAwarded,
                                    "PointsRedeemed" => $PointsRedeemed
                                );

                                $loyaltyPoint = loyaltyController::ctrAddLoyaltyPoints($loyaltydata);

                                // First payment method
                                $randomNumber1 = mt_rand(1000, 9999); // Generate a random 4-digit number for the first payment
                                $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                                $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                                $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                                $paymentId = "CASH-" . $randomNumber1 . "-" . $current_time_formatted;

                                // Insert data for the first payment method into the payments table
                                $paymentModel->insertPayment($paymentId, $amount, $_POST['topup'], $invoiceId, $storeid, $loyaltyid, $SaleId);

                                // Second payment method
                                $randomNumber2 = mt_rand(1000, 9999); // Generate a random 4-digit number for the second payment
                                $current_time = new DateTime("now", $timezone); // Get the current time again for the second payment
                                $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                                $paymentId2 = "POINT-" . $randomNumber2 . "-" . $current_time_formatted;

                                // Insert data for the second payment method into the payments table
                                $paymentModel->insertPayment($paymentId2, $amount, $paymentMethod2, $invoiceId, $storeid, $loyaltyid2, $SaleId);
                            } elseif ($_POST['topup'] == "topupM-pesa") {
                            }
                        } else {
                            $totalForPoints = $_POST['topUpamount'];
                            $paymentMethod2 = "points";
                            $PointsRedeemed = $_POST['redeemedpoints'];

                            $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
                            $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                            $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                            $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                            $loyaltyid = $randomNumber . $current_time_formatted;

                            // fetch the loyalty value
                            $table = "loyaltysettings";
                            $loyaltyvalue = LoyaltyModel::mdlShowLoyaltyValue($table);
                            // var_dump($loyaltyvalue);

                            $LoyaltyPointsAwarded = 0;
                            $customer_id = $_POST['pselectcustomer'];

                            $loyaltydata = array(
                                "loyaltyid" => $loyaltyid,
                                "customer_id" => $customer_id,
                                "PointsEarned" => $LoyaltyPointsAwarded,
                                "PointsRedeemed" => $PointsRedeemed
                            );

                            $loyaltyPoint = loyaltyController::ctrAddLoyaltyPoints($loyaltydata);

                            // Second payment method
                            $randomNumber2 = mt_rand(1000, 9999); // Generate a random 4-digit number for the second payment
                            $current_time = new DateTime("now", $timezone); // Get the current time again for the second payment
                            $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                            $paymentId = "POINT-" . $randomNumber2 . "-" . $current_time_formatted;

                            // Insert data for the second payment method into the payments table
                            $paymentModel->insertPayment($paymentId, $amount, $paymentMethod2, $invoiceId, $storeid, $loyaltyid, $SaleId);
                        }
                    } elseif ($paymentMethod == "M-pesa") {
                        require_once './stkpush/qr.php';
                        // Second payment method
                        $randomNumber3 = mt_rand(1000, 9999); // Generate a random 4-digit number for the second payment
                        $current_time = new DateTime("now", $timezone); // Get the current time again for the second payment
                        $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                        $loyaltyid = $randomNumber3 . $current_time_formatted;

                        // fetch the loyalty value
                        $table = "loyaltysettings";
                        $loyaltyvalue = LoyaltyModel::mdlShowLoyaltyValue($table);

                        $LoyaltyPointsAwarded = $invoiceTotal / intval($loyaltyvalue[0]['SettingValue']);
                        if (isset($_POST['selectcustomer'])) {
                            $customer_id = $_POST['selectcustomer'];
                        } else {
                            $customer_id = 0;
                        }


                        $loyaltydata = array(
                            "loyaltyid" => $loyaltyid,
                            "customer_id" => $customer_id,
                            "PointsEarned" => $LoyaltyPointsAwarded,
                            "PointsRedeemed" => $PointsRedeemed
                        );


                        $loyaltyPoint = loyaltyController::ctrAddLoyaltyPoints($loyaltydata);


                        // Second payment method
                        $randomNumber3 = mt_rand(1000, 9999); // Generate a random 4-digit number for the second payment
                        $current_time = new DateTime("now", $timezone); // Get the current time again for the second payment
                        $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                        $paymentId = "M-pesa-" . $randomNumber3 . "-" . $current_time_formatted;



                        $paymentModel->insertPayment($paymentId, $amount, $paymentMethod, $invoiceId, $storeid, $loyaltyid, $SaleId);

                        mobpay::payqr($storeid, $invoiceId, $amount);
                    } else {
                        // Generate the payment ID manually
                        $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
                        $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                        $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                        $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                        $paymentId = "CASH-" . $randomNumber . "-" . $current_time_formatted;
                        $loyaltyid = null;


                        // Insert payment data into the payments table
                        $paymentModel->insertPayment($paymentId, $amount, $paymentMethod, $invoiceId, $storeid, $loyaltyid, $SaleId);
                    }
                }
                // create an activitylog of the payment
                if ($paymentModel == true) {

                    if ($_SESSION['userId'] != 404) {
                        // Create an array with the data for the activity log entry
                        $data = array(
                            'UserID' => $_SESSION['userId'],
                            'ActivityType' => 'Sale',
                            'ActivityDescription' => 'User ' . $_SESSION['username'] . ' Processed transaction ' . $paymentId . '.',
                            'itemID' => $paymentId,
                            'TimeStamp' => $dateTimeStr
                        );
                        // Call the ctrCreateActivityLog() function
                        activitylogController::ctrCreateActivityLog($data);
                    }
                }


                // Redirect or display success message
                // echo "Payment and invoice added successfully!";
                //     echo "<script>
                //     var invoiceId = " . json_encode($invoiceId) . ";
                // 	window.open('views/pdfs/receipt.php?invoiceId=' + invoiceId, '_blank');
                // </script>";

                // Generate the receipt HTML
                $receiptHtml = $this->generateReceiptHtml($paymentId);

                echo "<script>
                var receiptHtml = " . json_encode($receiptHtml) . ";
                var receiptPopup = window.open('', '_blank');
                if (receiptPopup) {
                    receiptPopup.document.write(receiptHtml);
                    receiptPopup.document.close();
                    receiptPopup.onload = function () {
                        try {
                            receiptPopup.print();
                        } catch (error) {
                            console.error('Error while trying to print: ' + error.message);
                        }
                        // Close the popup window after a delay of 2 seconds
                        setTimeout(function () {
                            receiptPopup.close();
                        }, 2000);
                    };
                } else {
                    console.error('Failed to open popup window.');
                }
            </script>";
            }
        }
    }


    /*=============================================
	MAKE PAYMENT
	=============================================*/
    public function makePayment()
    {
        self::initialize();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');

            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();

            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');

            if (isset($_POST['makePayment']) && isset($_POST['r3'])) {

                // Retrieve payment data from the form or request parameters
                $amount = $_POST['payment'];
                $paymentMethod = $_POST['r3'];
                $invoiceId = $_POST['invoiceId'];
                $storeid = self::$storeid;


                $table = 'payments';
                $item = 'InvoiceID';
                $value = $invoiceId;
                $lastpayment = PaymentModel::mdlShowPayments($table, $item, $value);
                // var_dump($lastpayment);

                $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
                $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
                $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
                $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
                $loyaltyid = $randomNumber . $current_time_formatted;

                // Create an instance of the PaymentModel
                $paymentModel = new PaymentModel();

                if (isset($phoneNumber)) {

                    // fetch the loyalty value
                    $table = "loyaltysettings";
                    $loyaltyvalue = LoyaltyModel::mdlShowLoyaltyValue($table);
                    // var_dump($loyaltyvalue);

                    $LoyaltyPointsAwarded = $amount / $loyaltyvalue[0]['SettingValue'];

                    $loyaltydata = array(
                        "loyaltyid" => $loyaltyid,
                        "Phone" => $phoneNumber,
                        "PointsEarned" => $LoyaltyPointsAwarded,
                    );

                    $loyaltyPoint = loyaltyController::ctrAddLoyaltyPoints($loyaltydata);
                }

                // Create an instance of the PaymentModel
                $paymentModel = new PaymentModel();

                // Generate the payment ID manually
                $nextNumericPart = $paymentModel->getNextPaymentNumericPart(); // Implement this method in PaymentModel to get the next available numeric part
                $paymentId = "CASH-" . str_pad($nextNumericPart, 8, '0', STR_PAD_LEFT);

                // Insert payment data into the payments table
                $paymentModel->insertPayment($paymentId, $amount, $paymentMethod, $invoiceId, $storeid, $loyaltyid, $SaleId);

                if ($paymentModel == true) {

                    /*=============================================
                    EDIT INVOICE
                    =============================================*/

                    $table = "invoices";
                    $newDue = $_POST['due'] + $amount;

                    $data = array(
                        "newdue" => $newDue,
                        "invoiceid" => $_POST['invoiceId']
                    );

                    $answer = InvoiceModel::mdlEditInvoice($table, $data);

                    if ($answer == "ok") {

                        if ($_SESSION['userId'] != 404) {
                            // Create an array with the data for the activity log entry
                            $data = array(
                                'UserID' => $_SESSION['userId'],
                                'ActivityType' => 'Sale',
                                'ActivityDescription' => 'User ' . $_SESSION['username'] . ' Processed transaction ' . $data['invoiceid'] . '.',
                                'itemID' => $invoiceId,
                                'TimeStamp' => $dateTimeStr
                            );
                            // Call the ctrCreateActivityLog() function
                            activitylogController::ctrCreateActivityLog($data);
                        }
                        // Generate the receipt HTML
                        $receiptHtml = $this->generateReceiptHtml($paymentId);

                        echo "<script>
                        var receiptHtml = " . json_encode($receiptHtml) . ";
                        var receiptPopup = window.open('', '_blank');
                        if (receiptPopup) {
                            receiptPopup.document.write(receiptHtml);
                            receiptPopup.document.close();
                            receiptPopup.onload = function () {
                                try {
                                    receiptPopup.print();
                                } catch (error) {
                                    console.error('Error while trying to print: ' + error.message);
                                }
                                // Close the popup window after a delay of 2 seconds
                                setTimeout(function () {
                                    receiptPopup.close();
                                }, 2000);
                            };
                        } else {
                            console.error('Failed to open popup window.');
                        }
                    </script>";
                    }
                }
            }
        }
    }

    /*=============================================
	SHOW INVOICES
	=============================================*/

    public static function ctrShowInvoices($item, $value)
    {

        $table = "invoices";

        $answer = InvoiceModel::mdlShowInvoices($table, $item, $value);

        return $answer;
    }


    /*=============================================
	SHOW PAYMENTS
	=============================================*/

    public static function ctrShowPayments($item, $value)
    {

        $table = "payments";

        $answer = PaymentModel::mdlShowPayments($table, $item, $value);

        return $answer;
    }

    /*=============================================
	DATES RANGE
	=============================================*/

    public static function ctrSalesDatesRange($initialDate, $finalDate)
    {
        self::initialize();

        $table = "invoices";

        $answer = InvoiceModel::mdlSalesDatesRange($table, $initialDate, $finalDate, self::$storeid);

        return $answer;
    }
    /*=============================================
    DOWNLOAD EXCEL
    =============================================*/

    public function ctrDownloadReport()
    {
        self::initialize();

        $table = "invoices";

        if (isset($_GET["initialDate"]) && isset($_GET["finalDate"])) {

            $sales = InvoiceModel::mdlSalesDatesRange($table, $_GET["initialDate"], $_GET["finalDate"], self::$storeid);
        } else {
            $item = "store_id";
            $value = $_SESSION['storeid'];

            $sales = InvoiceModel::mdlShowInvoices($table, $item, $value);
        }

        /*=============================================
        WE CREATE EXCEL FILE
        =============================================*/

        $name = time() . '.xls';

        header('Expires: 0');
        header('Cache-control: private');
        header("Content-type: application/vnd.ms-excel"); // Excel file
        header("Cache-Control: cache, must-revalidate");
        header('Content-Description: File Transfer');
        header('Last-Modified: ' . date('D, d M Y H:i:s'));
        header("Pragma: public");
        header('Content-Disposition:; filename="' . $name . '"');
        header("Content-Transfer-Encoding: binary");

        echo utf8_decode("<table border='0'>
            <tr>
                <td style='font-weight:bold; border:1px solid #eee;'>Invoice</td>
                <td style='font-weight:bold; border:1px solid #eee;'>Seller</td>
                <td style='font-weight:bold; border:1px solid #eee;'>Quantity</td>
                <td style='font-weight:bold; border:1px solid #eee;'>Products</td>
                <td style='font-weight:bold; border:1px solid #eee;'>Tax</td>
                <td style='font-weight:bold; border:1px solid #eee;'>Subtotal</td>
                <td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td>
                <td style='font-weight:bold; border:1px solid #eee;'>Date</td>
            </tr>");

        foreach ($sales as $row => $item) {

            $customerName = isset($item["customername"]) ? $item["customername"] : "";
            $customer = PaymentController::ctrShowInvoices("invoiceId", $customerName);
            $customerName = isset($customer["customername"]) ? $customer["customername"] : "";

            $sellerId = isset($item["userId"]) ? $item["userId"] : "";
            $seller = userController::ctrShowUsers("userId", $sellerId);
            $sellerName = isset($seller["name"]) ? $seller["name"] : "";

            $products = json_decode($item["products"], true); // Decode the product field to retrieve the array of products

            $quantity = ""; // Variable to store the quantity
            $productDetails = ""; // Variable to store the product details

            if (is_array($products)) {
                foreach ($products as $product) {
                    $quantity .= $product["Quantity"] . "<br>"; // Extract the quantity and append to the quantity variable
                    $productDetails .= $product["product"] . "<br>"; // Extract the product name and append to the productDetails variable
                }
            }

            $paymentMethod = isset($item["paymentmethod"]) ? $item["paymentmethod"] : "";

            echo utf8_decode("<tr>
                <td style='border:1px solid #eee;'>" . $item["invoiceId"] . "</td>
                <td style='border:1px solid #eee;'>" . $sellerName . "</td>
                <td style='border:1px solid #eee;'>" . $quantity . "</td>
                <td style='border:1px solid #eee;'>" . $productDetails . "</td>
                <td style='border:1px solid #eee;'>Ksh " . number_format($item["totaltax"], 2) . "</td>
                <td style='border:1px solid #eee;'>Ksh " . number_format($item["subtotal"], 2) . "</td>
                <td style='border:1px solid #eee;'>Ksh " . number_format($item["total"], 2) . "</td>
                <td style='border:1px solid #eee;'>" . $item["datecreated"] . "</td>
            </tr>");
        }

        echo "</table>";
    }

    /*=============================================
	Adding TOTAL sales
	=============================================*/

    public static function ctrAddingTotalPayments($month, $storeid)
    {

        $table = "payments";

        $answer = PaymentModel::mdlAddingTotalPayments($table, $month, $storeid);

        return $answer;
    }


    /*=============================================
	Adding TOTAL sales
	=============================================*/

    public static function ctrAddingTotalSales($month)
    {

        $table = "invoices";

        $answer = InvoiceModel::mdlAddingTotalSales($table, $month);

        return $answer;
    }


    // mpesa payment
    public static function stkpush()
    {
        if (isset($_POST['phone'])) {
            date_default_timezone_set('Africa/Nairobi');

            # access token
            $consumerKey = '2FEEYG2mru3WUOsZOwSHA63GgLDqVhhI'; //Fill with your app Consumer Key
            $consumerSecret = 'G6f54z31OA7GOAPF'; // Fill with your app Secret

            # define the variales
            # provide the following details, this part is found on your test credentials on the developer account
            $BusinessShortCode = '174379';
            $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

            /*
            This are your info, for
            $PartyA should be the ACTUAL clients phone number or your phone number, format 2547********
            $AccountRefference, it maybe invoice number, account number etc on production systems, but for test just put anything
            TransactionDesc can be anything, probably a better description of or the transaction
            $Amount this is the total invoiced amount, Any amount here will be 
            actually deducted from a clients side/your test phone number once the PIN has been entered to authorize the transaction. 
            for developer/test accounts, this money will be reversed automatically by midnight.
          */

            $PartyA = $_POST['phone']; // This is your phone number, 
            $AccountReference = $_POST['phone'];
            $TransactionDesc = 'POS PAYMENT';
            $Amount = $_POST['amount'];

            # Get the timestamp, format YYYYmmddhms -> 20181004151020
            $Timestamp = date('YmdHis');

            # Get the base64 encoded string -> $password. The passkey is the M-PESA Public Key
            $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

            # header for access token
            $headers = ['Content-Type:application/json; charset=utf8'];

            # M-PESA endpoint urls
            $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
            $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

            # callback url
            $CallBackURL = 'https://mungatest.000webhostapp.com/callbackurl.php';

            $curl = curl_init($access_token_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
            $result = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $result = json_decode($result);
            $access_token = $result->access_token;
            curl_close($curl);

            # header for stk push
            $stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

            # initiating the transaction
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $initiate_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header

            $curl_post_data = array(
                //Fill in the request parameters with valid values
                'BusinessShortCode' => $BusinessShortCode,
                'Password' => $Password,
                'Timestamp' => $Timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $Amount,
                'PartyA' => $PartyA,
                'PartyB' => $BusinessShortCode,
                'PhoneNumber' => $PartyA,
                'CallBackURL' => $CallBackURL,
                'AccountReference' => $AccountReference,
                'TransactionDesc' => $TransactionDesc
            );

            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $curl_response = curl_exec($curl);
            //print_r($curl_response);

            //echo $curl_response;

        }
    }

    /*=============================================
	DELETE PRODUCT
	=============================================*/
    static public function ctrDeleteTransaction()
    {

        if (isset($_GET["reciept"])) {
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');

            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();

            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');

            $table = "payments";
            $data = $_GET["reciept"];

            $answer = PaymentModel::mdlDeleteTransaction($table, $data);

            if ($answer == "ok") {

                if ($_SESSION['userId'] != 404) {
                    // Create an array with the data for the activity log entry
                    $logdata = array(
                        'UserID' => $_SESSION['userId'],
                        'ActivityType' => 'Sale',
                        'ActivityDescription' => 'User ' . $_SESSION['username'] . ' deleted transaction ' . $data . '.',
                        'itemID' => $data,
                        'TimeStamp' => $dateTimeStr
                    );
                    // Call the ctrCreateActivityLog() function
                    activitylogController::ctrCreateActivityLog($logdata);
                }

                echo '<script>

				Swal.fire({
					  icon: "success",
					  title: "The transaction has been successfully deleted",
                      showConfirmButton: false,
                      timer: 2000 // 2 seconds
                      }).then((result) => {
                        // Code to execute after the alert is closed
                        window.location = "transactions";
                      });

				</script>';
            }
        }
    }

    /*=============================================
	FETCH GROUPED PAYMENTS
	=============================================*/

    public static function ctrfetchGroupedPayments($item, $value)
    {

        $table = "payments";

        $answer = PaymentModel::mdlfetchGroupedPayments($table, $item, $value);

        return $answer;
    }
}
