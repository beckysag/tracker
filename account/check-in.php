<?php

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


/************* AUTHENTICATION *************/
$login = new Login();

if (($login->isUserLoggedIn() == true) && ($login->isUserAdmin() == true)) {
	// is user is admin, show the admin page
	
} else {
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



GLOBAL $msg;
$callback = "pic2shop://scan?callback=http://web.engr.oregonstate.edu/~sagalynr/tracker/account/check-in.php?barcode=EAN";	

// Check code if submitted by form
if (!empty($_POST['barcode'])) { // if a barcode was submitted
	
	// Is it in the system?
	$stmt = $conn->prepare('SELECT item_code, item_id FROM items WHERE item_code = :code');
	$stmt->execute(array('code' => $_POST['barcode']));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($rows) == 1) {
		$item_id = $rows[0]['item_id'];
		
		// Is it currently checkout out?
		$stmt = $conn->prepare('SELECT loan_id FROM loans WHERE loan_item = :item_id AND loan_in IS NULL');
		$stmt->execute(array('item_id' => $item_id));
		$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($loans) == 1) {
			// Mark as checked in
			$loan_id = $loans[0]['loan_id'];
			$stmt = $conn->prepare("UPDATE loans SET loan_in=now() WHERE loan_id=?");
			$stmt->execute(array($loan_id));

	        $msg = "Item has been checked in.";
			include('views/getbarcode.php');		

		} elseif (count($loans) == 0) {
	        $msg = "Item is not listed as checked out.";
			include('views/getbarcode.php');		
		}
		else {
	        $msg = "DB is messed up. Too many loans for item.";
			include('views/getbarcode.php');				
		}
	
	} else { // Invalid barcode
        $msg = "Item doesnt exist in database. Add it first.";
		include('views/getbarcode.php');
	}	
	
} else { // nothing posted, just browed here
	include('views/getbarcode.php');
}

?>
