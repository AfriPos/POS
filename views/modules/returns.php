<?php
  $item = "store_id";
  $value = $_SESSION['storeid'];
  $order = 'id';
  $products = productController::ctrShowProducts($item, $value, $order, true);

  $selectOptions = "";

  foreach ($products as $product) {
      $selectOptions .= "<option value='" . $product['barcode'] . "'>" . $product['product'] . "</option>";
  }
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Return Products</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Return Products</li>
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
                          <h5 class="m-0">Initiate a Return</h5>
                        </div>
                        <div class="card-body">
                          <form action="" method="post">
        
                            
                            <!-- Product information -->
                            <div class="form-group">
                              <label for="product">Product:</label>
                              <div class="select2-purple">
                                <select class="select2" name="selectProduct" id="selectReturnProduct" data-placeholder="Select or search" data-dropdown-css-class="select2-purple" style="width: 100%;">
                                  <option value=""></option><?php echo $selectOptions; ?>
                                </select>
                              </div>
                            </div>

                            <!-- Quantity -->
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                            </div>

                            <!-- supplier -->
                            <div class="form-group">
                              <label for="product">Supplier:</label>
                              <div class="select2-purple">
                                <select class="select2" name="selectSupplier" id="selectSupplier" data-placeholder="Select or search" data-dropdown-css-class="select2-purple" style="width: 100%;">
                                  <option value=""></option> <?php
                                            $item = null;
                                            $value = null;

                                            $supplier = supplierController::ctrShowSuppliers($item, $value);
                                            foreach ($supplier as $key => $value) {
                                                echo '<option value="'.$value["supplierid"].'">'.$value["name"].'</option>';
                                            }
                                        ?>
                                </select>
                              </div>
                            </div>
                            <!-- Return Date -->
                            <div class="form-group">
                              <label for="dateField">Return Date:</label>
                              <input type="date" class="form-control" id="dateField" name="dateField">
                            </div>

                            <!-- Return reason -->
                            <div class="form-group">
                                <label for="reason">Reason for Return:</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            </div>

                            <!-- Return type -->
                            <div class="form-group">
                                <label for="return_type">Return Type:</label>
                                <select class="form-control" id="return_type" name="return_type" required>
                                    <option value="exchange">Exchange</option>
                                    <option value="refund">Refund</option>
                                    <option value="repair">Repair</option>
                                </select>
                            </div>

                            <button type="submit" name="btnschedule" class="btn btn-primary">Schedule</button>
                            <?php
                            $add= new ReturnProductController();
                            $add->ctrAddReturnProduct();

                            ?>
                          </form>
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
 
