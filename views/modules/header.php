<style>
  /* Custom CSS for the profile image in the dropdown menu */
  .user-profile-img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
  }
  /* Add any shaking animation to the .shaking-bell class */
@keyframes shake {
  0% { transform: rotate(0deg); }
  10% { transform: rotate(-5deg); }
  20% { transform: rotate(5deg); }
  30% { transform: rotate(-5deg); }
  40% { transform: rotate(5deg); }
  50% { transform: rotate(5deg); }
  60% { transform: rotate(-5deg); }
  70% { transform: rotate(5deg); }
  80% { transform: rotate(-5deg); }
  90% { transform: rotate(5deg); }
  100% { transform: rotate(0deg); }
}

.shaking-bell {
  transform-origin: top center;
  animation: shake 0.5s infinite; /* Adjust animation duration and timing as needed */
}

</style>
   
   
<?php
  // Set the default timezone to Nairobi
  date_default_timezone_set('Africa/Nairobi');

  // Create a DateTime object with the current date and time in Nairobi timezone
  $dateTime = new DateTime();

  // Format the DateTime as a string
  $dateTimeStr = $dateTime->format('Y-m-d H:i:s');

  // check if button is pressed
  $username=$_SESSION['username'];
  $pdo=connection::connect();
  if(isset($_POST['btnupdate'])){
    $newpassword=crypt($_POST["newpass"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
    $update=$pdo->prepare("UPDATE users set userpassword=:pass where username=:username");
    $update->bindParam(':pass',$newpassword,PDO::PARAM_STR);
    $update->bindParam(':username',$username,PDO::PARAM_STR);
    if($update->execute()){
      if ($_SESSION['userId'] != 404) {
        // Create an array with the data for the activity log entry
        $logdata = array(
            'UserID' => $_SESSION['userId'],
            'ActivityType' => 'Password Change',
            'ActivityDescription' => 'User ' . $_SESSION['username'] . ' changed password.',
            'TimeStamp' => $dateTimeStr
        );
        // Call the ctrCreateActivityLog() function
        activitylogController::ctrCreateActivityLog($logdata);
      }

      $_SESSION['status']="Password changed";
      $_SESSION['status_code']="success";
      session_destroy();
      echo'
      <script>
          window.location.reload();
      </script>
      ';
    }
  }
 ?>
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <!-- Left navbar links -->
      <li class="nav-item d-none d-sm-inline-block">
        <!-- <a href="index3.html" class="nav-link">Home</a> -->
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <!-- <a href="#" class="nav-link">Contact</a>-->
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <!-- Right navbar links -->
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item">
        <a href="#" class="btn position-relative nav-link" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
          <i class="fa fa-bell" id="bellIcon"></i>
          <span class="badge bg-warning rounded-pill position-absolute top-0 start-100 translate-middle"  id="rowCountSpan"></span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
          <img src="<?php echo $_SESSION['userphoto']; ?>" class="user-profile-img" alt="User Image" ><?php echo $_SESSION['username']; ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <?php
            echo '<a href="#" class="dropdown-item view-profile-link" userid="'.$_SESSION['userId'].'">Profile</a>';
          ?>
          <?php
            if ($_SESSION['role'] == "Administrator" || $_SESSION['role'] == 404) {
              echo '<a href="logs" class="dropdown-item">Logs</a>';
              echo '<a href="#" class="dropdown-item settings" data-toggle="modal" data-target="#settingsModal">Settings</a>';
              echo '<a href="#" class="dropdown-item" data-toggle="modal" data-target="#switchStoreModal">Switch Store</a>';
            }
            if ($_SESSION['storeid'] !== null && $_SESSION['role'] == "Administrator" || $_SESSION['storeid'] !== null && $_SESSION['role'] == 404) {
              echo '<a href="#" id="exit_store" value="'.$_SESSION['storeid'].'" class="dropdown-item">Exit store</a>';
            }
          ?>
          <div class="dropdown-divider"></div>
          <a href="logout" class="dropdown-item">Logout</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

<!-- Add the modal -->
<div class="modal fade" id="switchStoreModal" tabindex="-1" role="dialog" aria-labelledby="switchStoreModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="switchStoreModalLabel">Switch Store</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php
          $item = null;
          $value = null;
          $stores = storeController::ctrShowStores($item, $value);
          // Assuming the $stores array contains the store names and IDs from the database
          if (!empty($stores)) {
            foreach ($stores as $store) {
              $storeName = $store['store_name'];
              $storeID = $store['store_id'];
              // echo '<form action="ajax/session.ajax.php" method="post">';
              echo '<button class="btn switch-store-btn" id="store_id" value="' . $storeID . '">' . $storeName . '</button>';
              // echo '</form>';
            }
          } else {
            echo '<p>No stores found.</p>';
          }
        ?>
      </div>
    </div>
  </div>
</div>

<!-- Add the modal inside the body tag -->
<div class="modal fade" id="userProfileModal" tabindex="-1" role="dialog" aria-labelledby="userProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userProfileModalLabel">User Profile</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modalBodyContent">
          <!-- Display user profile information here -->
          <img id="profilePicture" src="#" class="img-fluid rounded-circle mx-auto d-block" style="width: 100px; cursor: pointer;" alt="User Profile Picture">
          <h4 id="userName" class="text-center"></h4>
          <p id="userEmail" class="text-center"></p>
          <p id="userRole" class="text-center"></p>
          <?php if ($_SESSION['storeid'] !== null && $_SESSION['role'] !== "Administrator") { ?>
            <p id="userStore" class="text-center"></p>
          <?php } ?>
        </div>

        <!-- Content for password change -->
        <div class="passwordChangeContent" style="display: none;">
          <!-- Add the form or content for changing password here -->
          <div class="col-lg-12">
            <!-- Horizontal Form -->
            <!-- /.card-header -->
            <!-- form start -->
            <form class="form-horizontal" id="passwordChangeForm" action="" method="post">
              <div class="card-body">
                <div class="form-floating mb-3">
                  <input type="password" class="form-control oldpass" name="oldpass">
                  <input type="hidden" class="userId" value="<?php echo $_SESSION['userId'] ?>">
                  <label for="floatingInput">Old Password</label>
                </div>
                <div class="form-floating mb-3">
                  <input type="password" class="form-control newpass" name="newpass">
                  <label for="floatingInput">New Password</label>
                </div>
                <div class="progress">
                  <div id="passwordStrengthMeter" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div id="passwordStrengthText"></div>
                <div class="form-floating mb-3">
                  <input type="password" class="form-control rnewpass" name="rnewpass">
                  <label for="floatingInput">Repeat New Password</label>
                </div>
                
              </div>
              <div class="alert alert-danger" id="passwordMismatchWarning" role="alert" style="display: none;">Passwords do not match!</div>
              <div class="alert alert-danger" id="oldpasswordMismatchWarning" role="alert" style="display: none;">Old Passwords is incorrect!</div>
              <div class="alert alert-danger" id="emptyFieldWarning" role="alert" style="display: none;">Empty field(s) found!</div>
              <!-- /.card-body -->
              <!-- /.card -->
              <div class="modal-footer justify-content-between updatePasswordFooter2" style="display: none;">
                <button type="button" class="btn btn-secondary closeBtn" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" name="btnupdate">Update password</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between updatePasswordFooter1">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary changePasswordButton">Change password</button>
      </div>
    </div>
  </div>
</div>


<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modalBodyContent">
          <img id="companyLogo" src="views/img/products/default/anonymous.png" class="img-fluid rounded-circle mx-auto d-block" style="width: 100px; cursor: pointer;">
        </div>
        <div class="form-group">

          <label class="" for="">Loyalty Platform</label>
          
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="loyaltyPoints">
            <label class="form-check-label" for="loyaltyPoints">Activate Loyalty Points</label>
          </div>
          
          <!-- <div class="my-4"></div> -->
          <label class="" for="">Customer Details</label>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="fetchdetails">
            <label class="form-check-label" for="fetchdetails">Fetch Customer Details</label>
          </div>
          
        </div>
        <form action="" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label for="loyaltyValueConversion">Loyalty Value Conversion</label>
            <input type="number" class="form-control" id="loyaltyValueConversion" name="loyaltyValueConversion">
          </div>
          <div class="form-group">
            <label for="loyaltyPointValue">Loyalty Point Value</label>
            <input type="number" class="form-control" id="loyaltyPointValue" name="loyaltyPointValue">
          </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" name="saveSetting">Save Changes</button>
      </div>
      <?php

        $editSettings = new loyaltyController();
        $editSettings -> ctrchangeLoyaltySettings();

      ?>  
        </form>
    </div>
  </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="rowCountSpanHeader">Notifications</h5>
    <!-- <div class="offcanvas-title dropdown-header" id="rowCountSpanHeader"></div> -->
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body notificationItems"></div>
</div>

<?php
if (isset($_SESSION['status'])&& $_SESSION['status']!=''){
  ?>
<script>
  Swal.fire({
    icon: '<?php echo $_SESSION['status_code'] ?>',
    title: '<?php echo $_SESSION['status'] ?>',
    showConfirmButton: false,
    timer: 2000 // Close the alert after 2 seconds
    })
</script>


<?php
unset($_SESSION['status']);
}

?>