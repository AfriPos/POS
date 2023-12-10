<style>
.card-custom {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 5px;
    transition: transform 0.3s ease-in-out;
}

.card-custom:hover {
    transform: translateY(-5px);
}

.card-custom .card-body {
    padding: 1.5rem;
}

.card-custom .card-title {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.card-custom .card-text {
    font-size: 0.9rem;
    color: #777;
}

.card-custom i.fa-solid {
    color: #007bff;
}

.card-custom p.text-right {
    font-size: 1.2rem;
    font-weight: bold;
    color: #28a745;
}
.custom-heading {
    color: #333;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0;
    text-decoration: none;
}
.btn-primary {
    transition: background-color 0.3s ease-in-out;
}

.btn-primary:hover {
    background-color: #fff;
    color: #007bff;
}
  .no-link-style  {
  text-decoration: none;
  }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Admin Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item active">Admin Dashboard </li>
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
                <div class="col-lg-3 col-md-6">
                    <a href="sales" class="no-link-style">
                        <div class="card card-custom">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-money-bill fa-2x mr-3"></i>
                                    <h5 class="card-title mb-0 custom-heading">Monthly Gross Income<?php if ($_SESSION['storeid'] !== null && $_SESSION['role'] == "Administrator") { echo "(per store)"; } ?></h5>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <p class="card-text">View</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="card-text text-right" id="totalSales">Ksh </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-md-6">
                    <a href="sales" class="no-link-style">
                        <div class="card card-custom">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-sitemap fa-2x mr-3"></i>
                                    <h5 class="card-title mb-0 custom-heading">Merchandise Value<?php if ($_SESSION['storeid'] !== null && $_SESSION['role'] == "Administrator") { echo "(per store)"; } ?></h5>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <p class="card-text">View</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="card-text text-right" id="totalMerchandise">Ksh </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-md-6">
                    <?php if ($_SESSION['storeid'] !== null) { ?>
                        <a href="sales" class="no-link-style">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-handshake fa-2x mr-3"></i>
                                        <h5 class="card-title mb-0 custom-heading">Monthly sold units</h5>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <p class="card-text">View</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="card-text text-right" id="totalQuantity">units</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php } else{?>
                        <a href="registration" class="no-link-style">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-users nav-icon fa-2x mr-3"></i>
                                        <h5 class="card-title mb-0 custom-heading">Employees</h5>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <p class="card-text">View</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="card-text text-right" id="totalUsers">persons</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-md-6">
                    <?php if ($_SESSION['storeid'] !== null) { ?>
                        <a href="products" class="no-link-style">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-cart-shopping fa-2x mr-3"></i>
                                        <h5 class="card-title mb-0 custom-heading">Products</h5>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <p class="card-text">View</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="card-text text-right" id="totalProducts"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php } else{?>
                        <a href="manage-stores" class="no-link-style">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-warehouse fa-2x mr-3"></i>
                                        <h5 class="card-title mb-0 custom-heading">Stores</h5>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <p class="card-text">View</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="card-text text-right" id="totalStores"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
                <!-- ./col -->
                <div class="col-md-12">
                  <?php
                    include 'finance-graphs/revenue-expense.php';
                  ?>
                </div>
                <div class="col-md-6 col-xs-12">
                  <?php
                    if (isset($_SESSION['storeid'])) {
                        include 'reports/top-products.php';
                    }else {
                        include 'reports/top-store.php';
                    }
                  ?>
                </div>
                <div class="col-md-6 col-xs-12">
                  <?php
                    if (isset($_SESSION['storeid'])) {
                        include 'home/recents.php';
                    }else {
                        include 'home/recentusers.php';
                    }
                  ?>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 