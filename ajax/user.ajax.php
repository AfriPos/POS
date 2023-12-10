<?php

session_start();
require_once "../controllers/user.controller.php";
require_once "../models/user.models.php";

class AjaxUsers{

	/*=============================================
	EDIT USER
	=============================================*/

	public $userId;
	public $activateUser;
	public $activateId;
	public $validateUser;
	public $validateEmail;

	public function ajaxEditUser(){

		$item = "userId";
		$value = $this->userId;

		$answer = userController::ctrShowUsers($item, $value);

		echo json_encode($answer);
	}


	/*=============================================
	ACTIVATE USER
	=============================================*/


	public function ajaxActivateUser(){

		$table = "users";
		$item1 = "status";
		$value1 = $this->activateUser;

		$item2 = "userId";
		$value2 = $this->activateId;

		$answer = userModel::mdlUpdateUser($table, $item1, $value1, $item2, $value2);


	}


	/*=============================================
	VALIDATE IF USER ALREADY EXISTS
	=============================================*/


	public function ajaxValidateUser(){

		$item = "username";
		$value = $this->validateUser;

		$answer = userController::ctrShowUsers($item, $value);

		echo json_encode($answer);

	}

	/*=============================================
	VALIDATE IF EMAIL ALREADY EXISTS
	=============================================*/


	public function ajaxValidateEmail(){

		$item = "email";
		$value = $this->validateEmail;

		$answer = userController::ctrShowUsers($item, $value);

		echo json_encode($answer);

	}

	public $oldpassword;
	public $userid;

	public function ajaxValidatePassword(){

		$item = "userId";
		$value = $this->userid;

		$answer = userController::ctrShowUsers($item, $value);

		$nonEncryptedPassword = $this->oldpassword;
		$storedEncryptedPassword = $answer['userpassword']; // Replace this with the actual stored encrypted password from the database

		// Use the crypt() function to encrypt the non-encrypted password for comparison
		$encryptedPassword = crypt($nonEncryptedPassword, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

		// echo json_encode($answer);
		if ($encryptedPassword === $storedEncryptedPassword) {
			echo json_encode(true);
		} else {
			echo json_encode(false);
		}

	}

}


/*=============================================
EDIT USER
=============================================*/

if (isset($_POST["userId"])) {

	$edit = new AjaxUsers();
	$edit -> userId = $_POST["userId"];
	$edit -> ajaxEditUser();
}

/*=============================================
VALIDATE OLD PASSWORD
=============================================*/

if (isset($_POST["oldpassword"]) && isset($_POST["user-id"])) {

	$validate = new AjaxUsers();
	$validate -> oldpassword = $_POST["oldpassword"];
	$validate -> userid = $_POST["user-id"];
	$validate -> ajaxValidatePassword();
}

/*=============================================
ACTIVATE USER
=============================================*/

if (isset($_POST["activateUser"])) {

	$activateUser = new AjaxUsers();
	$activateUser -> activateUser = $_POST["activateUser"];
	$activateUser -> activateId = $_POST["activateId"];
	$activateUser -> ajaxActivateUser();
}


/*=============================================
VALIDATE IF USER ALREADY EXISTS
=============================================*/


if (isset($_POST["validateUser"])) {

	$valUser = new AjaxUsers();
	$valUser -> validateUser = $_POST["validateUser"];
	$valUser -> ajaxValidateUser();

}


if (isset($_POST["validateEmail"])) {

	$valEmail = new AjaxUsers();
	$valEmail -> validateEmail = $_POST["validateEmail"];
	$valEmail -> ajaxValidateEmail();

}