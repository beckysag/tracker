<?php
require_once("../config/config.php");

try { // Open connection to database
	$conn = new PDO('mysql:host=' . DB_HOST . ';port='.DB_PORT.';dbname=' . DB_NAME, DB_USER, DB_PASS);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Error!: " . $e->getMessage(); 
}

$arr = array();
$rslt = array();
$sql = 'UPDATE accessories SET acc_name = ?, acc_description = ?, 
		 acc_quantity = ? WHERE acc_id = ?';

// acc_name is required
if (!empty($_POST["acc_name"])) {
	array_push($arr, $_POST["acc_name"]);
} else {
	$rslt['err'] = "An accessory name is required";
	$rslt['errno'] = -1;
	echo json_encode($rslt);
	exit();
}

if (!empty($_POST["acc_description"])) { // Description
	array_push($arr, $_POST["acc_description"]);
} else {
	array_push($arr, NULL);
}

if (!empty($_POST["acc_quantity"])) { // Quantity
	array_push($arr, $_POST["acc_quantity"]);
} else {
	array_push($arr, NULL);
}

// acc_id is required, return error if not submitted
if (!empty($_POST["acc_id"])) {
	array_push($arr, $_POST["acc_id"]);
} else {	
	$rslt['err'] = "An id is required";
	$rslt['errno'] = -1;
	echo json_encode($rslt);
	exit();
}
$stmt = $conn->prepare($sql);
$stmt->execute($arr);

$rslt['errno'] = 0;
$rslt['name'] = $_POST["acc_name"];
echo json_encode($rslt);
