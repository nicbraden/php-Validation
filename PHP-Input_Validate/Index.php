<?php
/*Author: Nicole Braden
 * Purpose: Simple PHP Web Application with Form Validation
 * 
 */

session_start();

require_once("DB.php");

$firstN = "";
$lastN = "";
$phone = array();
$phoneNum = "";
$email = array(); 
$emailAdd = ""; 

//get contact info 
if (isset($_SESSION['firstN'])) {
    $firstN = $_SESSION['firstN'];
} else if (isset($_POST['firstN'])) {
    $firstN = $_POST['firstN'];
}

if (isset($_SESSION['lastN'])) {
    $lastN = $_SESSION['lastN'];
} else if (isset($_POST['lastN'])) {
    $lastN = $_POST['lastN'];
}

if (isset($_SESSION['emailAdd'])) {
    $emailAdd = $_SESSION['emailAdd'];
} else if (isset($_POST['emailAdd'])) {
    $emailAdd = $_POST['emailAdd'];
}

if (isset($_SESSION['phoneNum'])) {
    $phoneNum = $_SESSION['phoneNum'];
} else if (isset($_POST['phoneNum'])) {
    $phoneNum = $_POST['phoneNum'];
}

if (isset($_SESSION['email'])){
    $email = $_SESSION['email'];
} else if (!empty($_POST['email'])){
    $email = array();
    foreach($_POST ['email'] as $email){
        array_push($email, $email);
    }
}  

if (isset($_SESSION['phone'])){
    $phone = $_SESSION['phone'];
} else if (!empty($_POST['phone'])){
    $phone = array();
    foreach($_POST ['phone'] as $phone){
        array_push($email, $email);
    }
}

?>
<html>
	<head>
		<meta charset="utf-8"></meta>
		<title>Input,Validate</title>
	</head>
	<body>
	<h1>Contact Information</h1>

        <form method="POST">
         <!-- information form -->
            <div>
                <p>Please enter the following information</p>
                	<label for="firstName">First Name</label>
            		<input type="text" size="50" maxlength="50" id="firstN" name="firstN" value="<?php echo $firstN; ?>">
            		 </br></br>
            		
            		<label for="lastName">Last Name</label>
            		<input type="text" size="50" maxlength="50" id="lastN" name="lastN" value="<?php echo $lastN; ?>">
                	 </br></br>
                	 
                    <input type="checkbox" name="phone[]" <?php if(in_array(0, $phone, false)){echo "checked=".'"'."checked".'"';} ?> value="checked">Phone Number</input><br>
                    <label for="phoneNum">Phone Number</label>
            		<input type="text" size="50" maxlength="20" id="phoneNum" name="phoneNum" value="<?php echo $phoneNum; ?>">
            		 </br></br>
            		 
                    <input type="checkbox" name="email[]" <?php if(in_array(0, $email, false)){echo "checked=".'"'."checked".'"';} ?> value="checked">Email Address</input><br>
                    <label for="emailAdd">Email Address</label>
            		<input type="text" size="50" maxlength="128" id="emailAdd" name="emailAdd" value="<?php echo $emailAdd; ?>">
            		
            		 </br></br>
           			 <!-- Sumbit button -->
            		<input class="btn" type="submit" name="submitBtn" value="Submit">
         	</div>
         </form>       
<?php 
    //submit button to function
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['submitBtn']))
    {
        validateContactInfo();
    }
    
    //display errors 
    function printErrors($errs){
        echo "<div>\n";
        echo "<h3>Errors must be fixed before proceeding.</h3>\n";
        echo "<ul>\n";
        foreach ($errs as $err){
            echo "<li>".$err."</li>\n";
        }
        echo "</ul>\n";
        echo "</div>\n";
    }
    
    //validate contact information
    function validateContactInfo() {
        $error_msg = array();
        
        //firstname validation
        if (!isset($_POST['firstN'])) {
            $error_msg[] = "First name field not defined";
        }
        else if (isset($_POST['firstN'])) {
            $firstN = trim($_POST['firstN']);
            if (empty($firstN)) {
                $error_msg[] = "First name field is empty";
            }
            else {
                if (strlen($firstN) >  50) {
                    $error_msg[] = "First name field contains too many characters";
                }
            }
        }
        
        // lastname validation
        if (!isset($_POST['lastN'])) {
            $error_msg[] = "Last name field not defined";
        }
        else if (isset($_POST['lastN'])) {
            $lastN = trim($_POST['lastN']);
            if (empty($lastN)) {
                $error_msg[] = "Last name field is empty";
            }
            else {
                if (strlen($lastN) >  50) {
                    $error_msg[] = "Last name field contains too many characters";
                }
            }
        }
        
        // phone number validation
        if (!isset($_POST['phoneNum'])) {
            $error_msg[] = "Phone number field not defined";
        }
        else if (isset($_POST['phoneNum'])) {
            $phoneNum = trim($_POST['phoneNum']);
            if (empty($phoneNum)) {
                $error_msg[] = "Phone number field is empty";
            }
            else {
                if (strlen($phoneNum) >  20) {
                    $error_msg[] = "Phone number field contains too many characters";
                }
            }
        }
        
        // emial address validation
        if (!isset($_POST['emailAdd'])) {
            $error_msg[] = "Email field not defined";
        }
        else if (isset($_POST['emailAdd'])) {
            $emailAdd = trim($_POST['emailAdd']);
            if (empty($emailAdd)) {
                $error_msg[] = "Email field is empty";
            }
            else {
                if (strlen($emailAdd) >  50) {
                    $error_msg[] = "Email field contains too many characters";
                }
            }
        }
        
        //persaonl email checked or not
        if(isset($_POST['email'])){
            $_POST['email'] = 1;
        }
        else {
            $_POST['email'] = 0;
        }
        
        //personal phone checked or not
        if(isset($_POST['phone'])){
            $_POST['phone'] = 1;
        }
        else {
            $_POST['phone'] = 0;
        }
        
        if (count($error_msg) == 0) {
            $_SESSION['firstN'] = $_POST['firstN'];
            $_SESSION['lastN'] = $_POST['lastN'];
            $_SESSION['emailAdd'] = $_POST['emailAdd'];
            $_SESSION['email'] = $_POST['email'];
            $_SESSION['phone'] = $_POST['phone'];
            $_SESSION['phoneNum'] = $_POST['phoneNum'];
            
            saveSubmit();
        }
        
        else{
            printErrors($error_msg);
        }
        
        return $error_msg;
    }    
   
    function saveSubmit() {
        
        $db_conn = connectDB();
        $stmt = $db_conn->prepare("INSERT INTO lab3 (first_name , last_name, email, email_personal, phone, phone_personal) VALUES (?,?,?,?,?,?)");
        
        if (!$stmt){
            echo "Error ".$stmt->errorCode()."\nMessage ".implode($stmt->errorInfo())."\n";
            exit(1);
        }
        
        $status = $stmt->execute(array($_SESSION['firstN'], $_SESSION['lastN'], $_SESSION['emailAdd'], $_SESSION['email'],  $_SESSION['phoneNum'], $_SESSION['phone']));
        if(!$status){
            echo "Error ".$stmt->errorCode()."\nMessage ".implode($stmt->errorInfo())."\n";
           
            exit(1);
        }
        
        $db_conn = NULL;
    }
?>
	</body>
</html>



