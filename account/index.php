<?php

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../libraries/password_compatibility_library.php");
}

// load the config file
require_once("../config/config.php");

// load the login class
require_once("../classes/Login.php");



// create a login object
$login = new Login();

// ... are we logged in?
if ($login->isUserLoggedIn() == true) {

	if ($login->isUserAdmin() == true) {
		// is user is admin, show the admin page
		include("../views/admin.php"); 
	} else {
		// show user's account page
		include("../views/logged_in.php"); 
	}

} else {
    include("../views/not_logged_in.php");
}

?>
