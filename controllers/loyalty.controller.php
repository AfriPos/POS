<?php
    class loyaltyController{
        /*=============================================
        SHOW LOYALTY VALUE
        =============================================*/
    
        static public function ctrShowLoyaltyValue(){
    
            $table = "loyaltysettings";
    
            $answer = LoyaltyModel::mdlShowLoyaltyValue($table);
    
            return $answer;
        }

        /*=============================================
        ADD LOYALTY POINTs
        =============================================*/
    
        static public function ctrAddLoyaltyPoints($data){
    
            $table = "loyalty-points";
    
            $answer = LoyaltyModel::mdlAddLoyaltyPoints($table, $data);
    
            return $answer;
        }


        /*=============================================
        SHOW LOYALTY POINTs
        =============================================*/

        static public function ctrShowLoyaltyPoints($item,$value, $fetchAll=false){

            $table = "loyalty-points";

            if ($fetchAll) {

                $answer = LoyaltyModel::mdlShowAllLoyaltyPoints($table, $item, $value);

            } else {
    
                $answer = LoyaltyModel::mdlShowLoyaltyPoints($table, $item, $value);
                
            }
    
            return $answer;
        }


        /*=============================================
        SHOW LOYALTY POINT CONVERSION VALUE
        =============================================*/
        static public function ctrShowLoyaltyPointConversionValue($item, $value){

            $table = "loyaltysettings";
    
            $answer = LoyaltyModel::mdlShowLoyaltyPointConversionValue($table, $item, $value);
    
            return $answer;

        }

        /*=============================================
        CHANGE LOYALTY SETTINGS
        =============================================*/
        static public function ctrchangeLoyaltySettings(){

            if (isset($_POST['saveSetting'])) {
                // Set the default timezone to Nairobi
                date_default_timezone_set('Africa/Nairobi');
                
                // Create a DateTime object with the current date and time in Nairobi timezone
                $dateTime = new DateTime();
                
                // Format the DateTime as a string
                $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
                                
                $table = "loyaltysettings";
                $data = array(
                    "LoyaltyPointValue" => $_POST['loyaltyPointValue'],
                    "LoyaltyValueConversion" => $_POST['loyaltyValueConversion']
                );
                
                $answer = LoyaltyModel::mdlchangeLoyaltySettings($table, $data);

                if($answer == 'ok'){
                    if ($_SESSION['userId'] != 404) {
                        // Create an array with the data for the activity log entry
                        $logdata = array(
                            'UserID' => $_SESSION['userId'],
                            'ActivityType' => 'Settings',
                            'ActivityDescription' => 'User ' . $_SESSION['username'] . ' changed loyalty settings.',
                            'TimeStamp' => $dateTimeStr
                        );
                        // Call the ctrCreateActivityLog() function
                        activitylogController::ctrCreateActivityLog($logdata);
                    }
    
                    echo '<script>
                        
                    Swal.fire({
                            icon: "success",
                            title: "Settings updated successfully",
                            showConfirmButton: false,
                            timer: 2000 // Auto close after 2 seconds
                          })
                        
                    </script>';
                }

            }
        }

    }