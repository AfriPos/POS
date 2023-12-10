<?php
class reportsController{
    

	/*=============================================
   SET THE STORE ID
   =============================================*/
	
   static private $storeid;

   public static function initialize() {
       // Start a session if it hasn't already been started
       if (session_status() == PHP_SESSION_NONE) {
           session_start();
       }
   
       if (isset($_SESSION['storeid']) && $_SESSION['storeid'] != null) {
           self::$storeid = $_SESSION['storeid'];
       } else {
           echo "<script>
               window.onload = function() {
                   Swal.fire({
                       title: 'No store is selected',
                       text: 'Redirecting to Dashboard',
                       icon: 'error',
                       showConfirmButton: false,
                       timer: 2000 // Display alert for 2 seconds
                   }).then(function() {
                       // After the alert is closed, redirect to the dashboard
                       window.location= 'dashboard';
                   });
               };
               </script>";
           exit; // Adding exit to stop further execution after the redirection
       }
   }

    /*=============================================
	SHOW A GENERAL REPORT
	=============================================*/

	static public function ctrShowGeneralreport(){
        
        self::initialize();

		$table = "sales";

        $storeid = self::$storeid;

		$answer = reportsModel::mdlShowGeneralreport($table, $storeid);

		return $answer;
	}

    /*=============================================
	SHOW A DETAILED REPORT
	=============================================*/

    static public function ctrShowDetailedreport($parameters) {
        self::initialize();
    
        $table = "sales";
        $storeid = self::$storeid;
    
        $answer = reportsModel::mdlShowDetailedreport($table, $storeid, $parameters);
    
        return $answer;
    }
    

}