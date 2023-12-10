   
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Manage stores</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Manage stores</li>
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
                <table id="example1" class="table-striped tables display" style="width:100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Store Name</th>
                      <th>Address</th>
                      <th>Contact</th>
                      <th>Email</th>
                      <th>Manager</th>
                      <th>logo</th>
                      <th>Opening time</th>
                      <th>Closing time</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $item = null;
                      $value = null;
                      $stores = storeController::ctrShowStores($item, $value);
                      foreach ($stores as $key => $value) {
                        echo '
                            <tr>
                            <td>'.($key+1).'</td>
                            <td>'.$value["store_name"].'</td>
                            <td>'.$value["store_address"].'</td>
                            <td>'.$value["contact_number"].'</td>
                            <td>'.$value["email"].'</td>
                            <td>'.$value["store_manager"].'</td>';

                        if ($value["logo"] != ""){
                          echo '<td><img src="'.$value["logo"].'" class="img-thumbnail" width="40px"></td>';
                        }else{
                          echo '<td><img src="views/img/store/default/store.png" class="img-thumbnail" width="40px"></td>';  
                        }
                        echo
                            '<td>'.$value["opening"].'</td>
                            <td>'.$value["closing"].'</td>
                            <td>
                            <button class="btn btnEditStore"  Storeid="'.$value["store_id"].'" image="'.$value["logo"].'" data-toggle="modal" data-target="#modalEditStore"><i class="fa fa-edit"></i></button>
                            <button class="btn btnDeleteStore"  Storeid="'.$value["store_id"].'" image="'.$value["logo"].'"><i class="fa fa-times"></i></button>
                            </td>';
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
  
<!-- Modal -->
<div class="modal fade" id="modalEditStore" tabindex="-1" role="dialog" aria-labelledby="storeModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="storeModalLabel">Store Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="editstoreName">Store Name</label>
                <input type="text" class="form-control" id="editstoreName" name="editstoreName" placeholder="Enter store name" required>
                <input type="hidden" name="editstoreId" id="editstoreId">
              </div>
              <div class="form-group">
                <label for="editstoreAddress">Store Address</label>
                <input type="text" class="form-control" id="editstoreAddress" name="editstoreAddress" placeholder="Enter store address" required>
              </div>
              <div class="form-group">
                <label for="editcontactNumber">Contact Number</label>
                <input type="text" class="form-control" id="editcontactNumber" name="editcontactNumber" placeholder="Enter contact number" required>
              </div>
              <div class="form-group">
                <div class="panel"><label for="storeLogo">Logo</label></div>
                <input type="file" class="storeLogo" name="storeLogo" id="storeLogo" >
                <p class="help-block">Maximum file size 2mb</p>
                <img src="views/img/store/default/store.png" class="thumbnail storeLogopreview" width="100px">
                <input type="hidden" name="currentImage" id="currentImage">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="editstoreEmail" name="editstoreEmail" placeholder="Enter email" required>
              </div>
              <div class="form-group">
                <label for="editstoreManager">Store Manager</label>
                <input type="text" class="form-control" id="editstoreManager" name="editstoreManager" placeholder="Enter store manager" required>
              </div>
              <div class="form-group">
                <label for="editopeningTime">Opening Time</label>
                <input type="time" class="form-control" id="editopeningTime" name="editopeningTime" required>
              </div>
              <div class="form-group">
                <label for="editclosingTime">Closing Time</label>
                <input type="time" class="form-control" id="editclosingTime" name="editclosingTime" required>
              </div>
            </div>
          </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" name="editStore">Edit store</button>
      </div>
      <?php

        $editstore = new storeController();
        $editstore -> ctrEditStore();

      ?>
      </form>
    </div>
  </div>
</div>

<?php

$deletestore = new storeController();
$deletestore -> ctrDeleteStore();

?>