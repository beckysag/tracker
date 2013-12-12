
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
	$('#dialog-back').css('display', 'none');
})
$(document).on("pageinit", "#add-acc-dialog", function() {
	$('#dialog-add-back').css('display', 'none');
})



// Page init for account/edit.php
$(document).on("pageinit", "#editPage", function() {

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


	function handleError(str){
		// remove any previous messages
		$('.message').remove();

		// add error message div to start of content div
		$('div[data-role="content"]')
			.prepend( $('<p class="error message">'+str+'</p>') );	
	}


	// EVENT: pagechange
	$(document).on("pagechange", function(e, ui) {
		// when the accessories section is shown (need to refresh it every time)
		if ( ui.toPage.attr("id") == "editPage") {
			resetAcc();
		}
	})


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
								'<p>Quantity: ' + arr[i]['acc_quantity'] + '</div>' +
								'<div class="acc-quantity">' + arr[i]['acc_quantity'] + '</div>' +
								'<p class="acc-description">' + arr[i]['acc_description'] + '</p>' +
								'<div class="acc-id">' + arr[i]['acc_id'] + '</div>' +
								'</a></li>'));														
						}						
					});
					$('#section-acc').append(list).trigger( "create" );
					// hide ids
					$('.acc-id').css('display', 'none');
					$('.acc-quantity').css('display', 'none');
					
					// bind click event for list items
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
				handleError("Unable to load data.");
			}
		})			
	}// end resetAcc()



	// click "add accessory" button to bring up dialog with form
	$('#show-acc-form').click(function(e){
		// enter item's id in hidden field
		$('#add-acc-dialog input[name="acc_item"]').val($('#barcode').val());
		$.mobile.changePage( "#add-acc-dialog", { role: "dialog" } );
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

function scancode() {
	setTimeout(function() {
		// if pic2shop not installed yet, go to App Store
		window.location = "http://itunes.com/apps/pic2shop";
	}, 25);
	// launch pic2shop and tell it to open Google Products with scan result
	window.location="pic2shop://scan?callback=http%3A%2F%2Fweb." +
		"engr.oregonstate.edu%2F~sagalynr%2Ftracker%2Ftest.php%3Fcode%3DEAN"
}
