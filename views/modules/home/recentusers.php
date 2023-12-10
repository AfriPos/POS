<?php
$pdo = connection::connect();
$usersQuery = $pdo->prepare("SELECT * FROM users WHERE date >= CURDATE() - INTERVAL 2 WEEK AND role != 'Administrator' AND  role != 404 ORDER BY userId DESC LIMIT 5");
$usersQuery->execute();
$usersData = $usersQuery->fetchAll(PDO::FETCH_ASSOC);
?>
 <style>
  .link  {
  text-decoration: none;
  }
</style>
<!-- RECENTLY ADDED USERS LIST -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Recently Added Users</h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body p-0">
    <ul class="products-list product-list-in-card pl-2 pr-2">
        <?php
        if ($usersData) {
          foreach ($usersData as $user) {
            echo '
            <li class="item">
                <div class="product-img">
                    <img src="' . $user['userphoto'] . '" class="img-size-50">
                </div>
                <div class="product-info">
                    <a href="#" class="product-title view-profile-link link" userid="' . $user['userId'] . '">
                        ' . $user['name'] . '
                        <span class="badge badge-warning float-right">' . $user['email'] . '</span>
                    </a>
                    <span class="product-description">
                        Role: ' . $user['role'] . '
                    </span>
                </div>
            </li>';
          }
        } else {
          echo '
          <li class="item text-center">
            <p>No data to display</p>
          </li>';
        }
        

        ?>
    </ul>
</div>

  <!-- /.card-body -->
  <div class="card-footer text-center">
    <a href="users" class="uppercase link">View All Users</a>
  </div>
  <!-- /.card-footer -->
</div>
<!-- /.card -->

<!-- Add the modal inside the body tag -->
<div class="modal fade" id="userProfileModal" tabindex="-1" role="dialog" aria-labelledby="userProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userProfileModalLabel">User Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Display user profile information here -->
        <img id="profilePicture" src="#" class="img-fluid rounded-circle mx-auto d-block" style="width: 100px;" alt="User Profile Picture">
        <h4 id="userName" class="text-center"></h4>
        <p id="userEmail" class="text-center"></p>
        <p id="userRole" class="text-center"></p>
        <?php if ($_SESSION['storeid'] !== null) { ?>
          <p id="userStore" class="text-center"></p>
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>

</script>
