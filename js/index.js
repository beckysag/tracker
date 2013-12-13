var root = 'http://web.engr.oregonstate.edu/~sagalynr/tracker/';

// Triggered when the page has been created in the DOM 
// (via ajax or other) but before all widgets have had an 
// opportunity to enhance the contained markup.
$( document ).on( "pagecreate", function( event ) {

	// Remove ugly shadow from buttons 
	$('a').removeClass('ui-shadow');

	// Include favicons on each page
	var found_ico = 0;
	var incl = $(document).find('link');
	// Check if already included
	$.each(incl, function(i, obj) {
		if (obj.href == root + 'images/favicon.png') found_ico = 1;
	});			
	if ( found_ico == 0 ){
		var se1 = document.createElement('link');
		se1.href = root + 'images/favicon.png';
		se1.rel = 'shortcut icon';
		se1.type = 'image/png';
		var se2 = document.createElement('link');
		se2.href = root + 'images/apple-touch-icon.png';
		se2.rel = 'apple-touch-icon';
		se2.sizes = '57x57';
		var head = document.getElementsByTagName('head')[0]
		head.appendChild(se1);
		head.appendChild(se2);
	}
});


// When the dialog loads, hide the back button
$(document).on("pageinit", "#edit-acc-dialog", function() {
	$('#dialog-back').css('display', 'none');
})


// When the dialog loads, hide the back button
$(document).on("pageinit", "#add-acc-dialog", function() {
	$('#dialog-add-back').css('display', 'none');
})


// Page init for account/edit.php
$(document).on("pageinit", "#editPage", function() {
	// Add slide-down menu here (after jqm renders) so jqm doesn't interfere	
	var menu = $('<nav id="mobile"><ul id="mmenu"><li><a href="index.php">Admin Home</a></li>' +
		'<li><a href="index.php?logout">Logout</a></li></ul></nav>');
	$('#editPage div[data-role="header"]').after(menu);

	// Hide the slide-down nav menu initially	
	$("#mmenu").hide();
    $(".navtoggle").click(function() {
        $("#mmenu").slideToggle(500);
    });
	
	// On initial pageload, view button is selected in controlgroup
	$('#btn-view-item').addClass('ui-btn-active');
	$('#section-edit').hide();
	$('#section-acc').hide();
		

	function displayError(str){
		$('.message').remove();	// remove any previous messages
		// add error message div to start of content div
		$('div[data-role="content"]').prepend( $('<p class="error message">'+str+'</p>') );	
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
			if (arr['errno'] == -1) {
				// Display error message
				displayError(arr['err']);
			} else { 
				// success - reset form values
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
			displayError("Unable to load data.");
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
				if (arr['errno'] == -1) { 
					// Display error message
					displayError(arr['err']);
				} else {
					// Success - reset form values		
					// replace null values with empty string			
					$.each(arr, function(i, obj) {
						if (jQuery.type(obj) === "null")
							arr[i] = "";
					});			
					$('.item-title').text(arr['item_name']);
					$('#row-type td').text(getType(arr['item_type']));
					$('#row-description td').text(arr['item_description']);
					$('#row-features td').text(arr['item_features']);
					$('#row-pages td').text(arr['item_pages']);
					$('#row-os td').text(arr['item_os']);
					$('#row-condition td').text(arr['item_condition']);
				}				
			},			
			// error function
			error: function(data) {
				displayError("Unable to load data.");
			}
		})			
	}// end resetView()
	

	// refresh list of Accessories
	function resetAcc(){	
		// first remove old list
		$('#section-acc ul').remove();

		// get fresh array of acecssories
		$.ajax({
			type: "POST",
			url: "../scripts/get_accessories.php",
			data: {
				barcode: $('#barcode').val()
			},
			// success function
			success: function (rslt) {
				var arr = $.parseJSON(rslt);
				if (arr['errno'] == -1) { 
					// Display error message
					displayError(arr['err']);
				} else { 					
					// success - add accessories to page	
					var list = $('<ul data-role="listview" data-inset="true" class="acc"></ul>');
					list.append($('<li data-role="list-divider">Accessories</li>'));

					// loop through each result	
					$.each(arr, function(i, obj) {
						// ignore non-item array elements												
						if (arr[i]['acc_id']){						
							// replace nulls with empty strings
							if (jQuery.type(arr[i]['acc_description']) === "null") {
								arr[i]['acc_description'] = "";
							}
							list.append($('<li><a href=""><h1>'+ arr[i]['acc_name'] +'</h1>' + 
							'<p>Quantity: ' + arr[i]['acc_quantity'] + '</div>' +
							'<div class="acc-quantity">' + arr[i]['acc_quantity'] + '</div>' +
							'<p class="acc-description">' + arr[i]['acc_description'] + '</p>' +
							'<div class="acc-id">' + arr[i]['acc_id'] + '</div>' +
							'</a></li>'));														
						}						
					});
					$('#section-acc').append(list).trigger( "create" );
					// hide id and quantity field
					$('.acc-id').css('display', 'none');
					$('.acc-quantity').css('display', 'none');
					
					// bind click event for listview items
					$('.acc li').click(function(e){
						// show form, remove .message, hide back button
						$('#acc-form').show();					
						$('#edit-acc-dialog').find('.message').remove();
						$('#dialog-back').css('display', 'none');

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
				displayError("Unable to load data.");
			}
		})			
	}// end resetAcc()


	// EVENT: click a "view|edit|add accessory" controlgroup button
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
			
			// remove any previous messages and refresh form
			$('.message').remove();
			resetForm();
		}		
	})


	// EVENT: change value of select element in "edit item" form
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
	
	
	// EVENT: click "submit" on "edit item" form
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
				$('.message').remove();
				var arr = $.parseJSON(rslt);
				if (arr['errno'] == -1) {
					// Display error message
					displayError(arr['err']);
					resetForm();
				} else { // Display success message
					$('form').before($('<p class="success message">'+arr['name']+' edited successfully!</p>'));
				}				
			})
			.fail(function() {
				$('.message').remove();			// remove any previous messages
				displayError("Something went wrong. Try again.");
			});		
	})	




	/* Start of accessory-related events */

	// EVENT: pagechange
	$(document).on("pagechange", function(e, ui) {		
		// when the accessories section is shown (need to refresh it every time)
		if ( ui.toPage.attr("id") == "editPage") {
			resetAcc();
		}
	})

	// EVENT: click "add accessory" button to bring up dialog with form
	$('#show-acc-form').click(function(e){
		// show form and clear, remove .message, hide back button
		$('#add-acc-dialog form').show().get(0).reset();
		$('#add-acc-dialog').find('.message').remove();
		$('#add-acc-dialog #dialog-add-back').css('display', 'none');

		// enter item's id in hidden field
		$('#add-acc-dialog input[name="acc_item"]').val($('#section-edit input[name="item_id"]').val());
		$.mobile.changePage( "#add-acc-dialog", { role: "dialog" } );
	})	

	// EVENT: in "Add Accessory" form, click "submit"
	$('#submit-add-acc').click(function(e){
        var form = $(this).parents('form');
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../scripts/add_accessory.php",
			data: form.serialize()
		})
			.done(function(rslt) {
				$('.message').remove();
				var arr = $.parseJSON(rslt);
				console.info(arr);
				if (arr['errno'] == -1) { 	// display error message
					form.before( $('<p class="error message">'+ arr['err'] +'</p>') );
				} else {	// success
					form.before( $('<p class="message">'+arr['name']+' added successfully!</p>'));
					form.hide();					
					$('#dialog-add-back').css('display', 'block'); // Show the "Back" button
				}				
			})
			.fail(function() {
				$('.message').remove();
				form.before($('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	

	// EVENT: in "Edit Accessory" form, click "submit"
	$('#submit-update-acc').click(function(e){
        var form = $(this).parents('form');
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "../scripts/update_accessory.php",
			data: form.serialize()
		})
			.done(function(rslt) {
				$('.message').remove();
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

	// EVENT: in "Edit Accessory" form, click "Delete"
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
					form.before($('<p class="message">'+arr['name']+' removed successfully!</p>'));
					form.hide();					
					$('#dialog-back').css('display', 'block');										
				}				
			})
			.fail(function() {
				$('.error').remove();
				form.before( 
					$('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	

}); // end of #editPage init


// Click handler for listview items on results page
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


// Event: init of detailPage
$(document).on("pageinit", "#detailPage", function() {
	$('#edit').click(function(){
		var x = $('.code-field')[0].innerHTML;
		$('#item_code').val(x);
		$("#edit-form").submit(); // post form data to accounts/edit.php
	})	
});



// Handle initialization of the addPage (accounts/add.php)
$(document).on("pageinit", "#addPage", function() {
	

	// Pages and O/S fields are initially hidden
	$('.pages-field').css("display", "none");
	$('.os-field').css("display", "none");


	// Event: on change value of select element with item type
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

	// Event: click "submit" button in form
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
				$('.message').remove();
				var arr = $.parseJSON(rslt);
				if (arr['errno'] == -1) {
					// display error message
					$('form').before( $('<p class="error message">'+ arr['err'] +'</p>') );
				} else {// success
					$('form').before($('<p class="error message">'+arr['name']+' added successfully!</p>'));
					$('form').hide();					
				}				
			})
			.fail(function() {
				$('.error').remove();			
				$('form').before($('<p class="error message">Something went wrong. Try again.</p>') );
			});		
	})	
});


// Convert item type from integer (as in database) 
// to string and return string
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
