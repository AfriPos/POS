   
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Logs</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Logs </li>
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
                            <th>User</th>
                            <th>Activity Type</th>
                            <th>Activity Description</th>
                            <th>Timestamp</th>
                            <th>Store</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php
                            $item = null;
                            $value = null;
                            $logs = activitylogController::ctrFetchActivityLog($item, $value);
                            // var_dump($logs);
                            foreach ($logs as $log) {
                                $item = "userId";
                                $value = $log["UserID"];
                                $user = userController::ctrShowUsers($item, $value);
                                if ($user) {
                                    $username = $user['username'];
                                } else {
                                    $username = "[deleted]";
                                }
                                if ($log['store_id']) {
                                    $item = "store_id";
                                    $value = $log["store_id"];
                                    $store = storeController::ctrShowStores($item,$value);
                                    if ($store){
                                        $storename = $store[0]["store_name"];
                                    } else {
                                        $storename = " ";
                                    }
                                } else {
                                    $storename = " ";
                                }

                                echo '
                                <tr>
                                <td>'.$username.'</td>
                                <td>'.$log["ActivityType"].'</td>
                                <td>'.$log["ActivityDescription"].'</td>
                                <td>'.$log["Timestamp"].'</td>
                                <td>'.$storename.'</td>';
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
