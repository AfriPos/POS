 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Categories</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item active">Categories</li>
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
                <h5 class="m-0">Add Category</h5>
              </div>
              <div class="card-body">
                <div class="col-md-12">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Category</label>
                                <input type="text" class="form-control" name="newCategory" id="newCategory" placeholder="Add Category" required>
                            </div>  
                        </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <div class="text-center">
                        <button type="submit" class="btn btn-primary" name="addCat">Add Category</button>
                      </div>
                    </div>
                            <?php
                            $add= new categoriesController();
                            $add->ctrCreateCategory();
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
                <h5 class="m-0">Categories</h5>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table-striped tables display" style="width:100%">
                  <thead>
           
                    <tr>
                      
                      <th>#</th>
                      <th>Category</th>
                      <th>Actions</th>

                    </tr> 

                  </thead>
                  <tbody>
                  <?php

                    $item = "store_id";
                    $value = $_SESSION['storeid'];

                    $categories = categoriesController::ctrShowCategories($item, $value);


                    // var_dump($categories);

                    foreach ($categories as $key => $value) {

                      echo '<tr>
                              <td>'.($key+1).'</td>
                              <td>'.$value['Category'].'</td>
                              <td>

                                <div class="btn-group">

                                <button class="btn btnEditCategory" idCategory="'.$value["id"].'" data-toggle="modal" data-target="#editCategories"><i class="fa fa-edit"></i></button>';


                                    

                                  if ($_SESSION['role'] == "Administrator" || $_SESSION['role'] == "Supervisor") {
                                    echo '<button class="btn  btnDeleteCategory" idCategory="'.$value["id"].'"><i class="fa fa-times"></i></button>';
                                  }else{
                                    echo '<button class="btn askDeleteCategory" idCategory="'.$value["id"].'" data-toggle="modal" data-target="#askDeleteCategory" ><i class="fa fa-times"></i></button>';
                                  }
                                  

                      echo '      </div>  

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

<!--======================================
module edit Categories            
=======================================-->

<!--Edit Modal -->
<div id="editCategories" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form role="form" method="POST">
        <div class="modal-header">
            <h4 class="modal-title">Edit Categories</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <!--Input name -->
            <div class="form-group">
                <input class="form-control input-lg" type="text" id="editCategory" name="editCategory" required>
                <input type="hidden" name="idCategory" id="idCategory" required>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
        <?php
          $editCategory = new categoriesController();
          $editCategory -> ctrEditCategory();
        ?>
      </form>
    </div>
  </div>
</div>

<!--======================================
module make request
=======================================-->
<!--Request Modal -->
<div class="modal fade" id="askDeleteCategory">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Make request</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <!-- Delete reason -->
          <div class="form-group">
              <label for="reason">Reason for deletion:</label>
              <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
              <input type="hidden" name="id" id="id" required>
              <input type="hidden" name="type" id="type" value="Category deletion">
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="makeRequest">Send request</button>
        </div>
        <?php
          $makeRequest = new notificationController();
          $makeRequest -> ctrMakeRequest();
        ?>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<?php
  
  $deleteCategory = new categoriesController();
  $deleteCategory -> ctrDeleteCategory();
?>