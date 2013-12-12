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




$_POST['item_code'] = 1234;



// Check code if submitted by form
if (!empty($_POST['item_code'])) { // if an item was submitted

	// Open connection to database
	try {
		$conn = new PDO('mysql:host=' . DB_HOST . ';port='.DB_PORT.
						';dbname=' . DB_NAME, DB_USER, DB_PASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo "Error!: " . $e->getMessage(); 
	}
	
	// Is it a valid code?
	$stmt = $conn->prepare('SELECT item_code, item_id FROM items WHERE item_code = :item_code');
	$stmt->execute(array('item_code' => $_POST['item_code']));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($rows) == 0) {
		
	} else { // Invalid barcode
		$addoredit = "edit";
		include('views/edit.php');
	}	
	
} else { // nothing posted, just browed here
	//header("Location:../index.php");

	// just for testing, uncomment above and delete below
	$addoredit = "edit";
	include('views/edit.php');

?>


<!DOCTYPE html>
<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Library Tracker - Admin</title>

	<link rel="stylesheet" href="../css/jquery.mobile-1.3.1.css" />
	<link rel="stylesheet" href="../css/index.css" />	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>  
</head>

<body>
<div data-role="page">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker Admin</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
		<a href="index.php?logout">Logout</a>
	</div><!-- /header -->
	<div data-role="content">
		<a href=# data-role="button" data-rel="back" data-direction="reverse" 
			data-icon="back" data-inline="true">Back</a>
  </div><!-- end content --> 
</div><!-- end page --> 
</body>
</html>

<?php
}
?>
