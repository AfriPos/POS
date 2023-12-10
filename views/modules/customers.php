<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Manage Customers</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Manage Customer</li>
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
        <div class="col-lg-5">
          <!-- Add Customer Form -->
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h5 class="m-0">Add Customer</h5>
            </div>
            <div class="card-body">
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="CustomerName">Customer Name</label>
                  <input type="text" class="form-control" id="CustomerName" name="CustomerName" placeholder="Enter Customer name" required>
                </div>
                <div class="form-group">
                  <label for="CustomerAddress">Customer Address</label>
                  <input type="text" class="form-control" id="CustomerAddress" name="CustomerAddress" placeholder="Enter Customer address" required>
                </div>
                <div class="form-group">
                  <label for="contactNumber">Contact Number</label>
                  <input type="text" class="form-control" id="contactNumber" name="contactNumber" placeholder="Enter contact number" required>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="CustomerEmail" name="CustomerEmail" placeholder="Enter email" required>
                </div>
                <button type="submit" class="btn btn-primary" name="addCustomer">Add Customer</button>
                <?php

                  $createCustomer = new customerController();
                  $createCustomer -> ctrCreateCustomer();

                ?>  
              </form>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col-lg-12 -->
        <div class="col-lg-7">
        <!-- /.col-md-6 -->

          <div class="card card-primary card-outline">
            <div class="card-body">
              <table id="example1" class="table-striped tables display" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $item = null;
                    $value = null;
                    $customers = customerController::ctrShowCustomers($item, $value);
                    foreach ($customers as $key => $value) {
                      echo '
                          <tr>
                          <td>'.($key+1).'</td>
                          <td>'.$value["name"].'</td>
                          <td>'.$value["address"].'</td>
                          <td>'.$value["phone"].'</td>
                          <td>'.$value["email"].'</td>
                          <td>
                          <button class="btn btnEditcustomer"  customerid="'.$value["customer_id"].'" data-toggle="modal" data-target="#modalEditCustomer"><i class="fa fa-edit"></i></button>
                          <button class="btn btnDeletecustomer"  customerid="'.$value["customer_id"].'"><i class="fa fa-times"></i></button>
                          </td>';
                    }
                  ?>
                </tbody>
                
              </table>
            </div>
          </div>
  
        </div>
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->



<!-- Modal -->
<div class="modal fade" id="modalEditCustomer" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customerModalLabel">Customer Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="form-group">
              <label for="editcustomerName">customer Name</label>
              <input type="text" class="form-control" id="editcustomerName" name="editcustomerName" placeholder="Enter customer name" required>
              <input type="" name="editcustomerId" id="editcustomerId">
            </div>
            <div class="form-group">
              <label for="editcustomerAddress">customer Address</label>
              <input type="text" class="form-control" id="editcustomerAddress" name="editcustomerAddress" placeholder="Enter customer address" required>
            </div>
            <div class="form-group">
              <label for="editcontactNumber">Contact Number</label>
              <input type="text" class="form-control" id="editcontactNumber" name="editcontactNumber" placeholder="Enter contact number" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" id="editcustomerEmail" name="editcustomerEmail" placeholder="Enter email" required>
            </div>
          </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" name="editcustomer">Edit customer</button>
      </div>
      <?php

        $editcustomer = new customerController();
        $editcustomer -> ctrEditCustomer();

      ?>
      </form>
    </div>
  </div>
</div>

<?php

// $deletecustomer = new customerController();
// $deletecustomer -> ctrDeleteCustomer();

?>