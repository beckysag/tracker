<?php
/*                                                                		*
 * Prompt admin to scan or enter barcode								*
 * 																		*
 * If scanned, do some error checking (ie if scanner fails) ??? 		*
 * Check that item is in system, if not, display error message with		*
 *   prompt to enter as new item 										*
 * If in system & available, get users name, add record to loans table 	*
 *                                                                 		*
 *                                                                		*/

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // add the PHP 5.5 password hashing functions to older versions of PHP
	require_once("../libraries/password_compatibility_library.php");
}

// include the config file (includes db.php + functions.php)
require_once("../config/config.php");

// load the login class
require_once("../classes/Login.php");

GLOBAL $msg;

// Authentication
$login = new Login();
if (!(($login->isUserLoggedIn() == true) && ($login->isUserAdmin() == true))) {
    // the user is not logged in
	header("Location:../index.php");
}

// Open connection to database
try {
	$conn = new PDO('mysql:host=' . DB_HOST . ';port='.DB_PORT.';dbname=' . DB_NAME, DB_USER, DB_PASS);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Error!: " . $e->getMessage(); 
}

// Callback for scanning
$callback = "pic2shop://scan?callback=http://web.engr.oregonstate.edu/~sagalynr/tracker/account/check-out.php?barcode=EAN";

// Check code if submitted by form
if (!empty($_POST['barcode'])) { // if a barcode was submitted
	
	// Is it in the system?
	$stmt = $conn->prepare('SELECT item_code, item_id FROM items WHERE item_code = :code');
	$stmt->execute(array('code' => $_POST['barcode']));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($rows) == 1) {
		$item_id = $rows[0]['item_id'];
		
		// Is it available to rent??
		$stmt = $conn->prepare('SELECT loan_id FROM loans WHERE loan_item = :item_id AND loan_in IS NULL');
		$stmt->execute(array('item_id' => $item_id));
		$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($loans) == 0) {
			$msg = "";
			include('views/get-user.php');
		} else {
	        $msg = "Item is listed as checked out.";
			include('views/getbarcode.php');		
		}
	
	} else { 
        $msg = "Item doesnt exist in database. Add it first.";
		include('views/getbarcode.php');
	}	
} 

// If form was submitted but without a barcode, tell user a barcode is required
// and show the "getbarcode" page again
elseif (isset($_POST['barcode'])) { 
	$msg = "Please enter a barcode on continue.";
	include('views/getbarcode.php');

} elseif (!empty($_POST['username'])) { // if a username was submitted
	// If we get to this point, we know a valid barcode was already given and 
	// item_id is in $_POST['item_id']
		
	// Check if username exists
	$stmt = $conn->prepare('SELECT user_id, user_name FROM users WHERE user_name = :username AND user_type = 2');
	$stmt->execute(array('username' => $_POST['username']));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($rows) == 1) {
		$msg = "";
		$user_id = $rows[0]['user_id'];
		$item_id = $_POST['item_id'];

		// Add new record to loans table with user_id and code and current time
		$stmt = $conn->prepare('INSERT INTO loans (loan_item, loan_user) VALUES (?,?)');
		$stmt->execute(array( $item_id, $user_id ));
				
		include('views/header.php');
		echo "Item successfully checked out.";
		include('views/footer.php');
		
	}
	else {
		// Show page to get username again
		$msg = "user doesnt exist";		
		$item_id = $_POST['item_id'];
		include('views/get-user.php');
	}
	
} else { // nothing posted, just browed here
	include('views/getbarcode.php');
}

?>
