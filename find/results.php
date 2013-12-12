<?php

/**
This page selects all items from the database table `items` with the posted type
and with the posted attributes

*/

require_once("../config/config.php");

try {	// Open connection to database
    $conn = new PDO('mysql:host=' . DB_HOST . ';port='.DB_PORT.';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Error!: " . $e->getMessage(); 
}
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
	<script src="../js/index.js"></script>  
</head>


<body>
<div data-role="page" id="results">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
	</div><!-- /header -->


  <div data-role="content">

  	<div class="content-primary">	
		<ul data-role="listview" id="result-list">
  				
		<?php
		// Start with empty array (for values to bind) and basic SELECT query
		$arr = array();
		$sql = 'SELECT item_id, item_code, item_name, item_description, item_features, 
			item_condition, item_model, item_os, item_pages
			FROM items WHERE item_type LIKE :item_type';

		// If page url loaded directly, set item_type to wildcard
		if (!empty($_POST["item_type"])) {
			$type_id = $_POST["item_type"];
		} else {
			$type_id = '%';
		}
		$arr['item_type'] = $type_id;


		// Now add individual attributes based on what was posted...
		
		if (!empty($_POST["item_name"])) {
			$sql .= ' AND item_name LIKE :item_name';
			$arr['item_name'] = '%'.$_POST["item_name"].'%';
		}

		if (!empty($_POST["item_model"])) {
			$sql .=' AND item_model = :item_model';
			$arr['item_model'] = $_POST["item_model"];
		}

		if (!empty($_POST["item_description"])) {
			$sql .=' AND item_description LIKE :item_description';
			$arr['item_description'] = '%'.$_POST["item_description"].'%';
		}
		
		if (!empty($_POST["condition"])) {
		   if($_POST["condition"] == "A") {		   
			   $sql .=' AND item_condition LIKE :condition';
			   $arr['condition'] = '%';
			}
			else {		   
			   $sql .=' AND item_condition = :condition';
			   $arr['condition'] = $_POST["condition"];
			}
		}

		if (!empty($_POST["item_os"])) {
		   if (! ($_POST["item_os"] == "A") ) {
			   $sql .=' AND item_os LIKE :item_os';
			   $arr['item_os'] = $_POST["item_os"].'%';
			}
		}


		// Prepare and execute our statement
		$stmt = $conn->prepare($sql);
		$stmt->execute($arr);	
		
		// Fetch results
		$rows = clean_arr($stmt->fetchAll(PDO::FETCH_ASSOC));
		
		// If no items found, tell the user
		if (count($rows) == 0) {
			echo 'No results found';
			echo '<a href=# data-role="button" data-rel="back" 
					data-direction="reverse" data-icon="back" 
					data-inline="true">Back</a>';
		}
				
		// Else, display found items
		foreach ($rows as $row) {
			echo '<li><a href="">';
			echo '<h3>'. $row['item_name'] .'</h3>';
			echo '<p><strong>'. $row['item_description'] .'</strong></p>';
			echo '<div id="item-features" style="display:none">'. $row['item_features'] .'</div>';
			# Hidden div with item code
			echo '<div id="item-code" style="display:none">'. $row['item_code'] .'</div>';
			echo '</a></li>';
		}
		?>

		</ul>
		</div><!--/content-primary -->	

  </div><!-- end content --> 

	<!-- values to pass to detail page -->
	<form style="display:none;" id="hidden-form" method="post" action="detail.php" 
			data-transition="slide">
		<input type="text" name="item-name" value="myval"/>
		<input type="text" name="item-description"/>
		<input type="text" name="item-features"/>
		<input type="text" name="item-code"/>
		<input type="submit"/>		
	</form>
	
</div><!-- end page --> 

</body>
</html>