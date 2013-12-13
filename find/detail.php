<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
	exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
	// add the PHP 5.5 password hashing functions to older versions of PHP
	require_once("../libraries/password_compatibility_library.php");
}
require_once("../config/config.php");
require_once("../classes/Login.php");
$login = new Login();
?>

<!DOCTYPE html>
<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Library Tracker</title>

	<link rel="stylesheet" href="../css/jquery.mobile-1.3.1.css" />
	<link rel="stylesheet" href="../css/index.css" />	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>  
	
</head>


<body>
<div data-role="page" id="detailPage">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
	</div><!-- /header -->


  <div data-role="content"> 

	<!-- Back Button-->  
	<a href=# data-role="button" data-rel="back" data-direction="reverse" 
			data-icon="back" data-inline="true">Back</a>

	<?php
	if (empty ($_POST['item-code'])) {
		echo "Start a new search.";
	} else {

		if (($login->isUserLoggedIn() == true) && ($login->isUserAdmin() == true)) {
			echo '<a href=# data-role="button" data-icon="edit" data-inline="true" id="edit">Edit</a>';
		}

		// Strip slashes
		if ( !empty($_POST) ) $_POST = array_stripslash($_POST);

		$n = $d = $c = $f = "";
		if (!empty ($_POST['item-name'])) $n = $_POST['item-name'];			
		if (!empty ($_POST['item-description'])) $d = clean($_POST['item-description']);			
		if (!empty ($_POST['item-features'])) $f = $_POST['item-features'];			
		if (!empty ($_POST['item-code'])) $c = $_POST['item-code'];			

		echo '<h3>' . $n . '</h3>';		

		try {
			$conn = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME,DB_USER,DB_PASS);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo "Error!: " . $e->getMessage(); 
		}
		$stmt = $conn->prepare('SELECT * FROM items WHERE item_code = :barcode');
		$stmt->execute(array('barcode' => $_POST['item-code']));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$arr = $rows[0];
					
		echo '	<table>
		<tbody>
			<tr>
				<th>Barcode</th>
				<td class="code-field">'. $c .'</td>
			</tr>
			<tr>
				<th scope="row">Features</th>
				<td>'. $f .'</td>
			</tr>
			<tr>
				<th scope="row">Description</th>
				<td>'. $d .'</td>
			</tr>
			<tr>
				<th scope="row">Condition</th>
				<td>'. get_condition($arr['item_condition']) .'</td>
			</tr>
			<tr>
				<th scope="row">Model</th>
				<td>'. $arr['item_model'] .'</td>
			</tr>
		</tbody>
	</table>';
		$stmt = $conn->prepare('SELECT * FROM items LEFT JOIN loans ON item_id=loan_item ' .
			'WHERE item_code = :barcode AND loan_in IS NULL AND loan_out IS NOT NULL');
		$stmt->execute(array('barcode' => $_POST['item-code']));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($rows) == 0) echo '<p class="success">Available to rent</p>';
		else echo '<p class="error">Currently checked out.</p>';
	}
	?>	

	<form class="hidden" action="../account/edit.php" method="post" id="edit-form" data-ajax="false">
		<input type="text" name="barcode" id="item_code" />
		<button type="submit" data-theme="a" data-mini="true" id="submit-btn">Submit</button>
	</form>
	
  </div><!-- end content --> 
</div><!-- end page --> 


</body>
</html>