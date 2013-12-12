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
	<script>
		/**
		 *  post params to url, where params is json array 
		 */
		function do_post(url, params) {
			var form = document.createElement("form");
			form.setAttribute("method", "post");
			form.setAttribute("action", url);
			/* iterate over keys of params object */
			for (var key in params) {
				if (params.hasOwnProperty(key)) {
					var hiddenField = document.createElement("input");
					hiddenField.setAttribute("type", "hidden");
					hiddenField.setAttribute("name", key);
					hiddenField.setAttribute("value", params[key]);
					form.appendChild(hiddenField);
				}
			}
			document.body.appendChild(form);
			form.submit();
		}
		
		
		$( document ).on( "pageinit", "#findIndex", function( event ) {
			$('#item-list li a').click(function(e){
				e.preventDefault();
				var id = $(this).attr('id');
				var args = {
					id: id
				};				
				var url = id + ".php";
				do_post(url, args)
			});
		});
	</script>

</head>


<body>
<div data-role="page" id="findIndex">

	<div data-role="header" data-position="fixed">
		<h1>Library Tracker</h1>
		<a href="../" data-icon="home" data-iconpos="notext">Home</a>
	</div><!-- /header -->

  <div data-role="content">
  
  	<div class="content-primary">
  		<ul data-role="listview" id="item-list">
			<li><a id="hardware" href="index.html">Hardware</a></li>
			<li><a id="laptop" href="index.html">Laptops</a></li>
			<li><a id="mobile" href="index.html">Mobile Devices</a></li>
			<li><a id="book" href="index.html">Books</a></li>
			<li><a id="game" href="index.html">Games</a></li>
		</ul>		

	</div><!--/content-primary -->		


  </div><!-- end content --> 

</div><!-- end page --> 

</body>
</html>
