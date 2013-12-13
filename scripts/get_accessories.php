<?php
require_once("../config/config.php");

try { // Open connection to database
	$conn = new PDO('mysql:host=' . DB_HOST . ';port='.DB_PORT.';dbname=' . DB_NAME, DB_USER, DB_PASS);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Error!: " . $e->getMessage(); 
}


$rslt = array();	// array of data to return

// Barcode is required, return error if not submitted
if (empty($_POST["barcode"])) {
	$rslt['err'] = "A barcode is required";
	$rslt['errno'] = -1;
	echo json_encode($rslt);
	exit();
}



$stmt = $conn->prepare(
	'SELECT * FROM accessories 
	 WHERE acc_item = (SELECT item_id FROM items WHERE item_code = :barcode)');
$stmt->execute(array('barcode' => $_POST['barcode']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rslt = $rows;
$rslt['errno'] = 0;
$rslt['n'] = count($rows);
echo json_encode($rslt);