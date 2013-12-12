
function getType(n) {
	var item_type;
	switch (Number(n)) {
		case 1:
			item_type = 'Hardware';
			break;
		case 2:
			item_type = 'Laptop';
			break;
		case 3:
			item_type = 'Mobile';
			break;
		case 4:
			item_type = 'Book';
			break;
		case 5:
			item_type = 'Game';
			break;
	}
	return item_type;
}

// jquery css edits
function loadPage(){
	$('a').removeClass('ui-shadow');
}


// when dialog loads, hide the back button
$(document).on("pageinit", "#edit-acc-dialog", function() {
	//$('#dialog-back').css('display', 'none');
})


// Page init for account/edit.php
$(document).on("pageinit", "#editPage", function() {

	function handleError(str){
		// remove any previous messages
		$('.message').remove();

		// add error message div to start of content div
		$('div[data-role="content"]')
			.prepend( $('<p class="error message">'+str+'</p>') );	
	}

	// reset prefilled form values to current db values
	function resetForm(){	
		$.ajax({
			type: "POST",
			url: "../scripts/get_item.php",
			data: {barcode: $('#barcode').val()}
		})
		.done(function(rslt) {
			var arr = $.parseJSON(rslt);
			//console.info(arr);

			if (arr['errno'] == -1) { // error

			} else { // success - reset form values
				$('#section-edit #select-choice-min').val(arr['item_type']);
				$('#section-edit #name').val(arr['item_name']);
				$('#section-edit #model').val(arr['item_model']);
				$('#section-edit #features').val(arr['item_features']);
				$('#section-edit #pages').val(arr['item_pages']);
				$('#section-edit #os').val(arr['item_os']);
				$('#section-edit #description').val(arr['item_description']);

				// uncheck all "condition" radio buttons
				$('input[name="condition"]').prop("checked",false).checkboxradio("refresh");

				// check the correct radio button
				var c = arr['item_condition'];
				var curr = $('input[name="condition"]').filter('input[value=' +c + ']');					
				curr.prop("checked",true).checkboxradio("refresh");
			}				
		})
		.fail(function() {
		});			
			
	}// end resetForm()
	
	
	
	// reset View section current item attributes
	function resetView(){	
		// get fresh array of item attributes
		$.ajax({
			type: "POST",
			url: "../scripts/get_item.php",
			data: {
				barcode: $('#barcode').val()
			},
			// success function
			success: function (rslt) {
				var arr = $.parseJSON(rslt);
				//console.info(arr);
				if (arr['errno'] == -1) { 
					// error
					handleError(arr['err']);
				} else { 					
					// success - reset form values		
					// replace null values with empty string			
					$.each(arr, function(i, obj) {
						if (jQuery.type(obj) === "null")
							arr[i] = "";
					});			
					$('#row-type td').text(getType(arr['item_type']));
					$('#row-model td').text(arr['item_model']);
					$('#row-description td').text(arr['item_description']);
					$('#row-features td').text(arr['item_features']);
					$('#row-pages td').text(arr['item_pages']);
					$('#row-os td').text(arr['item_os']);
					$('#row-condition td').text(arr['item_condition']);
				}				
			},			
			// error function
			error: function(data) {
				handleError("Unable to load data.");
			}
		})			
	}// end resetView()
	


	// reset Accessories section current item attributes
	function resetAcc(){	
		// append stuff to #section-acc
		// get fresh array of item attributes
		$.ajax({
			type: "POST",
			url: "../scripts/get_accessories.php",
			data: {
				barcode: $('#barcode').val()
			},
			// success function
			success: function (rslt) {
				var arr = $.parseJSON(rslt);
				//console.info(arr);				

				if (arr['errno'] == -1) { 
					// error
					handleError(arr['err']);
				} else { 					
					// success - add accessories to page	
					var list = $('<ul data-role="listview" data-inset="true" class="acc"></ul>');
					list.append($('<li data-role="list-divider">Accessories</li>'));

					// loop through each result	
					$.each(arr, function(i, obj) {
						// ignore non-item array elements												
						if (arr[i]['acc_id']){
						
							// replace nulls with empty strings
							if (jQuery.type(arr[i]['acc_description']) === "null")
								arr[i]['acc_description'] = "";
								
								list.append($('<li><a href=""><h1>'+ arr[i]['acc_name'] +'</h1>' + 
								'<p class="acc-description">' + arr[i]['acc_description'] + '</p>' +
								'<p class="acc-quantity">' + arr[i]['acc_quantity'] + '</p>' +
								'<div class="acc-id">' + arr[i]['acc_id'] + '</div>' +
								'</a></li>'));														
						}
					});
					$('#section-acc').append(list).trigger( "create" );
					
					// bind click event for list items
					$('.acc li').click(function(e){
						var id = $(this).find($('.acc-id')).text();
						var name = $(this).find($('h1')).text();
						var desc = $(this).find($('.acc-desciption')).text();
						var quantity = $(this).find($('.acc-quantity')).text();
						var barcode = $('#barcode').val();

						$.mobile.changePage( "#edit-acc-dialog", { role: "dialog" } );
						$('#edit-acc-dialog div[data-role="header"] h1').text(name);
						$('#edit-acc-dialog form input[name="acc_name"]').val(name);
						$('#edit-acc-dialog form input[name="acc_quantity"]').val(quantity);
						$('#edit-acc-dialog form input[name="acc_description"]').val(desc);
						$('#edit-acc-dialog form input[name="acc_id"]').val(id);						
					})
					
					
				}				
			},			
			// error function
			error: function(data) {
				handleError("Unable to load data.");
			}
		})			
	}// end resetAcc()



	loadPage();	



	// Pages and O/S fields are initially hidden
	$('.pages-field').css("display", "none");
	$('.os-field').css("display", "none");

	

	/* Slide-down menu stuff */
	
	// Add slide-down menu here so jqm doesn't interfere	
	var menu = $('<nav id="mobile"><ul id="mmenu">' + 
		'<li><a href="#">Admin Home</a></li>' +
        '<li><a href="#">Logout</a></li>' + 
		'</ul></nav>');
	$('#editPage div[data-role="header"]').after(menu);

	// hide it initially	
	$("#mmenu").hide();

    $(".navtoggle").click(function() {
        $("#mmenu").slideToggle(500);
    });
	


	/* edit/add controlgroup stuff */
	
	// when page first loads, view button is selected
	$('#btn-view-item').addClass('ui-btn-active');
	$('#section-edit').hide();
	$('#section-acc').hide();
	

	// EVENT: click "view/edit"/"add accessory" button
	$('#editnav a').click(function(e){
		var id = $(this).attr('id');

		// make current selection button active
		$(this).parent().find('a').removeClass('ui-btn-active');
		$(this).addClass('ui-btn-active');
		
		if (id == 'btn-view-item') {
			// show #section-add, hide #section-edit & #section-view
			$('#section-view').show();
			$('#section-acc').hide();
			$('#section-edit').hide();
			
			// refresh array of item attributes (in case it's been edited since page loaded)
			resetView();	
		} 
		else if (id == 'btn-add-acc') {
			// show #section-add, hide #section-edit & #section-view
			$('#section-acc').show();
			$('#section-edit').hide();
			$('#section-view').hide();
			resetAcc();	
		} 
		else if (id == 'btn-edit-item') {
			// show #section-edit, hide #section-add & #section-view
			$('#section-edit').show();
			$('#section-acc').hide();
			$('#section-view').hide();		
			
			// reset form
			$('.message').remove();			// remove any previous messages
			resetForm();
		}
		
	})


	/******* $('#select-choice-min').change() *******/
	// add type-specific input fields to the form
	$('#select-choice-min').on('change', function(){
		var val = $(this).val();

		if ((val == 1) || (val == 2) || (val == 3)) {
			// add OS input field
			$('.os-field').css("display", "block");		
			$('.pages-field').css("display", "none");
		} else if (val == 4) {
			// add pages input field
			$('.pages-field').css("display", "block");
			$('.os-field').css("display", "none");
		} else {
			// hide both
			$('.pages-field').css("display", "none");
			$('.os-field').css("display", "none");
		}
	})
	/******* $('#select-choice-min').change() *******/
	
	

	/************* #submit-edit.click() *************/
	$('#submit-edit').click(function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../scripts/edit.php",
			data: { 
				barcode: $('#barcode').val(),
				type: $('#select-choice-min').val(),
				name: $('#name').val(),
				model: $('#model').val(),
				description: $('#description').val(),
				os: $('#os').val(),
				pages: $('#pages').val(),
				features: $('#features').val(),
				condition: $("input[name=condition]:checked").val()
			}
		})
			.done(function(rslt) {
				$('.error').remove();
				var arr = $.parseJSON(rslt);
				console.info(arr);

				if (arr['errno'] == -1) { // error
					$('.message').remove();			// remove any previous messages
					// display error message
					$('form').before( $('<p class="error message">'+ arr['err'] +'</p>') );
					resetForm();

				} else {// success
					$('.message').remove();			// remove any previous messages
					$('form').before( 
						$('<p class="success message">'+arr['name']+' edited successfully!</p>'));
				}				
			})
			.fail(function() {
				$('.message').remove();			// remove any previous messages
				$('form').before( 
					$('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	
	/************* #submit-edit.click() *************/

	/************* #submit-add-acc.click() *************/
	$('#submit-add-acc').click(function(e){
        var form = $(this).parents('form');
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../scripts/add_accessory.php",
			data: form.serialize()
		})
			.done(function(rslt) {
				$('.error').remove();
				var arr = $.parseJSON(rslt);
				console.info(arr);
				if (arr['errno'] == -1) { // error
					// display error message
					form.before( $('<p class="error">'+ arr['err'] +'</p>') );
				} else {// success
					form.before( $('<p class="message">'+arr['name']+' added successfully!</p>'));
					form.hide();					
				}				
			})
			.fail(function() {
				$('.error').remove();
				form.before( 
					$('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	
	/************* #submit-add-acc.click() *************/

	/************* .submit-update-acc.click() *************/
	$('#submit-update-acc').click(function(e){
        var form = $(this).parents('form');
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../scripts/update_accessory.php",
			data: form.serialize()
		})
			.done(function(rslt) {
				$('.error').remove();
				var arr = $.parseJSON(rslt);
				console.info(arr);
				if (arr['errno'] == -1) { // error
					// display error message
					form.before( $('<p class="error">'+ arr['err'] +'</p>') );
				} else {// success
					form.before( $('<p class="message">'+arr['name']+' updated successfully!</p>'));
					form.hide();					
					$('#dialog-back').css('display', 'block');
										
				}				
			})
			.fail(function() {
				$('.message').remove();
				form.before( 
					$('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	
	/************* .submit-update-acc.click() *************/

	/************* .submit-remove-acc.click() *************/
	$('#submit-remove-acc').click(function(e){
        var form = $(this).parents('form');
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../scripts/remove_accessory.php",
			data: form.serialize()
		})
			.done(function(rslt) {
				$('.error').remove();
				var arr = $.parseJSON(rslt);
				console.info(arr);
				if (arr['errno'] == -1) { // error
					// display error message
					form.before( $('<p class="error">'+ arr['err'] +'</p>') );
				} else {// success
					form.before( 
						$('<p class="message">'+arr['name']+' removed successfully!</p>'));
					form.hide();					
				}				
			})
			.fail(function() {
				$('.error').remove();
				form.before( 
					$('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	
	/************* .submit-remove-acc.click() *************/


});




/**
 * Click handler for listview items on results page
 */
$( document ).on( "pageinit", "#results", function( event ) {	
	$('#result-list li').click(function(){

		var name = $(this).find('h3').text();
		$('input:text[name=item-name]').val(name);

		var desc = $(this).find('p').text();
		$('input:text[name=item-description]').val(desc);

		var features = $(this).find('#item-features').text();
		$('input:text[name=item-features]').val(features);

		var code = $(this).find('#item-code').text();
		$('input:text[name=item-code]').val(code);
		
		//$('#hidden-form').submit();
		$.mobile.changePage( "detail.php", {
			type: "post", 
			data: $("form#hidden-form").serialize()
		});		

	})	
});



$(document).on("pageinit", "#detailPage", function() {
	$('#edit').click(function(){
		var x = $('.code-field')[0].innerHTML;
		$('#item_code').val(x);
		$("#edit-form").submit(); // post form data to accounts/edit.php
	})	
});







$(document).on("pageinit", "#addPage", function() {

	// Pages and O/S fields are initially hidden
	$('.pages-field').css("display", "none");
	$('.os-field').css("display", "none");


	/******* $('#select-choice-min').change() *******/
	// add type-specific input fields to the form
	$('#select-choice-min').on('change', function(){
		var val = $(this).val();

		if ((val == 1) || (val == 2) || (val == 3)) {
			// add OS input field
			$('.os-field').css("display", "block");		
			$('.pages-field').css("display", "none");
		} else if (val == 4) {
			// add pages input field
			$('.pages-field').css("display", "block");
			$('.os-field').css("display", "none");
		} else {
			// hide both
			$('.pages-field').css("display", "none");
			$('.os-field').css("display", "none");
		}
	})
	/******* $('#select-choice-min').change() *******/

	/************* #submit-edit.click() *************/
	$('#submit-edit').click(function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../scripts/add.php",
			data: { 
				barcode: $('#barcode').val(),
				type: $('#select-choice-min').val(),
				name: $('#name').val(),
				model: $('#model').val(),
				description: $('#description').val(),
				features: $('#features').val(),
				condition: $("input[name=condition]:checked").val()
			}
		})
			.done(function(rslt) {
				$('.error').remove();
				var arr = $.parseJSON(rslt);
				console.log(arr);
				if (arr['errno'] == -1) { // error
					// display error message
					$('form').before( $('<p class="error">'+ arr['err'] +'</p>') );
				} else {// success
					$('form').before( 
						$('<p class="message">'+arr['name']+' added successfully!</p>'));
					$('form').hide();					
				}				
			})
			.fail(function() {
				$('.error').remove();			
				$('form').before( 
					$('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	
	/************* #submit-edit.click() *************/

});






/*
$(document).on("pageinit", "#editView", function() {

	// add type-specific input fields to the form
	$('#select-choice-min').on('change', function(){
		var val = $(this).val();

		if ((val == 1) || (val == 2) || (val == 3)) {
			// add OS input field
			$('.os-field').css("display", "block");		
			$('.pages-field').css("display", "none");
		} else if (val == 4) {
			// add pages input field
			$('.pages-field').css("display", "block");
			$('.os-field').css("display", "none");
		} else {
			// hide both
			$('.pages-field').css("display", "none");
			$('.os-field').css("display", "none");
		}
	})

	$('#submit-edit').click(function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../scripts/add.php",
			data: { 
				barcode: $('#barcode').val(),
				type: $('#select-choice-min').val(),
				name: $('#name').val(),
				model: $('#model').val(),
				description: $('#description').val(),
				features: $('#features').val(),
				condition: $("input[name=condition]:checked").val()
			}
		})
			.done(function(rslt) {
				$('.error').remove();
				var arr = $.parseJSON(rslt);
				console.info(arr);
				if (arr['errno'] == -1) { // error
					// display error message
					$('form').before( $('<p class="error">'+ arr['err'] +'</p>') );
				} else {// success
					$('form').before( 
						$('<p class="message">'+arr['name']+' added successfully!</p>'));
					$('form').hide();					
				}				
			})
			.fail(function() {
				$('.error').remove();			
				$('form').before( 
					$('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	

	// append form to content
	var form = $('<form method="post"></form>');

	var action = $('#addoredit')[0].innerText;
	var code = $('#item_code').text();

	var fc = $('<div data-role="fieldcontain"></div>');

	// add header to form
	if (action == 'edit')
		form.append('<h2>Edit</h2>');
	else
		form.append('<h2>Add New</h2>');


	// add barcode fieldcontain
	var code_input = $('<input type="text" name="barcode" id="barcode" "data-mini="true"/>');
	form.append(fc.clone().append($('<label for="barcode">Barcode:</label>'))
		.append(code_input)
	);

	// add type fieldcontain
	form.append(fc.clone().append($('<div data-role="fieldcontain" ' + 
	'class="ui-field-contain ui-body ui-br"><label for="select-choice-min" ' +
	'class="select ui-select">Type:</label><div class="ui-select"><div data-corners="true" ' +
	'data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-icon="arrow-d" ' +
	'data-iconpos="right" data-theme="c" data-mini="false" class="ui-btn ui-shadow ui-btn-corner-all '+
	'ui-fullsize ui-btn-icon-right ui-btn-up-c"><span class="ui-btn-inner"><span class="ui-btn-text">'+
	'<span></span></span><span class="ui-icon ui-icon-arrow-d ui-icon-shadow">&nbsp;</span></span>'+
	'<select name="select-choice-min" id="select-choice-min" data-mini="false"><option value="0">'+
	'</option><option value="1">Hardware</option><option value="2">Computer</option>'+
	'<option value="3">Mobile</option><option value="4">Book</option><option value="5">Game</option>'+
	'</select></div></div></div>')));
				

	// add name fieldcontain
	form.append(fc.clone().append($('<label for="name">Name:</label>'))
		.append('<input type="text" name="name" id="name" "data-mini="true"/>')
	);

	// add model fieldcontain
	form.append(fc.clone().append($('<label for="model">Model:</label>'))
		.append('<input type="text" name="model" id="model" "data-mini="true"/>')
	);

	// add features fieldcontain
	form.append(fc.clone().append($('<label for="features">Features:</label>'))
		.append('<input type="text" name="features" id="features" "data-mini="true"/>')
	);

	// add pages fieldcontain
	form.append(fc.clone().addClass('pages-field').append($('<label for="pages">Pages:</label>'))
		.append('<input type="text" name="pages" id="pages" "data-mini="true"/>')
	);

	// add OS fieldcontain
	form.append(fc.clone().addClass('os-field').append($('<label for="os">Operating System:</label>'))
		.append('<input type="text" name="os" id="os" "data-mini="true"/>')
	);

	// add description fieldcontain
	form.append(fc.clone().append($('<label for="description">Description:</label>'))
		.append('<input type="text" name="description" id="description" "data-mini="true"/>')
	);
	
	// add condition fieldcontain
	var fs = $('<fieldset data-role="controlgroup" data-mini="true" '+
		'class="ui-corner-all ui-controlgroup ui-controlgroup-vertical ui-mini" aria-disabled="false" '+
		'data-disabled="false" data-shadow="false" data-corners="true" data-exclude-invisible="true" '+ 
		'data-type="vertical" data-init-selector=":jqmData(role=\'controlgroup\')">')
	form.append(fc.clone().append(fs));
	fs.append('<div role="heading" class="ui-controlgroup-label"><legend>Condition:</legend></div>');

	var controls = $('<div class="ui-controlgroup-controls"></div>');
	fs.append(controls);
	controls.append('<div class="ui-radio"><input type="radio" name="condition" id="radio-choice-1" '+
		'value="N" checked="checked"><label for="radio-choice-1" data-corners="true" '+
		'data-shadow="false" data-iconshadow="true" data-wrapperels="span" '+
		'data-icon="radio-on" data-theme="c" data-mini="true" class="ui-radio-on ui-btn '+
		'ui-btn-up-c ui-btn-corner-all ui-mini ui-btn-icon-left ui-first-child">'+
		'<span class="ui-btn-inner"><span class="ui-btn-text">New</span><span '+
		'class="ui-icon ui-icon-radio-on ui-icon-shadow">&nbsp;</span></span></label></div>')
	  .append('<div class="ui-radio"><input type="radio" name="condition" id="radio-choice-2" value="LN"><label for="radio-choice-2" data-corners="true" data-shadow="false" data-iconshadow="true" data-wrapperels="span" data-icon="radio-off" data-theme="c" data-mini="true" class="ui-radio-off ui-btn ui-btn-up-c ui-btn-corner-all ui-mini ui-btn-icon-left"><span class="ui-btn-inner"><span class="ui-btn-text">Like New</span><span class="ui-icon ui-icon-radio-off ui-icon-shadow">&nbsp;</span></span></label></div>')
	  .append('<div class="ui-radio"><input type="radio" name="condition" id="radio-choice-3" value="G"><label for="radio-choice-3" data-corners="true" data-shadow="false" data-iconshadow="true" data-wrapperels="span" data-icon="radio-off" data-theme="c" data-mini="true" class="ui-radio-off ui-btn ui-btn-up-c ui-btn-corner-all ui-mini ui-btn-icon-left"><span class="ui-btn-inner"><span class="ui-btn-text">Good</span><span class="ui-icon ui-icon-radio-off ui-icon-shadow">&nbsp;</span></span></label></div>')
	.append('<div class="ui-radio"><input type="radio" name="condition" id="radio-choice-4" value="P"><label for="radio-choice-4" data-corners="true" data-shadow="false" data-iconshadow="true" data-wrapperels="span" data-icon="radio-off" data-theme="c" data-mini="true" class="ui-radio-off ui-btn ui-btn-up-c ui-btn-corner-all ui-mini ui-btn-icon-left ui-last-child"><span class="ui-btn-inner"><span class="ui-btn-text">Poor</span><span class="ui-icon ui-icon-radio-off ui-icon-shadow">&nbsp;</span></span></label></div>');


	// add submit button
	var z = $('<div data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" ' +
		'data-theme="a" data-mini="true" data-disabled="false" aria-disabled="false" ' +
		'class="ui-submit ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-up-a">' +	
		'<span class="ui-btn-inner"><span class="ui-btn-text">Submit</span></span>' + 
		'<button type="submit" data-theme="a" data-mini="true" id="submit-edit" ' + 
			'class="ui-btn-hidden" data-disabled="false">Submit</button>');
		
	$('<div class="ui-block"></div>')
		.append(z)
		.appendTo(
			$('<fieldset class="ui-grid"></fieldset>').appendTo(
				$('<div class="ui-body ui-body-b"></div>').appendTo(form)
			)
		);

	// add jquery mobile classes
	form.find( ":jqmData(role='fieldcontain')" ).addClass("ui-field-contain ui-body ui-br");

	form.find( ":jqmData(role='fieldcontain')" )
		.children("label").addClass("ui-input-text");

	form.find( ":jqmData(role='fieldcontain')" )
		.children("input").addClass("ui-input-text ui-body-c")
		.wrap('<div class="ui-input-text ui-shadow-inset ui-corner-all ui-btn-shadow ui-body-c ui-mini"></div>');
	
	$('.content').append(form);		

	// Pages and O/S fields are initially hidden
	$('.pages-field').css("display", "none");
	$('.os-field').css("display", "none");
});

$(document).on("pageshow", "#editView", function() {
	$('#barcode').val($('#item_code').text());
});
*/



function scancode() {
	setTimeout(function() {
		// if pic2shop not installed yet, go to App Store
		window.location = "http://itunes.com/apps/pic2shop";
	}, 25);
	// launch pic2shop and tell it to open Google Products with scan result
	window.location="pic2shop://scan?callback=http%3A%2F%2Fweb." +
		"engr.oregonstate.edu%2F~sagalynr%2Ftracker%2Ftest.php%3Fcode%3DEAN"
}
