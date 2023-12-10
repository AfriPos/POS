<?php

class activitylogController {

    /*=============================================
    CREATE ACTIVITYLOG
    =============================================*/
    public static function ctrCreateActivityLog($data){
        if ($data) {

            $table = "useractivitylog";

            $answer = activitylogModel::mdlCreateActivityLog($table, $data);

            return $answer;
            
        }
    }
    
    /*=============================================
    FETCH ACTIVITYLOG
    =============================================*/
    public static function ctrFetchActivityLog($item, $value){
        
        $table = "useractivitylog";

        $answer = activitylogModel::mdlFetchActivityLog($table, $item, $value);

        return $answer;

    }
}
?>
