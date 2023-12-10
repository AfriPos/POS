 <style>
        /* Center the button in the middle of the content-wrapper div */
        .renew {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Style the button */
        .centered-button {
            background-color: #007bff;
            color: #fff;
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            cursor: pointer;
        }

    </style>
    <!-- Contains page content -->
    <div class="renew">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                
                <div class="alert alert-danger transaction-failed" role="alert" style="display: none;">
                    Transaction Failed, please try again. If you think this is an error please contact us at info@afripos.co.ke
                </div>

                <div class="alert alert-success transaction-success" role="alert" style="display: none;">
                    Transaction Successful, page will reload.
                </div>

                <div class="alert alert-success check-phone" role="alert" style="display: none;">
                    Please check your phone, a payment request has been sent.
                </div>

                <div class="row mb-2">
                    <h1 class="m-0">Your service is due make payment to access the site</h1>
                </div><!-- /.row -->
                <div class="row mb-2">
                    <form method="post" id="renewalForm">
                        <!-- Loading spinner div -->
                        <div class="loading-spinner"></div>

                        <!-- Centered Button -->
                        <div class="text-center">
                            <button name="payment" type="submit" class="btn btn-primary centered-button payment" organizationcode="<?php echo $_SESSION['organizationcode'] ?>">Pay</button>
                        </div>
                    </form>
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
    </div>
    <!-- /.content-->

