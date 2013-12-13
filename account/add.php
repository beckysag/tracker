<?php
/*                                                                		
 * Displays form to add a new item to the database. The barcode is prepopulated from the
 * previous form, where the item was scanned (or the barcode was manually entered).
 *                                                                		
 */

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


// Authenticate user, and return to homepage if user isn't logged in
$login = new Login();
if (!(($login->isUserLoggedIn() == true) && ($login->isUserAdmin() == true))) {
    // The user is not logged in
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

// used by getbarcode.php
$callback = "pic2shop://scan?callback=http://web.engr.oregonstate.edu/~sagalynr/tracker/account/add.php?barcode=EAN";	

$okay = -1;

// Check code if submitted by form
if (!empty($_POST['barcode'])) { // if a barcode was submitted
	
	// Is it in the system already?
	$stmt = $conn->prepare('SELECT item_code, item_id FROM items WHERE item_code = :code');
	$stmt->execute(array('code' => $_POST['barcode']));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($rows) == 0) {
		// Show form to add details with barcode in form
		$okay = 1;
								
	} else { // Invalid barcode
        $msg = "Item already exists in database.";
		include('views/getbarcode.php');
	}	
	
} else { // nothing posted, just browed here
	include('views/getbarcode.php');	
}

if ($okay == 1 ) {
?>


<!DOCTYPE html>
<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Library Tracker - Admin</title>

	<link rel="stylesheet" href="../css/jquery.mobile-1.3.1.css" />
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>  
	<link rel="stylesheet" href="../css/index.css" />	
	<script src="../js/index.js"></script>  
</head>

<body>
<div data-role="page" id="addPage">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker Admin</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
		<a href="index.php?logout">Logout</a>
	</div><!-- /header -->
	

	<div data-role="content" class="content">

		<a rel="external" id="back-to-admin" 
			href="<?php GLOBAL $site_root; echo $site_root;?>">
			Back to Admin Console</a><br><br>

		<?php
			GLOBAL $msg;
			if (strlen($msg) > 0) echo $msg . "<br>" . "<br>";
		?>


		<form method="post">
			<h2>Add New</h2>
			<div data-role="fieldcontain">
				<label for="barcode">Barcode:</label>
				<input type="text" name="barcode" id="barcode" 
						value="<?php echo $_POST['barcode'];?>" data-mini="true" readonly />
			</div>

			<div data-role="fieldcontain">
				<label for="select-choice-min" class="select">Type:</label>
				<select name="select-choice-min" id="select-choice-min">
					<option value="0" selected></option>
					<option value="1">Hardware</option>
					<option value="2">Computer</option>				   
				   	<option value="3">Mobile</option>				   
					<option value="4">Book</option>			
					<option value="5">Game</option>
				</select>
			</div>
			
			<div data-role="fieldcontain">
				<label for="name">Name:</label>
				<input type="text" name="name" id="name" data-mini="true"/>
			</div>

			<div data-role="fieldcontain">
				<label for="model">Model:</label>
				<input type="text" name="model" id="model" data-mini="true"/>
			</div>

			<div data-role="fieldcontain">
				<label for="features">Features:</label>
				<input type="text" name="features" id="features" data-mini="true"/>
			</div>

			<div data-role="fieldcontain" class="pages-field">
				<label for="pages">Pages:</label>
				<input type="text" name="pages" id="pages" data-mini="true"/>
			</div>

			<div data-role="fieldcontain" class="os-field">
				<label for="os">Operating System:</label>
				<input type="text" name="os" id="os" data-mini="true"/>
			</div>

			<div data-role="fieldcontain">
				<label for="description">Description:</label>
				<textarea name="description" id="description" data-mini="true"></textarea>
			</div>

			         		
			<div data-role="fieldcontain">
			    <fieldset data-role="controlgroup" data-mini="true">
			    	<legend>Condition:</legend>
			         	<input type="radio" name="condition" id="radio-choice-1" value="N" checked/>
			         	<label for="radio-choice-1">New</label>

			         	<input type="radio" name="condition" id="radio-choice-2" value="LN"/>
			         	<label for="radio-choice-2">Like New</label>

			         	<input type="radio" name="condition" id="radio-choice-3" value="G"/> 
			         	<label for="radio-choice-3">Good</label>

			         	<input type="radio" name="condition" id="radio-choice-4" value="P"/>
			         	<label for="radio-choice-4">Poor</label>
			    </fieldset>
			</div>

			<div class="ui-body ui-body-b">
				<fieldset class="ui-grid">
					<!-- handled by event handler in index.js-->
					<button type="submit" data-theme="a" data-mini="true" 
							id="submit-edit">Submit</button>
			    </fieldset>
			</div>
		</form>	
		
					
  </div><!-- end content --> 
</div><!-- end page --> 

</body>
</html>

<?php }?>
