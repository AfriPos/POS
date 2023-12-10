<?php

    $pdo = connection::connect();
    $storeid = $_SESSION['storeid'];

    // Get the current month and year
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Prepare the SQL statement
    $payments = $pdo->prepare("SELECT COUNT(*) AS paymentCount, SUM(amount) AS totalAmount FROM payments WHERE MONTH(PaymentDate) = :month AND YEAR(PaymentDate) = :year " . ($storeid ? "AND StoreID = :store_id" : "") . "");
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
    $invoices = $pdo->prepare("SELECT COUNT(*) AS invoicePaymentCount, SUM(TotalAmount) AS totalInvoiceAmount FROM invoices WHERE MONTH(datecreated) = :month AND YEAR(datecreated) = :year " . ($storeid ? "AND StoreID = :store_id" : "") . "");
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
    $paidinvoices = $pdo->prepare("SELECT COUNT(*) AS invoicePaidCount, SUM(TotalAmount) AS totalPaidInvoiceAmount FROM invoices WHERE MONTH(datecreated) = :month AND YEAR(datecreated) = :year AND dueamount = 0 " . ($storeid ? "AND StoreID = :store_id" : "") . "");
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

    $table = 'customers';
    $item = 'organizationcode';
    $value = $_SESSION['organizationcode'];
    
    $package = packagevalidateController::ctrshowpackage($table, $value);

     // Prepare the SQL statement to get monthly revenue (total payments - expenses - orders) for the current year
     $monthlyRevenue = $pdo->prepare("
     SELECT
         m.month,
         COALESCE(SUM(p.amount - COALESCE(e.amount, 0) - COALESCE(o.total, 0)), 0) AS revenue
     FROM (
         SELECT 1 AS month UNION SELECT 2 AS month UNION SELECT 3 AS month UNION SELECT 4 AS month UNION
         SELECT 5 AS month UNION SELECT 6 AS month UNION SELECT 7 AS month UNION SELECT 8 AS month UNION
         SELECT 9 AS month UNION SELECT 10 AS month UNION SELECT 11 AS month UNION SELECT 12 AS month
     ) m
     LEFT JOIN payments p ON MONTH(p.PaymentDate) = m.month AND YEAR(p.PaymentDate) = :year " . ($storeid ? "AND p.StoreID = :store_id" : "") . "
     LEFT JOIN expenses e ON m.month = MONTH(e.date) AND YEAR(p.PaymentDate) = YEAR(e.date)
     LEFT JOIN orders o ON m.month = MONTH(o.date) AND YEAR(p.PaymentDate) = YEAR(o.date)
     GROUP BY m.month
 ");
 $monthlyRevenue->bindParam(':year', $currentYear);
 if ($storeid) {
     $monthlyRevenue->bindParam(':store_id', $storeid, PDO::PARAM_STR);
 }
 $monthlyRevenue->execute();

 // Fetch the results
 $monthlyRevenueData = $monthlyRevenue->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.card-custom {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 5px;
    transition: transform 0.3s ease-in-out;
}

.card-custom:hover {
    transform: translateY(-5px);
}

.card-custom .card-body {
    padding: 1.5rem;
}

.card-custom .card-title {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.card-custom .card-text {
    font-size: 0.9rem;
    color: #777;
}

.card-custom i.fa-solid {
    color: #007bff;
}

.card-custom p.text-right {
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
}
.custom-heading {
    color: #333;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0;
    text-decoration: none;
}
.btn-primary {
    transition: background-color 0.3s ease-in-out;
}

.btn-primary:hover {
    background-color: #fff;
    color: #007bff;
}

</style>

<div class="content-wrapper">
<!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Finance Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Finance Dashboard </li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

 <!-- Main content -->
 <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                            <i class="fa-solid fa-money-bill-wave fa-2xl mr-3"></i>
                                <h5 class="card-title mb-0 custom-heading">Payments</h5>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <p class="card-text" id="totalAmount"></p>
                                </div>
                                <div class="col-6">
                                    <p class="card-text text-right" id="paymentCount"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-file-invoice fa-2xl mr-3"></i>
                                <h5 class="card-title mb-0 custom-heading">Invoices</h5>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <p class="card-text" id="totalInvoiceAmount"></p>
                                </div>
                                <div class="col-6">
                                    <p class="card-text text-right" id="invoicePaymentCount"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                            <i class="fa-solid fa-receipt fa-2xl mr-3"></i>
                                <h5 class="card-title mb-0 custom-heading">Paid Invoices</h5>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <p class="card-text" id="totalPaidInvoiceAmount"></p>
                                </div>
                                <div class="col-6">
                                    <p class="card-text text-right" id="invoicePaidCount"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->


    <?php 
       // var_dump($package);
       if ($package == 'Standard') {
           ?>
           <div class="content">
             <div class="container-fluid">
               <div class="row">
                 <div class="col-lg-12">
       
                   <div class="card card-primary card-outline">
                     <div class="card-header">
                       <h5 class="m-0">Monthly Revenue</h5>
                     </div>
                     <div class="card-body">
                       <table id="example1" class="table-striped tables display" style="width:100%">
                         <thead>
                           <tr>
                             <th>#</th>
                             <th>Month</th>
                             <th>Revenue</th>
                           </tr>
                         </thead>
                         <tbody>';
       <?php
           foreach ($monthlyRevenueData as $index => $row) {
               $month = date('F', mktime(0, 0, 0, $row['month'], 1));
               echo "<tr>";
               echo "<td>" . ($index + 1) . "</td>";
               echo "<td>$month</td>";
               echo "<td>Kshs " . number_format($row['revenue'], 2) . "</td>";
               echo "</tr>";
           }
       ?>
                         </tbody>
                       </table>
                     </div>
                   </div>
           
                 </div>
               </div>
             </div>
           </div>';
     <?php
        }else{
        ?>
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                    <?php
                        include 'finance-graphs/invoices-period.php';
                    ?>
                    </div>
                    <div class="col-lg-6">
                    <?php
                        include 'finance-graphs/payments-period.php';
                    ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                    <?php
                        include 'finance-graphs/revenue.php';
                    ?>
                    </div>
                    <div class="col-lg-6">
                    <?php
                        include 'finance-graphs/daily-monthly.php';
                    ?>
                    </div>
                </div>
            </div>
        </div>
<?php
}
?>

    </div>
    <!-- /.content-wrapper -->



<?php

?>

