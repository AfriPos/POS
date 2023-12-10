 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Sales</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="Home">Home</a></li>
              <li class="breadcrumb-item active">Sales reports</li>
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
              <div class="d-flex justify-content-between">
                <div class="input-group">
                  <button type="button" class="btn btn-default btn-sm dates" id="daterange-btn2">
                    <span>
                      <i class="fa fa-calendar"></i> Date range
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
                <div class="card-tools">
                  <?php 
                    if (isset($_GET['initialDate'])) {
                      echo'<a href="views/modules/printreport.php?initialDate='.$_GET["initialDate"].'&finalDate='.$_GET["finalDate"].'">';
                    } else {
                      echo'<a href="views/modules/printreport.php?">';
                    }
                  ?>
                  <button type="button" class="btn btn-success btn-sm" id="printexcel">Download Excel Report</button> 
                  </a>
                </div>
              </div>
            </div>
            </div>

              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <?php
                      include 'reports/sales-graphs.php'
                    ?>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 col-xs-12">
                  <?php include 'reports/income.php'; ?>
                  </div>
                  <div class="col-md-6 col-xs-12">
                    <div class="row">
                      <div class="col-md-12 col-xs-12">
                        <?php include 'reports/sellers.php'; ?>
                      </div>
                    </div>
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
 
  