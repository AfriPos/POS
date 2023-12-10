<?php
  function fillProducts(){

    $item = "store_id";
    $value = $_SESSION['storeid'];
    $order = 'id';
    $products = productController::ctrShowProducts($item, $value, $order, true);

    $output = '';
    foreach ($products as $row) {
        $output .= '<option value="' . $row['id'] . '">' . $row['product'] . '</option>';
    }

    return $output;
  }
 ?>
 <script>
  // Show SweetAlert on page load
  document.addEventListener('DOMContentLoaded', function () {
    // Check if DCM parameter is present in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const dcmParam = urlParams.get('DCM');

    // Execute the function only if DCM parameter is not already set
    if (!dcmParam) {
      showConfirmationDialog();
    } else{
      if (dcmParam == "basic") {
        document.getElementById('basicForm').style.display = 'block';
        $("#mdate, #edate, #snumber").closest(".form-group").hide();
        $("#addProductBtn, #productList").hide();
        $("#productImage").show();
      } else if(dcmParam == "advanced"){
        document.getElementById('basicForm').style.display = 'block';
        $("#mdate, #edate ,#snumber").closest(".form-group").show();
        $("#astock").closest(".form-group").hide();
        $("#addProductBtn, #productList").show();
        $("#productImage").hide();
      }
    }
  });
  function showConfirmationDialog() {
    Swal.fire({
      title: 'Choose Data Collection Type',
      icon: 'question',
      showDenyButton: true,
      denyButtonColor: "#236cb0",
      confirmButtonText: 'Basic',
      // DenyButtonText: 'Advanced',
      denyButtonText: `Advanced`,
    }).then((result) => {
      if (result.isConfirmed) {
      // User chose Basic, show Basic form
      window.location = "index.php?route=stock&DCM=basic";
    } else if (result.dismiss) {
      // User closed the modal without selecting anything
      // You can handle this case, e.g., show a default form or take some action

      document.getElementById('basicForm').style.display = 'none';
      document.getElementById('error').style.display = 'block';
    } else if (result.isDenied) {
      // User chose Advanced, show Advanced form
      window.location = "index.php?route=stock&DCM=advanced";
    }
    });
  }
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Add Stock</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="home">Home</a></li>
              <li class="breadcrumb-item active">Add Stock</li>
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
                <h5 class="m-0">Add Stock</h5>
              </div>
              <div class="card-body">
                <div class="row text-center" id="error" style="display: none;">
                  <p>You have to select a data collection method to proceed, please <a href="javascript:location.reload(true);">refresh</a> the page</p>
                </div>
                <div id="basicForm" style="display: none;">
                  <form method="post" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-lg-5">
                        <ul class="list-group">
                          <!-- <center><p class="list-group-item list-group-item-info"><b>PRODUCT</b></p></center> -->
                        </ul>
                        <div class="form-group">
                          <label>Product name</label>
                          <select class="form-control select2 productsdrop" data-dropdown-css-class="select2-purple" style="width: 100%;" name="product">
                              <option value="">Select or search</option><?php echo fillProducts();?>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="sproduct">Selected product</label>
                          <input type="text" class="form-control" name="sproduct" id="sproduct" placeholder="Selected product will appear here" readonly>
                        </div>
                        <div class="form-group">
                          <label for="cstock">Current stock</label>
                          <input type="text" class="form-control" name="cstock" id="cstock" placeholder="Current stock will appear here" readonly>
                        </div>
                        
                        <div class="form-group form-floating mb-3">
                          <input type="text" class="form-control" id="snumber" name="snumber">
                          <label for="mdate">Serial Number</label>
                        </div>

                        <div class="form-group form-floating mb-3">
                          <input type="date" class="form-control" id="mdate" name="mdate">
                          <label for="mdate">Manufacturing Date</label>
                        </div>

                        <div class="form-group form-floating mb-3">
                          <input type="date" class="form-control" id="edate" name="edate">
                          <label for="mdate">Expiry Date</label>
                        </div>

                        <div class="form-group">
                          <label for="astock">Stock</label>
                          <input type="number" min="1" step="any" class="form-control" name="astock" id="astock" placeholder="Add to stock">
                        </div>
                        <div class="card-footer">
                          <div class="text-center">
                            <button type="button" class="btn btn-primary" id="addProductBtn" name="addproduct" onclick="addStockProduct()">Add Product</button>
                            <button type="submit" class="btn btn-primary" name="addStock">Save</button>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-7">
                        <ul class="list-group">
                            <!-- <center><p class="list-group-item list-group-item-info"><b>PRODUCT IMAGE</b></p></center> -->
                            <img class="img-responsive" id="productImage">
                            <!-- <div id="productImage">image</div> -->
                            <!-- <div id="productList">jkbj</div> -->
                            <!-- <input type="text" name="productList" id="productList"> -->
                            <textarea name="productList" id="productList" cols="30" rows="10"></textarea>
                            <div id="shownproducts"></div>
                        </ul>
                      </div>
                    </div>
                      <?php
                        $addStock= new productController();
                        $addStock->ctrAddingStock();
                      ?>
                  </form>
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
 