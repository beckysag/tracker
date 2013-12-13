<?php

$path = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$dir = substr($path, 0, strrpos($path, '/')) . '/';
$base_dir = $dir . "index.php";

$msg = '';


/**
 * function to clean submitted data
 */
function clean($data) 
{
    get_magic_quotes_gpc() ? stripslashes(trim($data)) : trim($data);
    $data = htmlspecialchars($data);
    return trim($data);
}

// From: https://maxmorgandesign.com/php_remove_slashes_from_array/
function array_stripslash($theArray){
	foreach ( $theArray as &$v ) 
		if ( is_array($v) ) 
			$v = array_stripslash($v); 
		else $v = stripslashes($v);
   return $theArray;
}



/**
 * function to clean submitted arrays
 */
function clean_arr($arr)
{
	foreach ($arr as $row) {
		foreach ($row as $item) {
			clean($item);
		}
	}
	return $arr;
}




################ Debugging functions ##################

function do_alert($msg) 
{
    echo '<script type="text/javascript">alert("' . $msg . '"); </script>';
}



function print_and_die($msg) 
{
    echo $msg.'</br>';
    die();
}



function printvar($var) 
{
    echo '<pre>';
    if (is_array($var)) {
        print_r($var);
    } else {
        var_dump($var);
    }
    echo '</pre>';
}

function get_condition($str) {
	$ret = "";
	if ($str == 'N')
		$ret = 'New';
	elseif ($str == 'LN')
		$ret = 'Like New';
	elseif ($str == 'P')
		$ret = 'Poor';
	elseif ($str == 'G')
		$ret = 'Good';
	return $ret;
}
?>