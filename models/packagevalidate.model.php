<?php
require_once 'connection.php';

class packagevalidateModel{
    /*=============================================
	FETCH PACKAGE DETAILS
	=============================================*/
	static public function mdlfetchPackageDetails($table, $item, $value){

        $stmt = connection::connectbilling()->prepare("SELECT * FROM $table WHERE $item = :value");

        $stmt -> bindParam(":value", $value, PDO::PARAM_INT);

        $stmt -> execute();

        return $stmt -> fetch();
    
        $stmt -> closeCursor();

        $stmt = null;

    }
    /*=============================================
	VALIDATE PRODUCT
	=============================================*/
	static public function mdlfetchCustomerDetails($table, $organizationcode){

        $stmt = connection::connectbilling()->prepare("SELECT * FROM $table WHERE organizationcode = :organizationcode");

        $stmt -> bindParam(":organizationcode", $organizationcode, PDO::PARAM_STR);

        $stmt -> execute();

        return $stmt -> fetchAll();
    
        $stmt -> closeCursor();

        $stmt = null;

    }

}