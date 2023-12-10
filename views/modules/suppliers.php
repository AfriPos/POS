   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Supplier</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Supplier </li>
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
          <div class="col-lg-4">
          <!-- /.col-md-6 -->

            <div class="card card-primary card-outline">
              <div class="card-header">
                <h5 class="m-0">Add Supplier </h5>
              </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <form action="" method="post" enctype="multipart/form-data"  onsubmit="return validateAddedSupplier()">
                            <div class="card-body">
                              <div class="form-group">
                                  <label for="newSupplier">Supplier name</label>
                                  <input type="text" class="form-control" name="Supplier" id="Supplier" placeholder="Supplier name" required>
                              </div>
                              <div class="form-group">
                                  <label for="newAddress">Address</label>
                                  <input type="text" class="form-control" name="Address" id="Address" placeholder="Supplier address" required>
                              </div>
                              <div class="form-group">
                                  <label for="newAddress">Email</label>
                                  <input type="email" class="form-control" name="Email" id="Email" placeholder="Supplier email" required>
                              </div>
                              <div class="form-group">
                                  <label for="newAddress">Contact</label>
                                  <input type="text" class="form-control" name="Contact" id="Contact" placeholder="Supplier contact" required>
                              </div>
                            </div>
                        <!-- /.card-body -->

                            <div class="card-footer">
                              <div class="text-center">
                                  <button type="submit" class="btn btn-primary" name="addSupplier" >Add Suplier</button>
                              </div>
                            </div>
                                <?php
                                  $add= new supplierController();
                                  $add->ctrCreateSupplier();
                                ?>
                        </form>
                    </div> 
                </div>
            </div>
    
          </div>
          <div class="col-lg-8">
          <!-- /.col-md-6 -->
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h5 class="m-0">Supplier</h5>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table-striped tables display" style="width:100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Supplier</th>
                      <th>Address</th>
                      <th>Email</th>
                      <th>Contact</th>
                      <th>Action</th>
                    </tr> 

                    </thead>
  
                  <tbody id="tables">
                      <?php


                                            $item = "store_id";
                                            $value = $_SESSION['storeid'];

                        $suppliers = supplierController::ctrShowSuppliers($item, $value);
                        // var_dump($suppliers);

                        foreach ($suppliers as $key => $val) {

                          echo '
                            <tr>
                            <td>'.($key+1).'</td>
                            <td>'.$val["name"].'</td>
                            <td>'.$val["address"].'</td>
                            <td>'.$val["email"].'</td>
                            <td>'.$val["contact"].'</td>
                            <td><button class="btn btnEditSupplier" idSupplier="'.$val["supplierid"].'" data-toggle="modal" data-target="#modalEditSupplier"><i class="fa fa-edit"></i></button>
                            <button class="btn btnDeleteSupplier"  idSupplier="'.$val["supplierid"].'"><i class="fa fa-times"></i></button></td>
                          </tr>';

                        }

                      ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
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

  <div class="modal fade" id="modalEditSupplier">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Edit supplier</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>         
            <form action="" method="post" enctype="multipart/form-data" onsubmit="return validateEdittedSupplier()">
              <div class="card-body">
                  <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                            <label for="newSupplier">Supplier name</label>
                            <input type="text" class="form-control" name="newSupplier" id="newSupplier" placeholder="Supplier name" required>
                            <input type="hidden" name="supplierId" id="supplierId">
                        </div>
                        <div class="form-group">
                            <label for="newAddress">Address</label>
                            <input type="text" class="form-control" name="newAddress" id="newAddress" placeholder="Supplier address" required>
                        </div>
                        <div class="form-group">
                            <label for="newAddress">Email</label>
                            <input type="email" class="form-control" name="newEmail" id="newEmail" placeholder="Supplier email" required>
                        </div>
                        <div class="form-group">
                            <label for="newAddress">Contact</label>
                            <input type="text" class="form-control" name="newContact" id="newContact" placeholder="Supplier contact" required>
                        </div>
                      </div>               
                  </div>
              </div>
                <!-- /.card-body -->
              <div class="modal-footer justify-content-between">
                  <button type="submit" class="btn btn-default" data-dismiss="modal" name="editsupplier">Close</button>
                  <button type="submit" class="btn btn-primary" name="editsupplier">Save</button>
              </div>
                <?php

                  $editSupplier = new supplierController();
                  $editSupplier -> ctrEditSupplier();

                ?>   
            </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->

      <?php

        $deleteSupplier = new supplierController();
        $deleteSupplier -> ctrDeleteSupplier();

      ?>  
 
           
      ?>