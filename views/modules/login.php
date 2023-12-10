 <style>
  .link  {
  text-decoration: none;
  }
  </style>
<!-- <body class="hold-transition login-page">
  <div class="login-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <p><b>Inventory</b>SYSTEM</p>
      </div>
      <div class="card-body">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Login to start your session</p>
          <form action="" method="post">
            <div class="input-group mb-3">
              <input type="email" class="form-control" placeholder="Enter Email" name="txt_user" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" placeholder="Enter Password" name="txt_password" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block" name="btn_login">Login</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body> -->


	
<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" action="" method="post">
          
      <div class="card-header text-center">
        <p class="login100-form-title p-b-43"><b>Inventory</b>SYSTEM</p>
      </div>
					<span class="login100-form-title p-b-43">
						Login to start session
					</span>
					
					
					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="email" name="txt_user">
						<span class="focus-input100"></span>
						<span class="label-input100">Email</span>
					</div>
					
					
					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<input class="input100" type="password" name="txt_password">
						<span class="focus-input100"></span>
						<span class="label-input100">Password</span>
					</div>
					

					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn" name="btn_login">
							Login
						</button>
					</div>

				</form>

				<div class="login100-more" style="background-image: url('views/img/login/login-image.jpg');">
          <h1 class="logo">Afri POS</h1>
          <p class="on-image">Improving Retail Business one shop at a Time.</p>
          <p class="copyright on-image">All Rights Reserved &copy; 2023 <a href="https://afripos.co.ke" target="_blank" class="link">AfriPOS</a></p>
				</div>
			</div>
		</div>
	</div>


<?php
  $login= new userController();
  $login->ctrUserLogin();
?>