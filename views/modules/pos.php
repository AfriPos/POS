<?php
    function fillProducts(){
  
      $item = "store_id";
      $value = $_SESSION['storeid'];
      $order = 'id';
      $products = productController::ctrShowProducts($item, $value, $order, true);
  
      $output = '';
      foreach ($products as $row) {
          $output .= '<option value="' . $row['barcode'] . '">' . $row['product'] . '</option>';
      }
  
      return $output;
    }
 ?>
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Point of Sale</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item active">Point of Sale</li>
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
                <form action="" method="post" enctype="multipart/form-data" id="posForm">
                    <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <?php
                                    $element = "others";
                                    $table = "customers";
                                    $countAll = null;
                                    $organisationcode = $_SESSION['organizationcode'];
                                    $package = packagevalidateController::ctrPackageValidate($element, $table, $countAll, $organisationcode);
                                    if ($package) {
                                        echo'
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                                            </div>
                                            <input type="text" name="txtbarcode" id="scanbarcode" class="form-control" placeholder="Scan Barcode">
                                        </div>';
                                    }
                                ?>
                                <div class="form-group">
                                <label>Product name</label>
                                    <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;" id="pos-select">
                                        <option value="">-- Select or search --</option><?php echo fillProducts();?>
                                    </select>
                                </div>
                                <div class="tableFixHead">
                                    <table id="producttable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Stock</th>
                                                <th>Price</th>
                                                <th>Discount</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <th>Del</th>
                                            </tr>
                                        </thead>
                                        <tbody class="details" id="itemtable">
                                            <input type="hidden" name="productsList" id="productsList">
                                            <!-- <textarea name="productsList" id="productsList" cols="60" rows="10"></textarea> -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Sub Total</span>
                                    </div>
                                    <input type="text" class="form-control" name="subtotal" id="txtsubtotal_id" readonly>
                                    <input type="hidden" class="form-control" id="taxablesubtotal_id" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Kshs</span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">VAT</span>
                                    </div>
                                    <input type="text" class="form-control" id="txttaxtotal_id" name="totaltax" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Kshs</span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Total Discount</span>
                                    </div>
                                    <input type="text" class="form-control" id="txtdiscounttotal_id" name="totaldiscount" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Kshs</span>
                                    </div>
                                </div>
                                <hr style="height:2px; border-width:0; color:black; background-color:black;">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Total</span>
                                    </div>
                                    <input type="text" class="form-control form-control-lg total" id="txttotal_id" name="total" readonly>
                                    <input type="hidden" class="form-control" id="taxabletotal_id" readonly>
                                    <input type="hidden" class="form-control" id="nontaxabletotal_id" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Kshs</span>
                                    </div>
                                </div>
                                <hr style="height:2px; border-width:0; color:black; background-color:black;">
                                <div class="form-group clearfix" name="paymentmethod">
                                    <div class="icheck-primary form-check form-check-inline" >
                                        <input class="form-check-input" type="radio" name="r3" id="radioSuccess1" value="Cash">
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
                                                    <input class="form-check-input" type="radio" name="r3" id="radioSuccess3" value="M-pesa">
                                                    <label class="form-check-label" for="radioSuccess3">M-pesa</label>
                                                </div>';
                                        }     
                                    ?>
                                    <div class="icheck-info form-check form-check-inline points" style="display: none;">
                                        <input class="form-check-input" type="radio" name="r3" id="radioSuccess4" value="points">
                                        <label class="form-check-label" for="radioSuccess4">Points</label>
                                    </div>
                                </div>
                                <hr style="height:2px; border-width:0; color:black; background-color:black;">
                                <div class="save-order">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Due/Bal</span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg total" id="txtdue_id" name="dueamount" readonly >
                                        <div class="input-group-append">
                                            <span class="input-group-text">Kshs</span>
                                        </div>
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Paid</span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg total" id="txtpaid_id" name="txtpaid" readonly>
                                        <input type="hidden" class="form-control form-control-lg redeemedpoints" id="redeemedpoints" name="redeemedpoints">
                                        <input type="hidden" class="form-control form-control-lg pointamountvalue" id="pointamountvalue" name="pointamountvalue">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Kshs</span>
                                        </div>
                                    </div>

                                    <?php
                                        // Fetch loyalty settings
                                        $stmt = connection::connect()->prepare("SELECT * FROM loyaltysettings");

                                        $stmt->execute();
                                        
                                        $setting = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        $CustomerDetailsValue = $setting[4]['SettingValue'];
                                        $LoyaltypointsValue = $setting[3]['SettingValue'];
                                        if ($CustomerDetailsValue == 1){
                                            echo '
                                            <div class="form-group">
                                            <hr style="height:2px; border-width:0; color:black; background-color:black;">
                                            <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;" name="selectcustomer" id="selectcustomer">
                                                <option selected disabled  value="">--Select or Search Customer--</option>';
    
                                                    $item = null;
                                                    $value =null;
    
                                                    $customers = customerController::ctrShowCustomers($item, $value);
    
                                                    foreach ($customers as $key => $value) {
                                                        
                                                        echo '<option value="'.$value["customer_id"].'">'.$value["name"].'</option>';
    
                                                    }
    
                                                    echo '
                                            </select>
                                        </div>
                                            ';
                                        }
                                    ?>
                                </div>
                                <div class="points-plat" style="display: none;">
                                    <div class="form-group">
                                        <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;" name="pselectcustomer" id="pselectcustomer">
                                            <option selected disabled  value="">--Select or Search Customer--</option>

                                                <?php
                                                $item = null;
                                                $value =null;

                                                $customers = customerController::ctrShowCustomers($item, $value);

                                                foreach ($customers as $key => $value) {
                                                    
                                                    echo '<option value="'.$value["customer_id"].'">'.$value["name"].'</option>';

                                                }

                                                ?>
                                        </select>
                                    </div>
                                    <div class="$('#waitingMessage').text $('#waitingMessage').text-danger" id="lesspoints" role="$('#waitingMessage').text" style="display: none;">Points not enough to make purchase!<br>Select another payment method to topup.</div>
                                    <div class="$('#waitingMessage').text $('#waitingMessage').text-danger" id="nophone" role="$('#waitingMessage').text" style="display: none;">The customer is not in the loyalty program or does not exist.</div>
                                    <div class="$('#waitingMessage').text $('#waitingMessage').text-success" id="eligible" role="$('#waitingMessage').text" style="display: none;">The customer is eligible.</div>
                                    <div class="payment-methods" style="display: none;">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control top-up" id="floatingtop-upInput" name="topUpamount" placeholder="Topup amount">
                                            <label for="floatingtop-upInput">Topup amount</label>
                                        </div>
                                        <!-- <button type="button" class="btn btn-primary topupCash">Cash</button> -->
                                        
                                    <!-- <div class="icheck-primary form-check form-check-inline" >
                                        <input class="form-check-input topupCash" type="radio" name="r3" id="radioSuccess4" value="topupCash">
                                        <label class="form-check-label" for="radioSuccess4">Cash</label>
                                    </div> -->
                                    
                                    <div class="icheck-primary form-check form-check-inline">
                                        <input class="form-check-input topupCash" type="radio" id="topupCash" name="topup" value="topupCash">
                                        <label class="form-check-label" for="topupCash">Cash</label>
                                    </div>
                                    <div class="icheck-success form-check form-check-inline">
                                        <input class="form-check-input topupMpesa" type="radio" id="topupMpesa" name="topup" value="topupM-pesa">
                                        <label class="form-check-label" for="topupMpesa">M-pesa</label>
                                    </div>
                                        <!-- <button type="button" class="btn btn-success topupMpesa">Mpesa</button> -->
                                    </div>
                                </div>
                                <!-- End of additional inputs -->

                                <hr style="height:2px; border-width:0; color:black; background-color:black;">
                                <div class="card-footer">
                                    <div class="text-center">
                                        <button name="saveorder" id="submitButton" class="btn btn-primary">Save Order</button>
                                    </div>
                                </div>
                                <?php
                                    $add= new PaymentController();
                                    $add->addPayment();
                                ?>
                            </div>
                        </div>
                    </div>
                    </div>
                </form>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 

<!-- New M-Pesa Payment Modal -->
<div class="modal fade" id="mpesaModal" tabindex="-1" role="dialog" aria-labelledby="mpesaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mpesaModalLabel">M-Pesa Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="mpesaQRCodeContainer" class="text-center">
                    <!-- QR Code will be dynamically inserted here -->
                </div>
                <div id="waitingMessage" class="text-center">
                    Waiting for transaction...
                </div>
                <div class="form-group mt-3">
                    <label for="mpesaTransactionId">Transaction ID:</label>
                    <input type="text" class="form-control" id="mpesaTransactionId" placeholder="Enter Transaction ID">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="verifyMpesa">Verify Transaction</button>
            </div>
        </div>
    </div>
</div>

<?php
// Get the current timestamp in the specified format
$timestamp = date("Y-m-d H:i:s");
// Echo the timestamp to be used in JavaScript
echo "<script>const timestamp = '$timestamp';</script>";
?>


<script>
$(document).ready(function() {
    // Function to show the M-Pesa modal
    function showMpesaModal() {
                    
        $('#mpesaModal').modal('show');

        // Simulate an AJAX request to get the QR code data
        $.ajax({
            url: './stkpush/qr.php', // Replace with the actual URL of your server-side script
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Display the generated QR code
                    $('#mpesaQRCodeContainer').html('<img src="' + response.qrImage + '" alt="M-Pesa QR Code" class="img-fluid">');
                    // Display the waiting message
                    $('#waitingMessage').text('Waiting for transaction...');

                    // Start checking for payment status periodically
                    const intervalId = setInterval(function() {
                        checkPayment(intervalId, timestamp);
                    }, 1000); // Check every 5 seconds (adjust as needed)
                    
                } else {
                    $('#waitingMessage').text('An error occurred while generating the QR code. Please try again later.');
                }
            },
            error: function() {
                $('#waitingMessage').text('An error occurred while generating the QR code. Please try again later.');
            }
        });
    }
    function checkPayment(intervalId, timestamp) {
        // Get the amount and timestamp from your form or wherever they are stored
        var amount = $('#txttotal_id').val(); 
        
        var data = new FormData();
        data.append("amount", amount);
        data.append("timestamp", timestamp);

        // Perform a POST request to check the payment status
        $.ajax({
            url: 'ajax/payment.ajax.php', // Replace with the actual URL of your server-side script
            method: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(response) {
                console.log(response);
                if (response != false) {
                    // Payment successful, update UI and stop checking
                    clearInterval(intervalId);
                    $('#waitingMessage').text('Payment successful!');
                }
            },
            error: function() {
                // An error occurred, update UI and stop checking
                clearInterval();
                $('#waitingMessage').text('An error occurred while checking payment status. Please try again later.');
            }
        });
    }


    // Event listener for the M-Pesa radio button
    $('#radioSuccess3').change(function() {
        if ($(this).prop('checked')) {
            // M-Pesa is selected, show the modal
            showMpesaModal();

        }
    });

    // Event listener for the Verify Transaction button
    $('#verifyMpesa').click(function() {
        // Your logic to verify the M-Pesa transaction using the entered transaction ID
        var transactionId = $('#mpesaTransactionId').val();
        // Add your verification logic here...

        // For now, just close the modal after verification (replace this with your actual logic)
        $('#mpesaModal').modal('hide');
    });
});
</script>
