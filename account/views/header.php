<!DOCTYPE html>
<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Library Tracker - Admin</title>

	<link rel="stylesheet" href="../css/jquery.mobile-1.3.1.css" />
	<link rel="stylesheet" href="../css/index.css" />	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>  
</head>

<body>
<div data-role="page">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker Admin</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
		<a href="index.php?logout">Logout</a>
	</div><!-- /header -->
	
	<div data-role="content">
		<a href="<?php GLOBAL $site_root; echo $site_root;?>">Back to Admin Console</a><br><br>
