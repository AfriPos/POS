<?php
require_once 'models/connection.php';
?>

<style>
    /* Custom CSS */
    .custom-table {
        border-radius: 10px; /* Curved edges */
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1); /* Box shadow */
        background-color: white; /* White background */
        color: #2C394B; /* Text color */
        font-family: 'Segoe UI', sans-serif; /* Modern font */
    }

    .custom-table th {
        background-color: #F2F4F6; /* Header background */
    }

    .custom-table tbody tr:nth-child(even) {
        background-color: #F5F7F9; /* Alternating row background */
    }

</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Invoices</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Invoices</li>
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
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <button type="button" class="btn btn-default float-right dates" id="daterange-btn">
                                <span>
                                    <i class="far fa-calendar-alt"></i> Date range
                                </span>
                                <i class="fas fa-caret-down"></i>
                            </button>
                            <h5 class="m-0">Invoices List</h5>
                        </div>
                        <div class="card-body" id="buttonContainer">
                            <div class="row">
                                <div class="col-12">
                                    <!-- <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by customer name or contact"> -->

                                    <!-- <div class="table-responsive"> -->
                                        <table id="example1" class="table-striped tables display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Customer Name</th>
                                                    <th>Phone Number</th>
                                                    <th>Date</th>
                                                    <th>DueDate</th>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                    <th>Discount</th>
                                                    <th>Due Amount</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="invoiceTableBody">
                                                <?php
                                                if (isset($_GET['initialDate'])) {
                                                    $initialDate = $_GET['initialDate'];
                                                    $finalDate = $_GET['finalDate'];
                                                } else {
                                                    $initialDate = null;
                                                    $finalDate = null;
                                                }
                                                $pdo = connection::connect();

                                                $invoices = PaymentController::ctrSalesDatesRange($initialDate, $finalDate);
                                                // var_dump($invoices);

                                                foreach ($invoices as $key => $value) {

                                                    echo '
                                                    <tr data-widget="expandable-table" aria-expanded="false">
                                                        <td>' . ($key + 1) . '</td>';
                                                    
                                                    $item = "customer_id";
                                                    $value1 = $value["CustomerID"];
                                                    $customer = customerController::ctrShowCustomers($item, $value1);
                                                    // var_dump($customer);


                                                    echo '
                                                        <td>' . $customer["name"] . '</td>
                                                        <td>' . $customer["phone"] . '</td>
                                                        <td>' . $value["DateCreated"] . '</td>
                                                        <td>' . $value["DueDate"] . '</td>';

                                                    if ($value["DueAmount"] == 0) {
                                                        echo '<td><button class="btn btn-success btn-sm">Paid</button></td>';
                                                    } elseif ($value["TotalAmount"] == abs($value['DueAmount'])) {
                                                        echo '<td><button class="btn btn-danger btn-sm">Unpaid</button></td>';
                                                    } else {
                                                        echo '<td><button class="btn btn-warning btn-sm">Partially Paid</button></td>';
                                                    }

                                                    echo '<td>' . $value["TotalAmount"] . '</td>
                                                        <td>' . $value["Discount"] . '</td>
                                                        <td>' . $value["DueAmount"] . '</td>';

                                                    if ($value["DueAmount"] != 0) {
                                                        echo '<td><button idInvoice="' . $value['InvoiceID'] . '" class="btn btn-s viewInvoice" data-toggle="modal" data-target="#viewInvoiceModal"><i class="fa-solid fa-eye"></i></button>
                                                                <button idInvoice="' . $value['InvoiceID'] . '" class="btn btn-s addPayment" data-toggle="modal" data-target="#makePaymentModal"><i class="fa-solid fa-check"></i></button></td>';
                                                    } else {
                                                        echo '<td><button idInvoice="' . $value['InvoiceID'] . '" class="btn btn-s viewInvoice" data-toggle="modal" data-target="#viewInvoiceModal"><i class="fa-solid fa-eye"></i></button></td>';
                                                    }

                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    <!-- </div> -->
                                </div>
                            </div>
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

<!-- View Invoice Modal -->
<div id="viewInvoiceModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <form role="form" method="POST">
        <div class="modal-header">
            <h4 class="modal-title">View Invoice</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
                <div class="modalBodyContent">
                    <div class="row">
                        <div class="col-sm-3">
                            <strong>Invoice to:</strong><br>
                            <strong>Number:</strong>
                        </div>
                        <div class="col-sm-3 invoice-name-number">

                        </div>
                        <div class="col-sm-3">
                            <strong>Document Date:</strong><br>
                            <strong>Due Date:</strong>
                        </div>
                        <div class="col-sm-3 invoice-dates">
                        </div>
                    </div>
                    <!-- Add some spacing -->
                    <div class="my-4"></div>
                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive custom-table">
                            <table id="invoice-table" class="w-100">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Barcode</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-table-body">
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Add some spacing -->
                    <div class="my-4"></div>

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-6">
                        </div>
                        <!-- /.col -->
                        <div class="col-6">
                            <div class="row">
                                <div class="col-sm-6 text-end">
                                    Total without VAT:
                                </div>
                                <div class="col-sm-6 text-end subtotal">
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 text-end">
                                    VAT:
                                </div>
                                <div class="col-sm-6 text-end vat">
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 text-end">
                                    Discount:
                                </div>
                                <div class="col-sm-6 text-end discount">
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 text-end">
                                    <strong class="fs-5">Total:</strong> <!-- Add fs-5 class for bigger text -->
                                </div>
                                <div class="col-sm-6 text-end">
                                    <strong class="fs-5 total"></strong> <!-- Add fs-5 class for bigger text -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 text-end">
                                    Due:
                                </div>
                                <div class="col-sm-6 text-end due">
                                    
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    
                    <!-- Add some spacing -->
                    <div class="my-4"></div>

                    <div class="row">
                        <div class="col-8 table-responsive custom-table">
                            <h4>Related payments:</h4>
                            <table id="payment-table" class="w-100">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Number</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="payment-table-body">
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
                        <li><a id="view-pdf-link" class="dropdown-item" href="#">View PDF</a></li>
                        <li><a id="download-pdf-link" class="dropdown-item" href="#">Download PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- View Invoice Modal -->
<div id="makePaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="paymentForm" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title">View Invoice</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label >InvoiceID</label>
                        <input type="text" class="form-control" name="invoiceId" id="invoiceId" readonly>
                    </div>   

                    <div class="form-group">
                        <label >Total</label>
                        <input type="text" class="form-control" name="total" id="total" readonly>
                    </div>  
                    
                    <div class="form-group">
                        <label >Due</label>
                        <input type="number" class="form-control" name="due" id="due" readonly>
                    </div>  
                    
                    <div class="form-group">
                        <label >Paid</label>
                        <input type="text" class="form-control" name="paid" id="paid" readonly>
                    </div>  

                    <div class="form-group clearfix" name="paymentmethod">
                        <div class="icheck-primary form-check form-check-inline">
                            <input class="form-check-input paymentmethods" type="radio" name="r3" id="radioSuccess1" value="Cash">
                            <label class="form-check-label" for="radioSuccess1">Cash</label>
                        </div>
                        <?php
                            $element = "others";
                            $table = "customers";
                            $countAll = null;
                            $organisationcode = $_SESSION['organizationcode'];
                            $package = packagevalidateController::ctrPackageValidate($element, $table, $countAll, $organisationcode);
                            if ($package) {
                                echo'<div class="icheck-success form-check form-check-inline">
                                        <input class="form-check-input paymentmethods" type="radio" name="r3" id="radioSuccess3" value="M-pesa">
                                        <label class="form-check-label" for="radioSuccess3">M-pesa</label>
                                    </div>';
                            }     
                        ?>
                    </div>
                    
                    <div class="form-group">
                        <label >Payment</label>
                        <input type="number" class="form-control" min="1" name="payment" id="payment">
                    </div>  
                </div>
                <div class="modal-footer justify-content-between"  id="modalFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="makePayment">Make Payment</button>
                </div>
                <?php
                    $add= new PaymentController();
                    $add->makePayment();
                ?>
            </form>
        </div>
    </div>
</div>
