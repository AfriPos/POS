<?php
require_once 'models/connection.php';
?>
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Transactions</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item active">Transactions</li>
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
          <div class="col-lg-12">
          <!-- /.col-md-6 -->

            <div class="card card-primary card-outline">
              <div class="card-body" id="buttonContainer">
              <table id="example1" class="table-striped tables display" style="width:100%">
                  <thead>
           
                    <tr>
                      
                      <th>#</th>
                      <th>Transaction ID</th>
                      <th>Amount</th>
                      <th>Payment Method</th>
                      <th>Date</th>
                      <th>Actions</th>


                    </tr> 

                    </thead>
                    <tbody>
                        <?php
                        $pdo = connection::connect();
                        $storeid = $_SESSION['storeid'];
                        $select = $pdo->prepare('SELECT * FROM payments WHERE StoreID = :storeid');
                        $select->bindParam(':storeid', $storeid);
                        $select->execute();
                        $paymentResult = $select->fetchAll();
                        foreach ($paymentResult as $paymentKey => $paymentVal) {
                            if ($paymentVal['Amount'] > 0) {

                                echo '<tr>
                                        <td>' . ($paymentKey + 1) . '</td>
                                        <td>' . $paymentVal["PaymentID"] . '</td>
                                        <td>' . $paymentVal["Amount"] . '</td> 
                                        <td>' . $paymentVal["PaymentMethod"] . '</td>
                                        <td>' . $paymentVal["PaymentDate"] . '</td>
                                        <td><button receipt="' . $paymentVal['ReceiptNumber'] . '" class="btn btn-s download-reciept"><i class="fa-solid fa-file-pdf"></i></button>
                                        <button receipt="' . $paymentVal['ReceiptNumber'] . '" class="btn btn-s view-receipt"><i class="fa-solid fa-eye"></i></button>
                                        <button receipt="' . $paymentVal['PaymentID'] . '" class="btn btn-s delete-Transaction"><i class="fa-solid fa-trash"></i></button></td>
                                    </tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
              </div>
            </div>
    
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  


  <div id="viewPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    
    <div class="modal-content">
      <form role="form" method="POST">
        <div class="modal-header">
            <h4 class="modal-title">View payment</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body"> 
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-3">
                        <strong>Customer:</strong><br>
                        <strong>Invoice number:</strong><br>
                        <strong>Payment type:</strong>
                    </div>
                    <div class="col-sm-3 payment-col1">

                    </div>
                    <div class="col-sm-3">
                        <strong>Sum:</strong><br>
                        <strong>Reciept number:</strong><br>
                        <strong>Payment date:</strong>
                    </div>
                    <div class="col-sm-3 payment-col2">

                    </div>
                </div>

                <!-- Add some spacing -->
                <div class="my-4"></div>

                <div class="row">
                    <div class="col-8 table-responsive custom-table">
                        <h4>Related payments:</h4>
                        <table id="invoice2-table" class="w-100">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Number</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="invoice2-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <div class="dropup-center dropup">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a id="view-reciept-pdf-link" class="dropdown-item" href="#">View PDF</a></li>
                    <li><a id="download-reciept-pdf-link" class="dropdown-item" href="#">Download PDF</a></li>
                </ul>
            </div>
        </div>
    </div>
  </div>
</div>

  <?php

$deleteTransaction = new PaymentController();
$reciept = $deleteTransaction->ctrDeleteTransaction();


    // $deleteTransaction = new PaymentController();
    // $deleteTransaction -> ctrDeleteTransaction();

    // var_dump($deleteTransaction)

  ?>
 