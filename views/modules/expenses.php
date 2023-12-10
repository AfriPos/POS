<?php
    require_once 'models/connection.php';
    function fillType($pdo){
        $pdo = connection::connect();
        $output="";
        $select=$pdo->prepare('select * from expensecat order by type ASC');

        $select->execute();
        
        $result=$select->fetchAll();

        foreach($result as $row){
            $output.='<option value="'.$row['type'].'">' . $row['type'] . '</option>';
        }


        return $output; ;

    }

    

 ?>
   <!-- Content Wrapper. Contains page content -->
   <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Expenses</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Expenses</li>
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
                <div class="d-flex justify-content-between">
                  <h5 class="m-0">Expense</h5>
                  <?php
                    if ($_SESSION['role'] == "Administrator") {
                      echo '<button class="btn btn-primary float-right btnAddExpenseType" data-toggle="modal" data-target="#addExpenseType">Add Expense Type</button>';
                    }
                  ?>
                </div>
              </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                              <div class="form-group">
                                  <label for="expense">Expense</label>
                                  <input type="text" class="form-control" name="expense" id="expense" placeholder="Expense Name" required>
                              </div>
                              <div class="form-group">
                                <label>Expense Type</label>
                                    <select class="form-control select2" data-dropdown-css-class="select2-primary" name="expenseType" style="width: 100%;" id="type-select">
                                        <option value="">Select or search</option><?php echo fillType($pdo);?>

                                    </select>
                                </div>
                              <div class="form-group">
                                  <label for="date">Date</label>
                                  <input type="date" class="form-control" name="date" id="date" placeholder="Expense Date" required>
                              </div>
                              <div class="form-group">
                                  <label for="amount">Amount</label>
                                  <input type="number" class="form-control" min="1" name="amount" id="amount" placeholder="Amount Spent" required>
                              </div>
                              <div class="form-group">
                                  <div class="panel"><label for="reciept">Reciept</label></div>
                                  <input type="file" class="reciept" name="reciept" id="reciept" >
                                  <p class="help-block">Maximum file size 2mb</p>
                                  <img src="views/expenses/defaults/reciept.png" class="thumbnail recieptthumb" width="100px">
                              </div>
                            </div>
                        <!-- /.card-body -->

                            <div class="card-footer">
                              <div class="text-center">
                                  <button type="submit" class="btn btn-primary" name="addExpense">Add Expense</button>
                              </div>
                            </div>
                                <?php
                                  $add= new expenseController();
                                  $add->ctrAddExpense();
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
                <h5 class="m-0">Expenses</h5>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table-striped tables display" style="width:100%">
                  <thead>
           
                    <tr>
                      
                      <th>#</th>
                      <th>Expense</th>
                      <th>Type</th>
                      <th>Date</th>
                      <th>Amount</th>
                      <th>Reciept</th>
                      <th>Action</th>
                    </tr> 

                    </thead>
  
                  <tbody>
                      <?php

                        $item = null; 
                        $value = null;

                        if ($_SESSION['storeid'] != null) {
                          $item = "store_id";
                          $value = $_SESSION['storeid'];
                        }else {
                          echo'<script>

                              Swal.fire({
                                  icon: "warning",
                                  title: "No Store Selected",
                                  text: "Please select a store first to view products.",
                                  showConfirmButton: false,
                                  timer: 2000 // Auto close after 2 seconds
                                  }).then(function () {
                                    // Code to execute after the alert is closed
                                    window.location = "dashboard";
                                  });
                
                            </script>';
                        }

                        $expenses= expenseController::ctrShowExpenses($item, $value);
                        foreach ($expenses as $key => $val) {
                          echo '<tr>
                          <td>'.($key+1).'</td>
                          <td>'.$val["expense"].'</td>
                          <td>'.$val["expense_type"].'</td>
                          <td>'.$val["date"].'</td>
                          <td>'.$val["amount"].'</td>';
                  
                          if (strtolower(pathinfo($val["receipt"], PATHINFO_EXTENSION)) === 'pdf') {
                            echo '<td><img src="views/expenses/defaults/pdf.png" class="img-thumbnail" width="40px"></td>';
                          } else {
                            echo '<td><img src="'.$val["receipt"].'" class="img-thumbnail" width="40px"></td>';
                          }
                          
                          echo '<td>
                          <button class="btn btnEditExpense" expenseId="'.$val["id"].'" data-toggle="modal" data-target="#editExpenseModal"><i class="fa fa-edit"></i></button>
                          <button class="btn btnDeleteExpense" expenseId="'.$val["id"].'" name="btnDeleteExpense"><i class="fa fa-times"></i></button>
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
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" role="dialog" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseModalLabel">Edit Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="form-group">
                  <label for="editExpense">Expense</label>
                  <input type="text" class="form-control" name="editExpenseId" id="editExpenseId" hidden>
                  <input type="text" class="form-control" name="editExpense" id="editExpense">
                </div>
                <div class="form-group">
                  <label for="editExpenseType">Expense Type</label>  
                  <select class="form-control" name="editExpenseType" id="editExpenseType">
                    <option value="">Select</option><?php echo fillType($pdo);?>
                    
                  </select>
                </div>
                <div class="form-group">
                  <label for="editDate">Date</label>
                  <input type="date" class="form-control" name="editDate" id="editDate">
                </div>
                <div class="form-group">
                  <label for="editAmount">Amount</label>
                  <input type="number" class="form-control" min="1" name="editAmount" id="editAmount">
                </div>
                <div class="form-group">
                    <div class="panel"><label for="editReceipt">Reciept</label></div>
                    <input type="file" class="editReceipt" name="editReceipt" id="editReceipt" >
                    <p class="help-block">Maximum file size 2mb</p>
                    <img src="views/expenses/defaults/reciept.png" class="thumbnail editrecieptthumb" width="100px">
                    <input type="hidden" name="existingFilePath" value="<?php echo $existingFilePath; ?>">
                </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary" name="updateExpense">Save Changes</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
                
                <?php
                  $edit= new expenseController();
                  $edit->ctrEditExpense();
                ?>
            </form>
        </div>
    </div>
</div>

<?php
  $delete=new expenseController();
  $delete->ctrDeleteExpense();
?>

<!-- Add Expense Type Modal -->
<div class="modal fade" id="addExpenseType" tabindex="-1" role="dialog" aria-labelledby="addExpenseTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseTypeModalLabel">Add Expense Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group">
                  <label for="expenseType">Expense Type</label>
            
                  <input type="text" class="form-control" name="expenseType" id="expenseType" placeholder="Add Expense Type">
                </div>
              </div>

                <?php
                  $add= new expenseController();
                   $add->ctrAddExpenseType();
                ?>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="addExpenseType">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
 
