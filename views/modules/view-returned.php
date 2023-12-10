   
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Returned products</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Returned products</li>
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
                <h5 class="m-0">Returned products</h5>
              </div>
              <div class="card-body">
                
              <table id="example1" class="table-striped tables display" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Return date </th>
                    <th>Reason</th>
                    <th>Return type</th>
                  </tr> 
                </thead>
                <tbody>
                  <?php
                    $item = "store_id";
                    $value = $_SESSION['storeid'];

                    $returns = ReturnProductController::ctrShowReturnProducts($item, $value);
                    
                    foreach($returns as $return=>$val){

                      $item = "supplierid";
                      $value = $val['supplier'];

                      $supplier = supplierController::ctrShowSuppliers($item, $value);
                      
                      $item2="barcode";
                      $value2=$val["product"];
                      $order='id';
                      $product=productController::ctrShowProducts($item2, $value2, $order);
                      
                      echo '

                            <tr>
                            <td>'.($return+1).'</td>
                            <td>'.$product["product"].'</td>
                            <td>'.$val["quantity"].'</td>
                            <td>'.$supplier["name"].'</td>
                            <td>'.$val["return_date"].'</td>
                            <td>'.$val["reason"].'</td>
                            <td>'.$val["return_type"].'</td>
                            
                            ';

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
 
