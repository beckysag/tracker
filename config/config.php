<?php

require_once("db.php");
require_once("functions.php");


// Turn on all errors
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

# Set the default timezone used by all date/time functions in a script (needed for PHP 5.1+)
date_default_timezone_set('America/New_York');

$path = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$dir = substr($path, 0, strrpos($path, '/')) . '/';
$base_dir = $dir . "index.php";

// Set the session_save_path
if ( $_SERVER['DOCUMENT_ROOT'] == '/nfs/ca/info/web' ) { // if OSU
	session_save_path('/nfs/stak/students/s/sagalynr/sessions');
	$site_root = "http://web.engr.oregonstate.edu/~sagalynr/tracker/account/";
} elseif ( $_SERVER['DOCUMENT_ROOT'] == '/Users/rsagalyn/Dropbox/htdocs' ) { // if local
	session_save_path('/Users/rsagalyn/Dropbox/htdocs/tracker/sessions');
	$site_root = "/tracker/account/";
}

ini_set('session.gc_probability', 1);
