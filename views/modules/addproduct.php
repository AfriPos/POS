 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Products</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item active">Products</li>
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
              <div class="card-body">
                <div class="card-header">
                  <h5 class="m-0">
                    Add products
                  </h5>
                </div>
                <div class="col-md-12">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Barcode</label>
                                        <input type="text" class="form-control" name="txtbarcode" id="txtbarcode" placeholder="Enter Barcode" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Product Name</label>
                                        <input type="text" class="form-control" name="txtproductname" id="txtproductname" placeholder="Enter Product name" required>
                                    </div>
                                    <div class="form-group">
                                      <label for="txtcategory">Category</label>
                                      <select class="form-control" name="txtcategory" id="txtcategory" required>
                                        <option selected disabled  value="">--Select Category--</option>

                                            <?php
                                              $item = "store_id";
                                              $value = $_SESSION['storeid'];

                                              $categories = categoriesController::ctrShowCategories($item, $value);

                                              foreach ($categories as $key => $value) {
                                                
                                                echo '<option value="'.$value["id"].'">'.$value["Category"].'</option>';

                                              }

                                            ?>
                                      </select>
                                    </div>
                                    <div class="form-group">
                                      <label for="txttaxcat">Tax type</label>
                                      <select name="txttaxcat" id="txttaxcat" class="form-control">
                                        <option selected disabled value="">--Select Tax type--</option>
                                        <?php
                                          $item = "store_id";
                                          $value = $_SESSION['storeid'];

                                          $tax = taxController::ctrShowTax($item,$value);
                                          
                                          foreach ($tax as $key => $value) {
                                              
                                            echo '<option value="'.$value["VAT"].'">'.$value["VATName"].'</option>';

                                          }

                                        ?>
                                      </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" placeholder= "Enter description" name="txtdescription" id="txtdescription" rows="4"></textarea>
                                      
                                    </div>
                                    
                                </div>
                                <div class="col-md-6">  
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Stock quantity</label>
                                        <input type="number" min="1" step="any" class="form-control" name="txtstock" id="txtstock" placeholder="Enter stock quantity" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Purchase price</label>
                                        <input type="number" min="1" step="any" class="form-control" name="txtpurchase" id="txtpurchase" placeholder="Enter purchase price" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Sale price</label>
                                        <input type="number" min="1" step="any" class="form-control" name="txtsale" id="txtsale" placeholder="Enter sale price" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="panel"><label for="exampleInputPassword1">Photo</label></div>
                                        <input type="file" class="txtproductimage" name="txtproductimage" id="txtproductimage" >
                                        <p class="help-block">Maximum file size 2mb</p>
                                        <img src="views/img/products/default/anonymous.png" class="thumbnail preview" width="100px">
                                    </div>
                                </div>                  



                            </div>
                        </div>
                     <!-- /.card-body -->

                        <div class="card-footer">
                          <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="addproduct">Add Product</button>
                          </div>
                        </div>
                        <?php
                          $addproduct= new productController();
                          $addproduct->ctrCreateProducts();
                        ?>
                    </form>
                </div>
                    <!-- end of col-md-12 -->
              </div>
            </div>
    
          </div>
          <!-- /.col-md-4 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
