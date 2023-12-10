   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Barcode Generator</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item"><a href="products">Product List</a></li>
              <li class="breadcrumb-item active">Barcode Generator</li>
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
              <div class="card-header">
                <h5 class="m-0">Generate Barcode Stickers<a href="products"><i class="fa fa-times float-right"></i></a></h5>
                
              </div>
              <div class="card-body">
                
                <?php
                  $item = "id"; 
                  $value = $_GET["product-id"];
                  $order='barcode';

                  $product = productController::ctrShowProducts($item, $value, $order);
                  // var_dump($product);
                  echo'
                    <div class="row">
                      <div class="col-lg-6">
                        <ul class="list-group">
                          <center><p class="list-group-item list-group-item-info"><b>PRINT BARCODE</b></p></center>
                        </ul>
                          <form class="form-horizontal" method="post" action="barcode/barcode.php" target="_blank">
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="product">Product:</label>
                              <div class="col-sm-10">
                                  <input autocomplete="OFF" type="text" class="form-control" id="product" name="product" value='.$product["product"].' readonly>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="product_id">Product ID:</label>
                              <div class="col-sm-10">
                                  <input autocomplete="OFF" type="text" class="form-control" id="product_id" name="product_id" value='.$product["barcode"].' readonly>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-sm-2" for="rate">Sale Price</label>
                              <div class="col-sm-10">          
                                  <input autocomplete="OFF" type="text" class="form-control" id="rate"  name="rate" value='.$product["saleprice"].' readonly>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-sm-3" for="print_qty">Barcode Quantity</label>
                              <div class="col-sm-10">          
                                  <input autocomplete="OFF" type="print_qty" class="form-control" id="print_qty"  name="print_qty">
                              </div>
                            </div>

                            <div class="form-group">        
                              <div class="col-sm-offset-2 col-sm-10">
                                  <button type="submit" class="btn btn-primary">Generate Barcode</button>
                              </div>
                            </div>
                        </form>
                      </div>
                      <div class="col-md-6">
                          <ul class="list-group">
                              <center><p class="list-group-item list-group-item-info"><b>PRODUCT IMAGE</b></p></center>
                              <img src="'.$product["image"].'" class="img-responsive">
                          </ul>
                      </div>
                    </div>';
                  ?>
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
 