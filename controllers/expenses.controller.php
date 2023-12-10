<?php
class expenseController{

	/*=============================================
   SET THE STORE ID
   =============================================*/
	
    static private $storeid;

	public static function initialize() {
		if ($_SESSION['storeid'] != null) {
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
    ADD EXPENSE
    =============================================*/

    static public function ctrAddExpense(){
        self::initialize();

        if (isset($_POST['addExpense'])) {
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
                        
            $table = "expenses";
            $targetDirectory = "views/expenses/reciepts/";
    
            $expenseName = $_POST["expense"];
            $date = $_POST["date"];
            $fileExtension = pathinfo($_FILES['reciept']['name'], PATHINFO_EXTENSION);
            $fileName = $targetDirectory . $expenseName . '' . $date . '' . time() . '.' . $fileExtension;
				
            $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number
            $timezone = new DateTimeZone("Africa/Nairobi"); // Replace "Your_Timezone" with the desired timezone identifier, such as "America/New_York"
            $current_time = new DateTime("now", $timezone); // Get the current time in the specified timezone
            $current_time_formatted = $current_time->format("His"); // Format the current time in hours, minutes, and seconds
            $expenseid = "EXP-" .$randomNumber . "-" . $current_time_formatted;
    
            $data = array("expenseID" => $expenseid,
                "expense" => $expenseName,
                "expense_type" => $_POST["expenseType"],
                "date" => $date,
                "amount" => $_POST["amount"],
                "receipt" => $fileName,
                "storeid" => self::$storeid
            );
    
            // Check if a file was uploaded
            if (!empty($_FILES['reciept']['tmp_name'])) {
                // Move the uploaded file to the destination path
                if (move_uploaded_file($_FILES['reciept']['tmp_name'], $fileName)) {
                    // File upload successful
                    $answer = ExpenseModel::mdlAddExpense($table, $data);
    
                    if ($answer == "ok") {
                        if ($_SESSION['userId'] != 404) {
                            // Create an array with the data for the activity log entry
                            $logdata = array(
                                'UserID' => $_SESSION['userId'],
                                'ActivityType' => 'Expense',
                                'ActivityDescription' => 'User ' . $_SESSION['username'] . ' added expense ' .$data['expense']. '.',
                                'storeid' => self::$storeid,
                                'TimeStamp' => $dateTimeStr
                            );
                            // Call the ctrCreateActivityLog() function
                            activitylogController::ctrCreateActivityLog($logdata);
                        }

                        echo '<script>
                            Swal.fire({
                                icon: "success",
                                title: "Expense added successfully.",
                                showConfirmButton: false,
                                timer: 2000 // Auto close after 2 seconds
                              }).then(function () {
                                // Code to execute after the alert is closed
                                window.location = "expenses";
                              });
                        </script>';
                    } 

                }

            } else {
                // No file uploaded, use the default image
                $defaultImage = "views/expenses/defaults/reciept.png";
                $data["receipt"] = $defaultImage;
                $answer = ExpenseModel::mdlAddExpense($table, $data);
    
                if ($answer == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Expense added successfully.",
                            showConfirmButton: false,
                            timer: 2000 // Auto close after 2 seconds
                          }).then(function () {
                            // Code to execute after the alert is closed
                            window.location = "expenses";
                          });
                    </script>';
                } 

            }

        }

    }
    
    /*=============================================
    EDIT EXPENSE
    =============================================*/
    static public function ctrEditExpense(){
        self::initialize();

        if (isset($_POST['updateExpense'])) {
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
            
            

            $targetDirectory = "views/expenses/reciepts/";
            $table = "expenses";
            $expenseId = $_POST["editExpenseId"];
            $expenseName = $_POST["editExpense"];
            $expenseType = $_POST["editExpenseType"];
            $date = $_POST["editDate"];
            $amount = $_POST["editAmount"];
            $fileExtension = pathinfo($_FILES['editReceipt']['name'], PATHINFO_EXTENSION);
            $filename= $targetDirectory.$expenseName . '_' . $date . '_' . time() . '.' . $fileExtension;

            $existingFilePath = ExpenseModel::mdlGetExpenseFilename($table, $expenseId); // The existing file path from the database
        
                    
            $item = "id";
            $value = $expenseId;
            $oldItem = ExpenseModel::mdlShowExpenses($table, $item, $value);

            // Check if a file was uploaded
            if (!empty($_FILES['editReceipt']['tmp_name'])) {
                // Move the uploaded file to the destination path
                if (move_uploaded_file($_FILES['editReceipt']['tmp_name'], $filename)) {

                    // Update the data in the database
                    $data = array(
                        "expense" => $expenseName,
                        "expense_type" => $expenseType,
                        "date" => $date,
                        "amount" => $amount,
                        "receipt" => $filename
                    );

                    $changedInfo = ''; // Initialize the changed information string

                    foreach ($data as $property => $value) {
                        if ($property !== $expenseId && $oldItem[$property] !== $value) {
                            $changedInfo .= "Property $property changed from {$oldItem[$property]} to $value. ";
                        }
                    }
                    
                    // If any properties were changed, use the changed information as the log message
                    if (!empty($changedInfo)) {
                        $logMessage = $changedInfo;
                    } else {
                        $logMessage = "Expense has been edited."; 
                    }

                    // File upload successful
                    $answer = ExpenseModel::mdlEditExpense($table, $data, $expenseId);

                    if ($answer == "ok") {
                        if ($_SESSION['userId'] != 404) {
                            // Create an array with the data for the activity log entry
                            $logdata = array(
                                'UserID' => $_SESSION['userId'],
                                'ActivityType' => 'Expense',
                                'ActivityDescription' => $logMessage,
                                'storeid' => self::$storeid,
                                'TimeStamp' => $dateTimeStr
                            );
                            // Call the ctrCreateActivityLog() function
                            activitylogController::ctrCreateActivityLog($logdata);
                        }
                        
                        // Delete the file
                        if (file_exists($existingFilePath)) {
                            unlink($existingFilePath);
                        }

                        echo '<script>
                            Swal.fire({
                                icon: "success",
                                title: "Expense updated successfully.",
                                showConfirmButton: false,
                                timer: 2000 // Auto close after 2 seconds
                              }).then(function () {
                                // Code to execute after the alert is closed
                                window.location = "expenses";
                              });
                        </script>';
                    }

                }

            }else {

                // Update the data in the database
                $data = array(
                    "expense" => $expenseName,
                    "expense_type" => $expenseType,
                    "date" => $date,
                    "amount" => $amount,
                    "receipt" => $existingFilePath
                );


                $changedInfo = ''; // Initialize the changed information string

                foreach ($data as $property => $value) {
                    if ($property !== 'discountid' && $oldItem[$property] !== $value) {
                        $changedInfo .= "Property '$property' changed from '{$oldItem[$property]}' to '$value'. ";
                    }
                }
                
                // If any properties were changed, use the changed information as the log message
                if (!empty($changedInfo)) {
                    $logMessage =$changedInfo;
                } else {
                    $logMessage = "Expense has been edited."; 
                }

                // File upload successful
                $answer = ExpenseModel::mdlEditExpense($table, $data, $expenseId);

                if ($answer == "ok") {
                    if ($_SESSION['userId'] != 404) {
                        // Create an array with the data for the activity log entry
                        $logdata = array(
                            'UserID' => $_SESSION['userId'],
                            'ActivityType' => 'Expense',
                            'ActivityDescription' => $logMessage,
                            'itemID' => $expenseId,
                            'storeid' => self::$storeid,
                            'TimeStamp' => $dateTimeStr
                        );
                        // Call the ctrCreateActivityLog() function
                        activitylogController::ctrCreateActivityLog($logdata);
                    }

                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Expense updated successfully.",
                            showConfirmButton: false,
                            timer: 2000 // Auto close after 2 seconds
                          }).then(function () {
                            // Code to execute after the alert is closed
                            window.location = "expenses";
                          });
                    </script>';
                }
            }

        }

    }

    /*=============================================
    DELETE EXPENSE
    =============================================*/
    
    static public function ctrDeleteExpense(){
        self::initialize();

        if (isset($_GET["deleteExpenseId"])) {
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
                        
            $table = "expenses";
            $expenseId = $_GET["deleteExpenseId"];
            
            $item = "id";
            $value = $expenseId;
		    $expenses = ExpenseModel::mdlShowExpenses($table, $item, $value);

            // Get the filename of the expense
            $filename = ExpenseModel::mdlGetExpenseFilename($table, $expenseId);

            $answer = ExpenseModel::mdlDeleteExpense($table, $expenseId);

            if ($answer == "ok") {
				if ($_SESSION['userId'] != 404) {
                    // Create an array with the data for the activity log entry
                    $logdata = array(
                        'UserID' => $_SESSION['userId'],
                        'ActivityType' => 'Expense',
                        'ActivityDescription' => 'User ' . $_SESSION['username'] . ' deleted expense ' .$expenses['expense']. ' of type ' . $expenses['expense_type'] . '.',
                        'itemID' => $value,
                        'storeid' => self::$storeid,
                        'TimeStamp' => $dateTimeStr
                    );
                    // Call the ctrCreateActivityLog() function
                    activitylogController::ctrCreateActivityLog($logdata);
                }

                // Delete the file
                if (file_exists($filename)) {
                    unlink($filename);
                }

                echo json_encode(array("status" => "success"));
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "The expense has been successfully deleted.",
                        showConfirmButton: false,
                        timer: 2000 // Auto close after 2 seconds
                      }).then(function () {
                        // Code to execute after the alert is closed
                        window.location = "expenses";
                      });
                </script>';
            }

        }

    }

    /*=============================================
    SHOW EXPENSE
    =============================================*/

    static public function ctrShowExpenses($item, $value){

		$table = "expenses";

		$expenses = ExpenseModel::mdlShowExpenses($table, $item, $value);

		return $expenses;
	}
    
    public function ctrAddExpenseType() {

        if (isset($_POST['addExpenseType'])) {
            // Set the default timezone to Nairobi
            date_default_timezone_set('Africa/Nairobi');
            
            // Create a DateTime object with the current date and time in Nairobi timezone
            $dateTime = new DateTime();
            
            // Format the DateTime as a string
            $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
            
            

            $table = "expensecat";
            $expenseType = $_POST["expenseType"];

            // Validate and sanitize the input as needed

            $data = array(
                "expensetype" => $expenseType
            );

            $answer = ExpenseModel::mdlAddExpenseType($table, $data);

            if ($answer == "ok") {

				if ($_SESSION['userId'] != 404) {
                    // Create an array with the data for the activity log entry
                    $logdata = array(
                        'UserID' => $_SESSION['userId'],
                        'ActivityType' => 'Expense',
                        'ActivityDescription' => 'User ' . $_SESSION['username'] . ' added expense type ' .$expenseType. '.',
                        'TimeStamp' => $dateTimeStr
                    );
                    // Call the ctrCreateActivityLog() function
                    activitylogController::ctrCreateActivityLog($logdata);
                }

                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Expense type added successfully.",
                        showConfirmButton: false,
                        timer: 2000 // Auto close after 2 seconds
                      }).then(function () {
                        // Code to execute after the alert is closed
                        window.location = "expenses";
                      });
                </script>';
            } else {
                echo '<div class="alert alert-danger">Error adding the expense type. Please try again.</div>';
            }

        }

    }

}


