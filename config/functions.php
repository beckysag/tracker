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


?>