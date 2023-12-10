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
                            <a href="products" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>View Products</p>
                            </a>
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
                            <a href="transactions" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Transactions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="invoices" class="nav-link">
                                <i class="fa-solid fa-circle nav-icon"></i>
                                <p>Client Invoices</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="customers" class="nav-link">
                        <i class="fa-solid fa-users"></i>
                        <p>
                            Customers
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

