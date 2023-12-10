   
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Payment</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payment</li>
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
              <div class="card-header card-primary">
                <h5 class="m-0">Featured</h5>
              </div>
              <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                      <form action="" method="post" id="paymentForm">
                        <div class="form-group">
                            <label >Customer Name</label>
                            <input type="text" class="form-control" name="cname" id="cname" readonly>
                        </div>  
                        
                        <div class="form-group">
                            <label >Products</label>
                            <input type="text" class="form-control" name="products" id="products" readonly>
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
                            <!-- <div class="icheck-primary d-inline">
                                <input type="radio" name="r3" id="radioSuccess1" value="Cash">
                                <label for="radioSuccess1">
                                    Cash
                                </label>
                            </div> -->
                            <div class="icheck-success form-check form-check-inline">
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
                        </div>
                        
                        <div class="form-group">
                            <label >Payment</label>
                            <input type="number" class="form-control" min="1" name="payment" id="payment">
                            <input type="hidden" class="form-control" name="invoiceId" id="invoiceId">
                        </div>  
                        <div class="card-footer">
                          <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="makePayment">Make Payment</button>
                          </div>
                        </div>
                        <?php
                            $add= new PaymentController();
                            $add->makePayment();
                        ?>
                      </form>
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
 