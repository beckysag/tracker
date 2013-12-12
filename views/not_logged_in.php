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




<!-- errors & messages --->
<?php

// show negative messages
if ($login->errors) {
    foreach ($login->errors as $error) {
        echo '<span class="error">' . $error . '</span>';    
    }
}

// show positive messages
if ($login->messages) {
    foreach ($login->messages as $message) {
        echo $message;
    }
}
?>
<!-- errors & messages --->



		<!-- login form box -->
		<form method="post" action="index.php" name="loginform" novalidate>

			<label for="login_input_username">Username</label>
			<input id="login_input_username" class="login_input" type="text" name="user_name" required />

			<label for="login_input_password">Password</label>
			<input id="login_input_password" class="login_input" type="password" name="user_password" autocomplete="off" required />

			<input type="submit"  name="login" value="Log in" />

		</form>

		<a href="register.php">Register new account</a>
		
	</div><!-- end content --> 
	
</div><!-- end page --> 

</body>
</html>







