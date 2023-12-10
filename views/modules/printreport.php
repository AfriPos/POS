<?php

session_start();
require_once "../../controllers/payments.controller.php";
require_once "../../models/payment.model.php";
require_once "../../controllers/user.controller.php";
require_once "../../models/user.models.php";

$report = new PaymentController();
$report -> ctrDownloadReport();