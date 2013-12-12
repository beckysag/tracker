<!DOCTYPE html>
<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Library Tracker</title>

	<link rel="stylesheet" href="../css/jquery.mobile-1.3.1.css" />
	<link rel="stylesheet" href="../css/index.css" />	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>  
	<script src="../js/index.js"></script>  
</head>


<body>
<div data-role="page">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
	</div><!-- /header -->


  <div data-role="content">  		
		
		<form action="results.php" method="post" data-ajax="false">

			<h2>Games</h2>
			<div data-role="fieldcontain">
				<label for="item_name">Name:</label>
				<input type="text" name="item_name" id="item_name" data-mini="true"/>
			</div>

			<div data-role="fieldcontain">
				<label for="model">Model:</label>
				<input type="text" name="item_model" id="model" data-mini="true"/>
			</div>

			<div data-role="fieldcontain">
			    <fieldset data-role="controlgroup" data-mini="true">
			    	<legend>Platform:</legend>

			    		<input type="radio" name="item_os" id="radio-choice-any" value="A" checked="checked"/>
			         	<label for="radio-choice-any">Any</label>

			         	<input type="radio" name="item_os" id="radio-choice-1" value="Playstation" />
			         	<label for="radio-choice-1">Playstation</label>

			         	<input type="radio" name="item_os" id="radio-choice-2" value="XBox"  />
			         	<label for="radio-choice-2">XBox</label>

			         	<input type="radio" name="item_os" id="radio-choice-3" value="Wii"  />
			         	<label for="radio-choice-3">Wii</label>

			         	<input type="radio" name="item_os" id="radio-choice-4" value="Desktop"  />
			         	<label for="radio-choice-4">Desktop</label>
			    </fieldset>
			</div>

			<div class="ui-body ui-body-b">
				<fieldset>
					<button type="submit" data-theme="a" data-mini="true" id="submit">Submit</button>
			    </fieldset>
			</div>

			<!-- submit id of item type -->
			<input type="hidden" name="item_type" value="5" data-mini="true"/>
		</form>	
	
  </div><!-- end content --> 

</div><!-- end page --> 
</body>
</html>