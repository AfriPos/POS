<?php

  require 'barcode/barcode128.php';

?>
   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">View Product</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item"><a href="products">Product list</a></li>
              <li class="breadcrumb-item active">View Product</li>
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
                <h5 class="m-0">View Product</h5>
              </div>
              <div class="card-body">
                <?php
                    
                    $item = "id"; 
                    $value = $_GET["product-id"];
                    $order='id';

                    $product = productController::ctrShowProducts($item, $value, $order);
                    
                    $item = "id";
                    $value = $product["idCategory"];
                    
                    $category = categoriesController::ctrShowCategories($item, $value);

                    // var_dump($$product["barcode"]);
                    
                                        // <span class="badge badge-light">'.bar128($product["barcode"]).'</span>

                    echo '
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group">
                                    <center><p class="list-group-item list-group-item-info"><b>PRODUCT DETAILS</b></p></center>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Barcode</b>
                                        <span class="badge badge-dark">'.$product["barcode"].'</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Product Name</b>
                                        <span class="badge badge-info">'.$product["product"].'</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Category</b>
                                        <span class="badge badge-warning">'.$category["Category"].'</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Description</b>
                                        <span class="badge badge-success">'.$product["description"].'</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Stock</b>
                                        <span class="badge badge-primary">'.$product["stock"].'</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Purchase Price</b>
                                        <span class="badge badge-danger">'.$product["purchaseprice"].'</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Sale Price</b>
                                        <span class="badge badge-dark">'.$product["saleprice"].'</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Product Profit</b>
                                        <span class="badge badge-success">'.($product["saleprice"]-$product["purchaseprice"]).'</span>
                                    </li>
                                </ul>
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
 