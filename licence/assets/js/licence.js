/**
 *  Licence JS
 *
 *  @description: Used to activate / deactivate licences via AJAX
 *  @author: Devin Walker
 *  @created: 12/3/13
 */

jQuery(document).ready(function ($) {

	var doing_licence_registration_ajax = false;
	var admin_url = ajaxurl.replace('/admin-ajax.php', ''), spinner_url = admin_url + '/images/wpspin_light';

	if (window.devicePixelRatio >= 2) {
		spinner_url += '-2x';
	}
	spinner_url += '.gif';

	/**
	 * Activates Licence
	 */
	$('.register-licence').on('click', function () {

		if (doing_licence_registration_ajax) {
			return false;
		}

		var licence_key = $.trim($('.licence-key').val());
		var licence_email = $.trim($('.licence-email').val());

		if (licence_key == '' || licence_email == '') {
			display_licence_message('Please enter your licence key and email found in your purchase email.', 'alert-danger');
			return false;
		}

		//empty any messages that may be present
		$('.licence-status').fadeOut('300', function () {
			$(this).attr('class', 'licence-status');
		});

		doing_licence_registration_ajax = true;

		$('.licence-button-wrap > .button:visible').after('<img src="' + spinner_url + '" alt="" class="register-licence-ajax-spinner general-spinner" />');

		$.ajax({
			url     : ajaxurl,
			type    : 'POST',
			dataType: 'JSON',
			cache   : false,
			data    : {
				action       : 'wordimpress_activate_licence',
				licence_key  : licence_key,
				licence_email: licence_email
			},
			error   : function (jqXHR, textStatus, errorThrown) {
				//for debugging errors in console
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);

				doing_licence_registration_ajax = false;
				$('.register-licence-ajax-spinner').remove();
				display_licence_message('A problem occurred when trying to deactivate the licence, please try again. If the problem continues please contact support.', 'alert-red');
			},
			success : function (data) {
				//show data in console
				console.log(data);

				//cleanup
				doing_licence_registration_ajax = false;
				//remove AJAX loader
				$('.register-licence-ajax-spinner').remove();

				//error in activation?
				if (typeof data.error !== 'undefined') {

					//display error message
					display_licence_error_message(data.error, data.code);
				}

				//No errors, proceed
				else {

					//success message
					display_licence_message('Your licence has been activated: ' + data.message + '. You will now receive plugin updates.', 'alert-success');
					//swap buttons
					$('.register-licence').fadeOut().addClass('licence-hidden');
					$('.deactivate-licence').removeClass('licence-hidden').fadeIn();
					//add checkmark
					$('.licence-input').addClass('input-active-licence');


				}
			}
		});

		return false;
	});


	/**
	 * Deactivate a Licence
	 */
	$('.deactivate-licence').on('click', function () {

		//prevents overlap
		if (doing_licence_registration_ajax) {
			return false;
		}

		//get current licence values in inputs
		var licence_key = $.trim($('.licence-key').val());
		var licence_email = $.trim($('.licence-email').val());

		//check if licence key is blank
		if (licence_key == '' || licence_email == '') {
			display_licence_message('Please enter the licence key and email found in your purchase email.', 'alert-danger');
			return false;
		}

		//empty any messages that may be present
		$('.licence-status').fadeOut('300', function () {
			$(this).attr('class', 'licence-status');
		});

		//we are starting to do some ajax
		doing_licence_registration_ajax = true;

		//place a loading image next to button
		$('.licence-button-wrap > .button:visible').after('<img src="' + spinner_url + '" alt="" class="register-licence-ajax-spinner general-spinner" />');

		//start AJAX
		$.ajax({
			url     : ajaxurl,
			type    : 'POST',
			dataType: 'JSON',
			cache   : false,
			data    : {
				action       : 'wordimpress_deactivate_licence',
				licence_key  : licence_key,
				licence_email: licence_email
			},
			error   : function (jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);

				doing_licence_registration_ajax = false;
				$('.register-licence-ajax-spinner').remove();
				$('.licence-status').addClass('alert alert-red').html('A problem occurred when trying to register the licence, please try again.');
			},
			success : function (data) {

				//debug info shown in console
				console.log(data);

				//no longer doing AJAX
				doing_licence_registration_ajax = false;

				//remove spinner
				$('.register-licence-ajax-spinner').remove();

				//output errors if any
				if (typeof data.errors !== 'undefined') {
					var msg = '';
					for (var key in data.errors) {
						msg += data.errors[key];
					}
					$('.licence-status').html(msg);
				}
				else {

					//display success message
					display_licence_message('Your licence has been deactivated successfully.', 'alert-warning');
					//swap buttons
					$('.deactivate-licence').fadeOut().addClass('licence-hidden');
					$('.register-licence').removeClass('licence-hidden').fadeIn();
					//clear values
					$('.licence-key').val('');
					$('.licence-email').val('');
					$('.licence-input').removeClass('input-active-licence');


				}
			}
		});

		return false;
	});


	function check_licence(licence_key, licence_email) {
		$.ajax({
			url     : ajaxurl,
			type    : 'POST',
			dataType: 'json',
			cache   : false,
			data    : {
				action       : 'wordimpress_check_licence',
				licence_key  : licence_key,
				licence_email: licence_email
			},
			error   : function (jqXHR, textStatus, errorThrown) {
				alert('A problem occurred when trying to check the licence, please try again.');
			},
			success : function (data) {
				if (typeof data.errors !== 'undefined') {
					var msg = '';
					for (var key in data.errors) {
						msg += data.errors[key];
					}
					$('.support-content').empty().html(msg);
				}
				else {
					$('.support-content').empty().html(data.message);
				}
			}
		});
	}


});


function display_licence_message(message, style) {


	jQuery('.licence-status').html(message).attr('class', '').addClass('licence-status alert ' + style);
	jQuery('.licence-status').fadeIn();


}

function display_licence_error_message(message, code) {

	var output_message = '';

	switch (code) {
		case '100':
			output_message = message + ': Please check that you have entered the licence key and email address found in your completed order email.';
			break;
		case '101':
			output_message = message;
			break;
		default :
			output_message = message +  '; Please contact support for assistance with activating your licence.'
	}


	jQuery('.licence-status').html(output_message).attr('class', '').addClass('licence-status alert alert-red');
	jQuery('.licence-status').fadeIn();


}