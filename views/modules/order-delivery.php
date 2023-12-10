
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Order Delivery Data Collection</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="vieworders">Orders</a></li>
              <li class="breadcrumb-item active">Recieve Batch</li>
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
                <h5 class="m-0">Featured</h5>
              </div>
              <div class="card-body">
                <div class="row">
                <?php if ($_GET['data-type'] == "advanced"):?>
                    <div class="col-md-6">
                      <div class="row">
                        <div class="form-group col-md-6 form-floating">
                          <select class="form-select" id="orderproducts" aria-label="Floating label select example">
                            <option selected>Select a product</option>
                          </select>
                        </div>
                        <div class="form-group col-md-6 form-floating mb-3">
                          <input type="text" class="form-control" id="snumber" name="snumber">
                          <label for="mdate">Serial Number</label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12 form-floating mb-3" style="display: none;">
                          <input type="number" class="form-control" id="bquantity" name="bquantity">
                          <label for="bquantity">Quantity</label>
                        </div>
                        <div class="form-group col-md-6 form-floating mb-3">
                          <input type="date" class="form-control" id="mdate" name="mdate">
                          <label for="mdate">Manufacturing Date</label>
                        </div>
                        <div class="form-group col-md-6 form-floating mb-3">
                          <input type="date" class="form-control" id="edate" name="edate">
                          <label for="mdate">Expiry Date</label>
                        </div>
                        <div id="productList"></div>
                        <div class="text-center">
                          <button type="button" class="btn btn-primary" id="addProductBtn" name="addproduct" onclick="addProduct()">Add Product</button>
                          <button type="button" class="btn btn-primary" name="addbatch" onclick="createBatch()">Create Batch</button>
                        </div>
                      </div>
                    </div>
                  <?php elseif ($_GET['data-type'] == "basic"):?>
                    <div class="col-md-6">
                      <div class="row">
                        <div class="form-group col-md-6 form-floating">
                          <select class="form-select" id="orderproducts" aria-label="Floating label select example">
                            <option selected>Select a product</option>
                          </select>
                        </div>
                        <div class="form-group col-md-6 form-floating mb-3">
                          <input type="number" class="form-control" id="bquantity" name="bquantity">
                          <label for="bquantity">Quantity</label>
                        </div>
                      </div>
                      <div class="row">
                        <div id="productList"></div>
                        <div class="text-center">
                          <button type="button" class="btn btn-primary" name="addbatch" onclick="createBatch()">Create Batch</button>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                  <div class="col-md-6">
                    <table id="batches" class="table-striped table-hover tables display" style="width:100%">
                      <thead>
                        <tr>
                          <th>BatchId</th>
                          <th>Quantity</th>
                          <th>Product</th>
                          <th>Date Created</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $table = "batches";
                          $item = "order_id";
                          $value = $_GET['order-id'];
                          $options = null;
                          $Batch = OrdersModel::mdlShowBatch($table, $item, $value, $options);
                          // Your PHP code here
                          foreach ($Batch as $key => $val) {
                            
                            $table = "batch_items";
                            $item = "batch_id";
                            $value = $val['batch_id'];
                            $options = null;
                            $batchitems = OrdersModel::mdlShowBatch($table, $item, $value, $options);
                            // Output main table row
                            echo '
                            <tr >
                              <td>' . $val['batch_id'] . '</td>
                              <td>' . $val['quantity'] . '</td>
                              <td>' . $val['product_id'] . '</td>
                              <td>' . $val['datecreated'] . '</td>
                            </tr>';
                            if ($batchitems) {
                              // Output hidden child row with batch items
                              echo '
                              <tr>
                                <td colspan="4">
                                  <table class="table-striped table-hover table mb-0">
                                          <thead>
                                            <tr>
                                              <th>Serial Numbers</th>
                                              <th>Manufacturing dates</th>
                                              <th>Expiry Dates</th>
                                            </tr>
                                          </thead>
                                          <tbody>';
                              
                              foreach ($batchitems as $item) {
                                echo '<tr>
                                        <td>' . $item['serialNumber'] . '</td>
                                        <td>' . $item['manufacturing_date'] . '</td>
                                        <td>' . $item['expiry_date'] . '</td>
                                      </tr>';
                              }
                              
                              echo '</tbody></table></td></tr>';
                            }
                          }
                        ?>
                      </tbody>
                    </table>
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
<!-- Your existing HTML and PHP code -->

