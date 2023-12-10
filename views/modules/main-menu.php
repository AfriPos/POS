<style>
  .link  {
    text-decoration: none;
  }
  .brand-text:hover {
    color: black;
  }
  .brand-text {
    color: black;
  }
  
</style>
  <?php
  
  $table = "customers";
  $item = "organizationcode";
  $value = $_SESSION['organizationcode'];
  $conn = connection::connectbilling()->prepare("SELECT * FROM `$table` WHERE $item = :$item");
  
  $conn->bindParam(':' . $item, $value, PDO::PARAM_STR);
  
  $conn->execute();
  
  $organizationData = $conn->fetch(PDO::FETCH_ASSOC); // Fetch the result into an associative array

  $conn -> closeCursor();

  $conn = null;
  ?>
<aside class="main-sidebar custom-sidebar">
    <a href="#" class="brand-link link">
        <img src=<?php echo $organizationData['organizationlogo'] ?> class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light" ><?php echo $organizationData['organizationname'] ?></span>
    </a>

    <div class="sidebar">
    <?php
        $item = "store_id";
        $value = $_SESSION['storeid'];
        $store = storeController::ctrShowStores($item, $value);
        ?>

        <?php if ($_SESSION['storeid'] !== null) { ?>
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo $store[0]['logo']; ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <div class="d-block link"><?php echo $store[0]['store_name']; ?></div>
            </div>
        </div>
    <?php } ?>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                    with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="dashboard" class="nav-link">
                        <i class="fa-solid fa-house-user nav-icon"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pos" class="nav-link">
                        <i class="fa-solid fa-store nav-icon"></i>
                        <p>
                            POS
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <p>
                            Inventory
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="category" class="nav-link">
                                <i class="fa-solid fa-sitemap nav-icon"></i>
                                <p>
                                    Category
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fa-solid fa-cart-shopping nav-icon"></i>
                                <p>
                                    Products
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="addproduct" class="nav-link">
                                        <i class="fa-solid fa-circle nav-icon"></i>
                                        <p>Add Products</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="products" class="nav-link">
                                        <i class="fa-solid fa-circle nav-icon"></i>
                                        <p>View Products</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="stock" class="nav-link">
                                        <i class="fa-solid fa-circle nav-icon"></i>
                                        <p>Add stock</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="discount" class="nav-link">
                                        <i class="fa-solid fa-circle nav-icon"></i>
                                        <p>Discounts</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fa-solid fa-boxes-packing"></i>
                                <p>
                                    Supply
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="suppliers" class="nav-link">
                                        <i class="fa-solid fa-circle nav-icon"></i>
                                        <p>Supliers</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="fa-solid fa-circle nav-icon"></i>
                                        <p>Supplier Invoices</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="fa-solid fa-boxes-packing"></i>
                                        <p>
                                        Orders
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="orders" class="nav-link">
                                                <i class="fa-solid fa-circle nav-icon"></i>
                                                <p>Make order</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="vieworders" class="nav-link">
                                                <i class="fa-solid fa-circle nav-icon"></i>
                                                <p>View orders</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fa-solid fa-right-left"></i>
                                <p>
                                    Return Products
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="returns" class="nav-link">
                                        <i class="fa-solid fa-circle nav-icon"></i>
                                        <p>Make return</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="view-returned" class="nav-link">
                                        <i class="fa-solid fa-circle nav-icon"></i>
                                        <p>View returned</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-arrow-trend-up nav-icon"></i>
                        <p>
                            Finance
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="finance-dashboard" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="transactions" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Transactions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="tax" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Tax</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="invoices" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Client Invoices</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="expenses" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Expenses</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-brands fa-sellsy"></i>
                        <p>Sales
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="sales" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Sales Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="sales-reports" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Manage Sales</p>
                            </a>
                        </li>
                    </ul>
                </li> -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-warehouse"></i>
                        <p>Stores
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="stores" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Add stores</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="manage-stores" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Manage stores</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="reports" class="nav-link">
                        <i class="fa-solid fa-chart-line nav-icon"></i>
                        <p>
                            Reports
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="customers" class="nav-link">
                        <i class="fa-solid fa-users"></i>
                        <p>
                            Customers
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="registration" class="nav-link">
                        <i class="fa-solid fa-user-tie nav-icon"></i>
                        <p>
                            Employees
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
