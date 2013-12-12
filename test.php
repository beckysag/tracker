<?php
$code = $_REQUEST['ean'];
echo '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>';
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>test barcode reader</title>
</head>

<body>
<div>
	<?php if ( $code ) : ?>
	<p>The barcode was <?php echo htmlentities( $code );?></p>
	<?php endif; ?>

	<p> <a 	href="pic2shop://scan?callback=http://web.engr.oregonstate.edu/~sagalynr/tracker/test.php?barcode=EAN">read barcode</a>
	</p>
</div>
</body>
</html>