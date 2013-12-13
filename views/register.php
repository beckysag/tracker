<!DOCTYPE html>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Library Tracker</title>

	<link rel="stylesheet" href="../css/jquery.mobile-1.3.1.css" />
	<link rel="stylesheet" href="../css/index.css" />	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>  
	<style>
	.ui-field-contain .ui-btn.ui-submit,
	.ui-field-contain div.ui-input-text {width:100% !important;}
	</style>
</head>


<body>
<div data-role="page">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
	</div><!-- /header -->


  <div data-role="content"> 

	<div id="errors">   
		<!-- errors & messages --->
		<?php

		// show negative messages
		if ($registration->errors) {
			foreach ($registration->errors as $error) {
				echo '<div class="error message">'.$error . '</div><br>' ;
			}
		}

		// show positive messages
		if ($registration->messages) {
			foreach ($registration->messages as $message) {
				echo '<div class="success message">'.$message. '</div><br>' ;
			}
		}
		?>
		<!-- errors & messages --->
	</div>


	<form method="post" action="register.php" name="registerform" novalidate>   
	
		<label for="login_fname">First</label>
		<input id="login_fname" type="text" pattern="[a-zA-Z]{2,64}" name="user_fname" required />

		<label for="login_lname">Last</label>
		<input id="login_lname" type="text" pattern="[a-zA-Z]{2,64}" name="user_lname" required />

		<!-- the user name input field uses a HTML5 pattern check -->
		<label for="login_username">Username (only letters and numbers, 2 to 64 characters)</label>
		<input id="login_username" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />
		
		<label for="login_password_new">Password (min. 6 characters)</label>
		<input id="login_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />  
	
		<label for="login_password_repeat">Repeat password</label>
		<input id="login_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
		<input type="submit"  name="register" value="Register" />
	
	</form>

	<!-- backlink -->
	<a href="index.php">Back to Login Page</a>	


  </div><!-- end content --> 

</div><!-- end page --> 


</body>
</html>
