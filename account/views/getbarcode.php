<?php
GLOBAL $site_root; 
echo '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>';
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
	<script src="../js/index.js"></script>
	
</head>


<body>
<div data-role="page" id="getBarcode">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker Admin</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
		<a href="index.php?logout">Logout</a>
	</div><!-- /header -->
	

	<div data-role="content">

		<a id="back-to-admin" href="<?php echo $site_root;?>">
			Back to Admin Console</a><br><br>

		<?php
		GLOBAL $msg;
		echo '<p class="error message">' . $msg . '</p>';
		?>

		Enter item barcode
		<a href="<?php echo $callback;?>" data-role="button">Scan Barcode with Phone</a>
		
		<div id="or-box">
			<span>or</span>
		</div>

		<form id="frm-barcode" method="post" data-ajax="false">

			<?php if (isset($_REQUEST['ean'])) : ?>			
				<input type="text" name="barcode" id="barcode" placeholder="Enter barcode"
					value="<?php echo htmlentities($_REQUEST['ean']);?>"/>			
			<?php else : ?>
				<input type="text" name="barcode" id="barcode" placeholder="Enter barcode"/>			
			<?php endif; ?>


		</form>	
		<button form="frm-barcode" type="submit" id="submit">Submit</button>
			
  </div><!-- end content --> 
</div><!-- end page --> 

</body>
</html>
