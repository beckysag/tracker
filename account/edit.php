<?php
/*

TODO: when click on "VIEW" refresh $arr() in case item has been updated
TODO: show accessories in view section
TODO: make sure edit.php scriot handles os and pages (now ajax function in index.js isn't checkoing for them)


 * Path: 	account/edit.php
 * Page ID: editPage
 * 
 * This page allows user to edit and add/edit/remove accessories for 
 * a given item. Edit section and add-accessory sections are toggled
 * by clicking the "edit" and "add accessories" buttons.
 * 
 */

// check for minimum PHP version
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


//----------------------- AUTHENTICATION -----------------------//
$login = new Login();

if (($login->isUserLoggedIn() == true) && ($login->isUserAdmin() == true)) {
	// is user is admin, show the admin page
	
} else {
    // the user is not logged in
	header("Location:../index.php");
}
//----------------------- AUTHENTICATION -----------------------//



/******** JUST FOR TESTING --- remove later ********/
	$_POST['barcode'] = 'AKB2519374';
/******** JUST FOR TESTING --- remove later ********/



//------------------------- PAGE SETUP -------------------------//
$okay = -1;
$code = "";
$arr = array();

// Check code if submitted by form
if (!empty($_POST['barcode'])) { // if an item was submitted

	$code = $_POST['barcode'];


	// Open connection to database
	try {
		$conn = new PDO('mysql:host=' . DB_HOST . ';port='.DB_PORT.
						';dbname=' . DB_NAME, DB_USER, DB_PASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo "Error!: " . $e->getMessage(); 
	}
	
	// Is it a valid code?
	$stmt = $conn->prepare('SELECT * FROM items WHERE item_code = :barcode');
	$stmt->execute(array('barcode' => $_POST['barcode']));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($rows) == 0) {
		
	} else { // barcode is valid
		$okay = 1;
		$arr = $rows[0];

		switch ($arr['item_type']) {
			case 1:
				$item_type = 'Hardware';
				break;
			case 2:
				$item_type = 'Laptop';
				break;
			case 3:
				$item_type = 'Mobile';
				break;
			case 4:
				$item_type = 'Book';
				break;
			case 5:
				$item_type = 'Game';
				break;
		}



	}	

} else { // nothing posted, just browed here
	header("Location:../index.php");
}
//------------------------- PAGE SETUP -------------------------//


// If we reach this point, we have a valid barcode number and 
// array $arr filled with its attributes

if ( $okay == 1) {
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

<div data-role="page" id="editPage">

	<!-- HEADER -->
	<div data-role="header" data-position="fixed" data-tap-toggle="false">
		<h1>Library Tracker Admin</h1>
		<a href="../" class="round-btn" data-icon="home" data-iconpos="notext">Home</a>
		<span class="btn-right navtoggle">&#9776</span>		
	</div><!-- /HEADER -->
	

	<!-- CONTENT -->
	<div data-role="content" class="content">
    
		<?php
			GLOBAL $msg;

			if (strlen($msg) > 0) { 
				echo $msg . "<br>" . "<br>";
			}

			echo '<h1>' . $arr['item_name'] . '</h1>';
		?>


		<div data-role="controlgroup" data-type="horizontal" data-mini="true" id="editnav">
			<a href="" id="btn-view-item" data-role="button" data-icon="bars"
				data-iconpos="right">View</a>
			<a href="" id="btn-edit-item" data-role="button" data-icon="edit"
				data-iconpos="right">Edit</a>
			<a href="#" id="btn-add-acc" data-role="button" data-icon="plus" 
				data-iconpos="right">Add Accessories</a>
		</div>



		<!-- VIEW SECTION -->
		<div id="section-view">
				
			<table>
				<tbody>
					<tr id="row-code">
						<th scope="row">Code</th>
						<td><?php echo $arr['item_code'];?></td>
					</tr>
					<tr id="row-type">
						<th scope="row">Type</th>
						<td><?php echo $item_type;?></td>
					</tr>
					<tr id="row-description">
						<th scope="row">Description</th>
						<td><?php echo $arr['item_description'];?></td>
					</tr>
					<tr id="row-features">
						<th scope="row">Features</th>
						<td><?php echo $arr['item_features'];?></td>
					</tr>
					<tr id="row-condition">
						<th scope="row">Condition</th>
						<td><?php echo $arr['item_condition'];?></td>
					</tr>
					<tr id="row-model">
						<th scope="row">Model</th>
						<td><?php echo $arr['item_model'];?></td>
					</tr>

					<!-- only show OS if item type = laptop or mobile -->
					<?php if ( ($item_type == 2) || ($item_type == 3) ) { ?>	
					<tr id="row-os">
						<th scope="row">OS</th>
						<td><?php echo $arr['item_os'];?></td>
					</tr>
					<?php } ?>	

					<!-- only show pages if item type = book -->
					<?php if ( $item_type == 4 ) { ?>	
					<tr id="row-pages">
						<th scope="row">Pages</th>
						<td><?php echo $arr['item_code'];?></td>
					</tr>
					<?php } ?>	
				</tbody>
			</table>

		</div>
		<!-- / VIEW SECTION -->


		<!-- EDIT SECTION -->
		<div id="section-edit">
			<form method="post">
				<div data-role="fieldcontain">
					<label for="barcode">Barcode:</label>
					<input type="text" name="barcode" id="barcode" 
							value="<?php echo $code;?>" data-mini="true" readonly/>
				</div>

				<div data-role="fieldcontain">
					<label for="select-choice-min" class="select">Type:</label>
					<select name="select-choice-min" id="select-choice-min" data-mini="true">

						<option value="0"></option>

						<option value="1" 
							<?php if ($arr['item_type'] == 1) echo 'selected';?>>Hardware
						</option>
				   
					   <option value="2"
						   <?php if ($arr['item_type'] == 2) echo 'selected';?>>Computer
						</option>
				   
					   <option value="3"
						   <?php if ($arr['item_type'] == 3) echo 'selected';?>>Mobile
						</option>
				   
						<option value="4"
							<?php if ($arr['item_type'] == 4) echo 'selected';?>>Book
						</option>
				   
						<option value="5"
							<?php if ($arr['item_type'] == 5) echo 'selected';?>>Game
						</option>
					</select>
				</div>
			
				<div data-role="fieldcontain">
					<label for="name">Name:</label>
					<input type="text" name="name" id="name" 
						value="<?php echo $arr['item_name'];?>" data-mini="true"/>
				</div>

				<div data-role="fieldcontain">
					<label for="model">Model:</label>
					<input type="text" name="model" id="model" 
						value="<?php echo $arr['item_model'];?>" data-mini="true"/>
				</div>

				<div data-role="fieldcontain">
					<label for="features">Features:</label>
					<input type="text" name="features" id="features" 
						value="<?php echo $arr['item_features'];?>" data-mini="true"/>
				</div>

				<div data-role="fieldcontain" class="pages-field">
					<label for="pages">Pages:</label>
					<input type="text" name="pages" id="pages" 
						value="<?php echo $arr['item_pages'];?>" data-mini="true"/>
				</div>

				<div data-role="fieldcontain" class="os-field">
					<label for="os">Operating System:</label>
					<input type="text" name="os" id="os" 
					value="<?php echo $arr['item_os'];?>" data-mini="true"/>
				</div>

				<div data-role="fieldcontain">
					<label for="description">Description:</label>
					<textarea cols="40" rows="8" name="description" id="description"
						data-mini="true"><?php echo $arr['item_description'];?></textarea>
				</div>

				<div data-role="fieldcontain">
					<fieldset data-role="controlgroup" data-mini="true">
						<legend>Condition:</legend>
							<input type="radio" name="condition" id="radio-choice-1" value="N"/>
							<label for="radio-choice-1">New</label>

							<input type="radio" name="condition" id="radio-choice-2" value="LN"/>
							<label for="radio-choice-2">Like New</label>

							<input type="radio" name="condition" id="radio-choice-3" value="G" />
							<label for="radio-choice-3">Good</label>

							<input type="radio" name="condition" id="radio-choice-4" value="P"/>
							<label for="radio-choice-4">Poor</label>
					</fieldset>
				</div>

				<!-- handled in index.js, form data sent to scripts/edit.php -->
				<div class="ui-body ui-body-b">
					<fieldset class="ui-grid">
							<button type="submit" data-theme="a" data-mini="true" 
									id="submit-edit">Submit</button>
					</fieldset>
				</div>
			</form>	
		</div>
		<!-- / EDIT SECTION -->
		
	
	
		<!-- ACCESSORY SECTION -->
		<div id="section-acc">

		<form>
		<button id="submit-add-acc">Add Accessory</button>	
		</form>

		</div>
		<!-- / ACCESSORY SECTION -->
		
					
	</div><!-- end content --> 
  
  
	<div data-role="panel" id="panel-nav" data-position="right" data-display="overlay">
		<ul data-role="listview" data-inset="false" data-icon="false" class="jqm-list">
			<li><a href="<?php GLOBAL $site_root; echo $site_root;?>">Admin Home</a></li>
            <li><a href="../../demos/">Logout</a></li>
		</ul>
	</div><!-- /panel -->
	  
  
  
</div><!-- end page --> 





<div data-role="dialog" id="edit-acc-dialog">
	
	<div data-role="header" data-theme="d">
		<h1></h1>
	</div>

	<div data-role="content">		

		<form>
			<div data-role="fieldcontain">
				<label for="name">Name:</label>
				<input type="text" name="acc_name" id="name" data-mini="true"/>
			</div>

			<div data-role="fieldcontain">
				<label for="quantity">Quantity:</label>
				<input type="number" name="acc_quantity" id="quantity" data-mini="true"/>
			</div>

			<div data-role="fieldcontain">
				<label for="description">Description:</label>
				<textarea cols="40" rows="8" name="acc_description" id="description" data-mini="true"></textarea>
			</div>

			<input type="hidden" name="acc_id" id="id" data-mini="true"/>

			<fieldset class="ui-grid-a">
				<div class="ui-block-a"><a href="" data-role="button" data-rel="back">Cancel</a></div>	   
				<div class="ui-block-b"><a href="" id="submit-update-acc" data-role="button" data-rel="back">Submit</a></div>	   
			</fieldset>

			<a href="" data-role="button" id="submit-remove-acc" data-rel="back">Delete</a>       
			<a href="" data-role="button" id="dialog-back" data-rel="back">Back</a>       
		</form>
		


	</div>
</div>






</body>
</html>
<?php } ?>