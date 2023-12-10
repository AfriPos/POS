<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">POS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src=<?php echo $_SESSION['userphoto'];?> class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['username'];?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library --> 
          <li class="nav-item">
            <a href="dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
               
              </p>
            </a>
          </li>
        <li class="nav-item">
            <a href="category" class="nav-link">
              <i class="nav-icon fas fa-table"></i>
              <p>
                Category
                
              </p>
            </a>
          </li>
          <li class="nav nav-item">
                <a href="" class="nav-link"><i class="nav-icon fas fa-chart-pie"></i>
                  <p>
                    Products
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="addproduct" class="nav-link">
                            <i class="fas fa-edit"></i>
                            <p>Add Products</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="products" class="nav-link">
                            <i class="fas fa-circle-o"></i>
                            <p>View Products</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="discount" class="nav-link">
                            <i class="fas fa-circle-o"></i>
                            <p>Add discount</p>
                        </a>
                    </li>
                </ul>
          </li>
          <li class="nav-item">
            <a href="pos" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                POS
                
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="invoices" class="nav-link">
              <i class="nav-icon fas fa-list"></i>
              <p>
                invoices
                
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="sales" class="nav-link">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>
                Sales Report
                
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="tax" class="nav-link">
              <i class="nav-icon fas fa-calculator"></i>
              <p>
                Tax
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="registration" class="nav-link">
              <i class="nav-icon fas fa-plus-square"></i>
              <p>
                Registration
               
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="changepassword" class="nav-link">
              <i class="nav-icon fas fa-user-lock"></i>
              <p>
                Change password
                
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="logout" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>
                Log out
               
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
