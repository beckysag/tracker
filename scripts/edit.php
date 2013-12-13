<?php
// edits an item based on posted values from form on accounts/edit.php page

require_once("../config/config.php");

try { // Open connection to database
	$conn = new PDO('mysql:host=' . DB_HOST . ';port='.DB_PORT.';dbname=' . DB_NAME, DB_USER, DB_PASS);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Error!: " . $e->getMessage(); 
}

// Strip slashes
if ( !empty($_POST) ) $_POST = array_stripslash($_POST);

$arr = array();
$rslt = array();
$sql = 'UPDATE items SET item_type = ?, item_name = ?, 
		item_description = ?, item_features = ?, 
		item_condition = ?, item_model = ?, item_os = ?, 
		item_pages = ? WHERE item_code = ?';

// Type is required
if (!empty($_POST["type"])) {
	array_push($arr, $_POST["type"]);
} else {
	$rslt['err'] = "A type is required";
	$rslt['errno'] = -1;
	echo json_encode($rslt);
	exit();
}
// Name is required
if (!empty($_POST["name"])) {
	array_push($arr, $_POST["name"]);
} else {
	$rslt['err'] = "A name is required";
	$rslt['errno'] = -1;
	echo json_encode($rslt);
	exit();
}
if (!empty($_POST["description"])) { // Description
	array_push($arr, $_POST["description"]);
} else {
	array_push($arr, NULL);
}
if (!empty($_POST["features"])) { // Features
	array_push($arr, $_POST["features"]);
} else {
	array_push($arr, NULL);
}
if (!empty($_POST["condition"])) { // Condition
	array_push($arr, $_POST["condition"]);
} else {
	array_push($arr, NULL);
}
if (!empty($_POST["model"])) { // Model
	array_push($arr, $_POST["model"]);
} else {
	array_push($arr, NULL);
}
if (!empty($_POST["os"])) { // Operating System
	array_push($arr, $_POST["os"]);
} else {
	array_push($arr, NULL);
}
if (!empty($_POST["pages"])) { // Pages
	// Is it a number??
	if (preg_match('/[^0-9]/', $_POST["pages"])) {
		$rslt['err'] = "Pages must be a numberic value";
		$rslt['errno'] = -1;
		echo json_encode($rslt);
		exit();	
	}
	array_push($arr, $_POST["pages"]);
} else {
	array_push($arr, NULL);
}

// Barcode is required, return error if not submitted
if (!empty($_POST["barcode"])) {
	array_push($arr, $_POST["barcode"]);
} else {	
	$rslt['err'] = "A barcode is required";
	$rslt['errno'] = -1;
	echo json_encode($rslt);
	exit();
}

$stmt = $conn->prepare($sql);
$stmt->execute($arr);

$rslt['errno'] = 0;
$rslt['name'] = $_POST["name"];
echo json_encode($rslt);