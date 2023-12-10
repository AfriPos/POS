 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Taxes And Discounts</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item active">Taxes And Discounts</li>
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
                <h5 class="m-0">Add Tax or Discount</h5>
              </div>
              <div class="card-body">
                <div class="col-md-12">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                                <label >VAT</label>
                                <input type="text" class="form-control" name="VAT" id="VAT" placeholder="Add VAT">
                            </div>  
                            <div class="form-group">
                                <label >VAT Type</label>
                                <input type="text" class="form-control" name="discount" id="discount" placeholder="Add VATtype">
                            </div>  
                        </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <div class="text-center">
                        <button type="submit" class="btn btn-primary" name="addTax">Save</button>
                      </div>
                    </div>
                            <?php
                                $add= new taxController();
                                $add->ctrCreateTax();
                            ?>
                    </form>
                </div>
                    <!-- end of col-md-12 -->
              </div>
            </div>
    
          </div>
          <!-- /.col-md-4 -->
          <div class="col-lg-8">
          <!-- /.col-md-6 -->
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h5 class="m-0">Taxes And Discounts</h5>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table-striped tables display" style="width:100%">
                  <thead>
           
                    <tr>
                      
                      <th>#</th>
                      <th>VAT</th>
                      <th>VAT Type</th>
                      <th>Actions</th>
                    </tr> 

                    </thead>
  
                  <tbody>
                  <?php

                    $item = "store_id";
                    $value = $_SESSION['storeid'];

                    $taxes = taxController::ctrShowTax($item, $value);

                    // var_dump($taxes);

                    foreach ($taxes as $key => $value) {

                      echo '<tr>
                              <td>'.($key+1).'</td>
                              <td class="text-uppercase">'.$value['VAT'].'</td>
                              <td class="text-uppercase">'.$value['VATName'].'</td>
                              <td>

                                <div class="btn-group">
                                    
                                  <button class="btn btnEdittax" taxId="'.$value["taxId"].'" data-toggle="modal" data-target="#edittax"><i class="fa fa-edit"></i></button>

                                  <button class="btn btnDeletetax" taxId="'.$value["taxId"].'"><i class="fa fa-times"></i></button>

                                </div>  

                              </td>

                            </tr>';
                    }

                  ?>

                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
    
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!--=====================================
=            module edit tax            =
======================================-->

<!-- Modal -->
<div id="edittax" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form role="form" method="POST">
        <div class="modal-header">
            <h4 class="modal-title">Edit Tax and Discount</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
         
        </div>
        <div class="modal-body">
          <div class="box-body">

            <!--Input name -->
            <div class="form-group">
                <label >VAT</label>
                <input type="text" class="form-control" name="editVAT" id="editVAT" placeholder="Add VAT">
                <input type="hidden" name="actualtaxId" id="actualtaxId">
            </div>  
            <div class="form-group">
                <label >VAT Type</label>
                <input type="text" class="form-control" name="editdiscount" id="editdiscount" placeholder="Add VATtype">
            </div> 

          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="edittax">Save changes</button>
        </div>

        <?php
  
          $edittax= new taxController();
          $edittax-> ctrEditTax();
        ?>
      </form>
    </div>

  </div>
</div>

<?php
  $deletetax = new taxController();
  $deletetax -> ctrDeleteTax();
?>
 