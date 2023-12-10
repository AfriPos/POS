<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Stores</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Add Store</li>
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
          <!-- Add Store Form -->
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h5 class="m-0">Add Store</h5>
            </div>
            <div class="card-body">
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="storeName">Store Name</label>
                  <input type="text" class="form-control" id="storeName" name="storeName" placeholder="Enter store name" required>
                </div>
                <div class="form-group">
                  <label for="storeAddress">Store Address</label>
                  <input type="text" class="form-control" id="storeAddress" name="storeAddress" placeholder="Enter store address" required>
                </div>
                <div class="form-group">
                  <label for="contactNumber">Contact Number</label>
                  <input type="text" class="form-control" id="contactNumber" name="contactNumber" placeholder="Enter contact number" required>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="storeEmail" name="storeEmail" placeholder="Enter email" required>
                </div>
                <div class="form-group">
                  <label for="storeManager">Store Manager</label>
                  <input type="text" class="form-control" id="storeManager" name="storeManager" placeholder="Enter store manager" required>
                </div>
                <div class="form-group">
                  <label for="openingTime">Opening Time</label>
                  <input type="time" class="form-control" id="openingTime" name="openingTime" required>
                </div>
                <div class="form-group">
                  <label for="closingTime">Closing Time</label>
                  <input type="time" class="form-control" id="closingTime" name="closingTime" required>
                </div>
                <div class="form-group">
                  <div class="panel"><label for="storeLogo">Logo</label></div>
                  <input type="file" class="storeLogo" name="storeLogo" id="storeLogo" >
                  <p class="help-block">Maximum file size 2mb</p>
                  <img src="views/img/store/default/store.png" class="thumbnail storeLogopreview" width="100px">
                </div>
                <button type="submit" class="btn btn-primary" name="addStore">Add Store</button>
                <?php

                  $createstore = new storeController();
                  $createstore -> ctrCreateStore();

                ?>  
              </form>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col-lg-12 -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
