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
<div data-role="page">

	<div data-role="header">
		<h1>Library Tracker</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
	</div><!-- /header -->

  <div data-role="content">
  	<div class="content-primary">	
  
  	<div>
    	Hi <?php echo $_SESSION['user_name']; ?>.
    </div>
    
    <h2>Your checked-out items:</h2>
<?php
$sql = 'SELECT loan_id, loan_item, loan_out, loan_in,
	item_code, item_name, item_description, item_features, 
	item_condition, item_model, item_os, item_pages
	FROM loans
	INNER JOIN users ON loans.loan_user = users.user_id
	INNER JOIN items ON loans.loan_item = items.item_id
	WHERE user_name = :user_name';

try {
	# Open connection to database
    $conn = new PDO('mysql:host=' . DB_HOST . ';port='.DB_PORT.';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $conn->prepare($sql);
	$stmt->execute(array('user_name' => $_SESSION['user_name']));
	$rows = clean_arr($stmt->fetchAll(PDO::FETCH_ASSOC));

	echo '<ul data-role="listview" data-inset="true">';
	foreach ($rows as $row) {
		echo '<li>';
		echo '<h3>'. $row['item_name'] .'</h3>';
		echo '<p><strong>'. $row['item_description'] .'</strong></p>';
		echo '<p>'. $row['loan_out'] .'</p>';
		# Hidden div with item code
		echo '<div id="item-code" style="display:none">'. $row['item_code'] .'</div>';
		echo '</li>';
	}
	echo '</ul>';

} catch (PDOException $e) {
	echo "Error!: " . $e->getMessage(); 
}

?>



	<div>
		<a href="index.php?logout">Logout</a>
	</div>


  </div>
  </div><!-- end content --> 

</div><!-- end page --> 

</body>
</html>


