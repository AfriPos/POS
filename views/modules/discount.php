 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Discounts</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item active">Discounts</li>
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
                <h5 class="m-0">Discounts</h5>
              </div>
              <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                            <label>Product name</label>
                                <select class="form-control select2bs4" name="discountproduct" id="discountproduct" data-dropdown-css-class="select2-blue" style="width: 100%;">
                                    <option selected disabled value="">--Select or search--</option>
                                    <?php 
                                      $item = "store_id";
                                      $value = $_SESSION['storeid'];
                                      $order='product';
                                      

                                      $product = productController::ctrShowProducts($item, $value, $order, true);
                                      foreach ($product as $key => $value) {

                                        echo '<option value="'.$value["id"].'">'.$value["product"].'</option>';

                                      }                                        
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Discount name</label>
                                <input type="text" class="form-control" name="discountname" id="discountname" placeholder="e.g chritsmas offer">
                            </div>
                            <div class="form-group">
                                <label for="">Discount Amount</label>
                                <input type="number" min="0" class="form-control" name="discountamount" id="discountamount" placeholder="Ksh">
                            </div>
                            <div class="form-group">
                                <label for="">Start Date</label>
                                <input type="date" class="form-control" name="startdate" id="startdate">
                            </div>
                            <div class="form-group">
                                <label for="">End Date</label>
                                <input type="date" class="form-control" name="enddate" id="enddate">
                            </div>
                        <div class="card-footer">
                          <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="adddiscount" id="addDiscount">Add Discount</button>
                          </div>
                        </div>
                        <?php
                          $adddiscount= new discountController();
                          $adddiscount->ctrCreateDiscount();
                        ?>
                        </form>
                    </div>
                    <div class="col-lg-8">
                        <table id="example1" class="table-striped tables display" style="width:100%">
                            <thead>
                                <tr>
                                  <td>#</td>
                                  <td>Product Name</td>
                                  <td>Discount</td>
                                  <td>Discount Amount</td>
                                  <td>Start Date</td>
                                  <td>End Date</td>
                                  <td>Actions</td>
                                </tr>
                            </thead>
                            <tbody>
                              <?php

                                $item = "store_id";
                                $value = $_SESSION['storeid'];

                                $discount = discountController::ctrShowDiscount($item, $value);
                                // var_dump($discount);
                                
                                foreach ($discount as $key => $val) {

                                  // var_dump($val["product"]);

                                  $item = "id";
                                  $value = $val["product"];
                                  $order='id';

                                  $product = productController::ctrShowProducts($item, $value, $order, false);

                                  echo '<tr>
                                          <td>'.($key+1).'</td>
                                          <td>'.$product["product"].'</td>
                                          <td>'.$val["discount"].'</td>
                                          <td>'.$val["amount"].'</td>
                                          <td>'.$val["startdate"].'</td>
                                          <td>'.$val["enddate"].'</td>
                                          <td>
                                            <div class="btn-group">  
                                              <button class="btn btnEditDiscount" idDiscount="'.$val["disId"].'" data-toggle="modal" data-target="#editDiscount"><i class="fa fa-edit"></i></button>
                                              <button class="btn btnDeleteDiscount" idDiscount="'.$val["disId"].'"><i class="fa fa-times"></i></button>
                                            </div>  
                                          </td>
                                        </tr>';

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

  <div class="modal fade" id="editDiscount">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit product</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>         
        <form action="" method="post" enctype="multipart/form-data">
        <div class="card-body">
          <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
            <label>Product name</label>
            <input type="text" class="form-control" name="editproduct" id="editproduct" readonly>
            <input type="hidden" name="barcode" id="barcode" >
            <input type="hidden" name="discountid" id="discountid" >
            </div>
            <div class="form-group">
                <label for="">Discount name</label>
                <input type="text" class="form-control" name="editdiscountname" id="editdiscountname">
            </div>
            <div class="form-group">
                <label for="">Discount Amount</label>
                <input type="number" min="0" class="form-control" name="editdiscountamount" id="editdiscountamount">
            </div>
            <div class="form-group">
                <label for="">Start Date</label>
                <input type="date" class="form-control" name="editstartdate" id="editstartdate">
            </div>
            <div class="form-group">
                <label for="">End Date</label>
                <input type="date" class="form-control" name="editenddate" id="editenddate">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" class="btn btn-default" data-dismiss="modal" name="editproduct">Close</button>
                <button type="submit" class="btn btn-primary" name="editdiscount">Save</button>
            </div>
            <?php
              $editdiscount= new discountController();
              $editdiscount->ctrEditDiscount();
            ?>
          </form>
        </div>
      </div>
    </div>
  </div>

  
  <?php
    $deletediscount= new discountController();
    $deletediscount->ctrDeleteDiscount();
  ?>
 
