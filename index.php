<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}

// include the config file (includes db.php + functions.php)
require_once("config/config.php");

// load the login class
require_once("classes/Login.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
	$LoggedIn = true;
} else { 
	// the user is not logged in
	$LoggedIn = false;
}
?>

<!DOCTYPE html>
<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Library Tracker</title>
	
	<link rel="stylesheet" href="css/jquery.mobile-1.3.1.css" />
	<link rel="stylesheet" href="css/index.css" />	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
	<script src="js/index.js"></script>  
</head>


<body>
<div data-role="page" id="homePage">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker</h1>
		<a href="" data-icon="home" data-iconpos="notext">Home</a>
		<?php if ($LoggedIn == true) { ?><a href="index.php?logout">Logout</a><?php } ?>
	</div><!-- /header -->

  <div data-role="content">
  	<div id="welcome"><p>Welcome to the Library Tracker for mobile!</p></div>

	<a rel="external" href="find" data-role="button">Browse Available</a>

<?php 

// If not logged in, prompt user to log in 
if ($LoggedIn == false) { ?>
	<div id="bottom" class="center-text">
		<p>Want to view your account?</p>
		<a href="account/index.php">Login/Register</div>
	</div>

<?php } else {

	// If admin user, link to admin page
	if ($_SESSION['user_type'] == 1 ) { ?>
		<div class="center-text">
			<a href="account/index.php" data-role="button">Admin Console</a>
		</div>

	<?php 
	// else, link to user's account page
	} else { ?>
		<div id="bottom" class="center-text">
			<p>Welcome <?php echo $_SESSION['user_name']; ?> </p>
			<a href="account/index.php">View Account</div>
		</div>
	<?php } ?>

<?php } ?>

  </div><!-- end content --> 

</div><!-- end page --> 

</body>
</html>
