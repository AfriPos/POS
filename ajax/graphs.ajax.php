<?php
require_once "../models/connection.php";
require_once "../controllers/product.controller.php";
require_once "../models/product.model.php";
require_once "../controllers/payments.controller.php";
require_once "../models/payment.model.php";
require_once "../controllers/store.controller.php";
require_once "../models/store.model.php";
require_once "../controllers/user.controller.php";
require_once "../models/user.models.php";

require_once "../controllers/reports.controller.php";
require_once "../models/reports.model.php";

session_start();
$pdo=connection::connect();


if ($_SESSION['storeid'] != null) {
  $item = "store_id";
  $value = $_SESSION['storeid'];
  $order='id';
  $products = productController::ctrShowProducts($item, $value, $order, true);
  $totalProducts = count($products);
  $storeid = $_SESSION['storeid'];
  $merch=$pdo->prepare( 'SELECT SUM(stock * purchaseprice) AS total_value FROM products WHERE store_id = :store_id');
  $merch -> bindParam(":store_id", $_SESSION['storeid'], PDO::PARAM_STR);
  $merch->execute();
  $result=$merch->fetch();
} else{
  $storeid = null;
  $merch=$pdo->prepare( 'SELECT SUM(stock * purchaseprice) AS total_value FROM products');
  $merch->execute();
  $result=$merch->fetch();
}
$table = "sales";
$parameters = array();
$parameters['reportclass'] = "monthlygsi";
$sales = reportsModel::mdlShowDetailedreport($table, $storeid, $parameters);
$totalSales = 0;
$totalproducts = 0;

foreach ($sales as $sale) {
    $totalSales += floatval($sale['TotalSales']);
    $totalproducts += floatval($sale['MonthlySoldUnits']);
}

$item1 = null;
$value1 = null;
$users=userController::ctrShowUsers($item1,$value1);
$stores=storeController::ctrShowStores($item1,$value1);
$totalstores=count($stores);
$totalusers = 0;
foreach ($users as $row) {
    if ($row['role'] !== 'Administrator') {
        $totalusers++;
    }
}



$storeid = $_SESSION['storeid'];
$currentYear = date('Y');

$paymentsQuery = $pdo->prepare("SELECT  MONTH(Date) AS month, SUM(`TotalAmount`) AS total_amount FROM sales WHERE YEAR(Date) = YEAR(CURDATE()) " . ($storeid ? "AND storeid = :store_id" : "") . " GROUP BY  MONTH(Date) ORDER BY month");

$expensesQuery = $pdo->prepare(" SELECT COALESCE(SUM(amount), 0) AS total_expenses, MONTH(date) AS month FROM expenses WHERE YEAR(date) = :year " . ($storeid ? "AND store_id = :store_id" : "") . " GROUP BY MONTH(date)");

$ordersQuery = $pdo->prepare(" SELECT COALESCE(SUM(total), 0) AS total_expenses, MONTH(date) AS month FROM orders WHERE YEAR(date) = :year " . ($storeid ? "AND store_id = :store_id" : "") . " GROUP BY MONTH(date)");

// $paymentsQuery->bindParam(':year', $currentYear, PDO::PARAM_STR);
if ($storeid) {
    $paymentsQuery->bindParam(':store_id', $storeid, PDO::PARAM_STR);
}
$paymentsQuery->execute();
$paymentData = $paymentsQuery->fetchAll(PDO::FETCH_ASSOC);

$expensesQuery->bindParam(':year', $currentYear, PDO::PARAM_STR);
if ($storeid) {
    $expensesQuery->bindParam(':store_id', $storeid, PDO::PARAM_STR);
}
$expensesQuery->execute();
$expenseData = $expensesQuery->fetchAll(PDO::FETCH_ASSOC);

$ordersQuery->bindParam(':year', $currentYear, PDO::PARAM_STR);
if ($storeid) {
    $ordersQuery->bindParam(':store_id', $storeid, PDO::PARAM_STR);
}
$ordersQuery->execute();
$orderData = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);


$months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

$monthlyPaymentData = [];
$monthlyExpenseData = [];

foreach ($months as $month) {
  $monthData = [
    'month' => $month,
    'total_amount' => 0
  ];

  foreach ($paymentData as $data) {
    if (intval($data['month']) === array_search($month, $months) + 1) {
      $monthData['total_amount'] = $data['total_amount'];
      break;
    }
  }

  $monthlyPaymentData[] = $monthData;
}

foreach ($months as $month) {
  $monthData = [
    'month' => $month,
    'total_expenses' => 0
  ];

  foreach ($expenseData as $data) {
    if (intval($data['month']) === array_search($month, $months) + 1) {
      $monthData['total_expenses'] += $data['total_expenses']; // Accumulate expenses
      break;
    }
  }

  foreach ($orderData as $data) {
    if (intval($data['month']) === array_search($month, $months) + 1) {
      $monthData['total_expenses'] += $data['total_expenses']; // Accumulate orders
      break;
    }
  }

  $monthlyExpenseData[] = $monthData;
}





$currentYear = date('Y');
$storesQuery = $pdo->prepare("SELECT s.store_id, s.store_name, s.logo, SUM(sale.TotalAmount) AS total_sales FROM store s JOIN sales sale ON s.store_id = sale.storeid GROUP BY s.store_id, s.store_name ORDER BY total_sales DESC; ");
$storesQuery->execute();
$storesData = $storesQuery->fetchAll();
$storeData = array();
foreach ($storesData as $store) {
  $storeData[] = [
      'store_id' => $store['store_id'],
      'store_name' => $store['store_name'],
      'logo' => $store['logo'],
      'total_revenue' => $store['total_sales'],
  ];
}



// Get the current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// Prepare the SQL statement
$payments = $pdo->prepare("SELECT COUNT(*) AS paymentCount, SUM(Amount) AS totalAmount FROM payments WHERE MONTH(PaymentDate) = :month AND YEAR(PaymentDate) = :year " . ($storeid ? "AND StoreID = :store_id" : "") . "");
$payments->bindParam(':month', $currentMonth);
$payments->bindParam(':year', $currentYear);
if ($storeid) {
    $payments->bindParam(':store_id', $storeid, PDO::PARAM_STR);
}
$payments->execute();

// Fetch the result
$paymentResult = $payments->fetch();

// Retrieve the count and total amount
$paymentCount = $paymentResult['paymentCount'];
$totalAmount = $paymentResult['totalAmount'];

// Prepare the SQL statement
$invoices = $pdo->prepare("SELECT COUNT(*) AS invoicePaymentCount, SUM(TotalAmount) AS totalInvoiceAmount FROM invoices WHERE MONTH(DateCreated) = :month AND YEAR(DateCreated) = :year " . ($storeid ? "AND StoreID = :store_id" : "") . "");
$invoices->bindParam(':month', $currentMonth);
$invoices->bindParam(':year', $currentYear);
if ($storeid) {
    $invoices->bindParam(':store_id', $storeid, PDO::PARAM_STR);
}
$invoices->execute();

// Fetch the result
$paymentInvoiceResult = $invoices->fetch();

// Retrieve the count and total amount
$invoicePaymentCount = $paymentInvoiceResult['invoicePaymentCount'];
$totalInvoiceAmount = $paymentInvoiceResult['totalInvoiceAmount'];

// Prepare the SQL statement
$paidinvoices = $pdo->prepare("SELECT COUNT(*) AS invoicePaidCount, SUM(TotalAmount) AS totalPaidInvoiceAmount FROM invoices WHERE MONTH(DateCreated) = :month AND YEAR(DateCreated) = :year AND DueAmount = 0 " . ($storeid ? "AND StoreID = :store_id" : "") . "");
$paidinvoices->bindParam(':month', $currentMonth);
$paidinvoices->bindParam(':year', $currentYear);
if ($storeid) {
    $paidinvoices->bindParam(':store_id', $storeid, PDO::PARAM_STR);
}
$paidinvoices->execute();

// Fetch the result
$paymentInvoiceResult = $paidinvoices->fetch();

// Retrieve the count and total amount
$invoicePaidCount = $paymentInvoiceResult['invoicePaidCount'];
$totalPaidInvoiceAmount = $paymentInvoiceResult['totalPaidInvoiceAmount'];



// Return the data as JSON
$data = [
    'totalAmount' => $totalAmount,
    'paymentCount' => $paymentCount,
    'totalInvoiceAmount' => $totalInvoiceAmount,
    'invoicePaymentCount' => $invoicePaymentCount,
    'totalPaidInvoiceAmount' => $totalPaidInvoiceAmount,
    'invoicePaidCount' => $invoicePaidCount,

    'storeData' => $storeData,

    'monthlyPayment' => $monthlyPaymentData,
    'monthlyExpense' => $monthlyExpenseData,

    'totalProducts' => isset($totalProducts) ? $totalProducts : 0,
    'totalMerchandise' => $result['total_value'],
    'totalSales' => $totalSales,
    'totalUsers' => $totalusers,
    'totalStores' => $totalstores,
    'totalQuantity' => $totalproducts,
    // 'session' => $_SESSION['storeid']
];
echo json_encode($data);
?>
