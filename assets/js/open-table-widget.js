/**
 *  Open Table Widget JavaScript
 *
 *  @description: JavaScripts for the frontend display of Open Table Widget
 *  @author: Devin Walker
 *  @created: 9/10/13
 */

jQuery(document).ready(function ($) {

	//Datepicker
	//@SEE: https://github.com/eternicode/bootstrap-datepicker
	$('.otw-reservation-date').datepicker({
		dateFormat    : $(this).attr('data-date-format'),
		autoclose     : true,
		todayHighlight: true
	});

	$(".otw-reservation-date").datepicker("setValue", '');

	//Selects (only if loaded)
	if (typeof $.fn.selectpicker == 'function') {
		$('.selectpicker').selectpicker();
	}

	//Party Size Change
	$('.otw-party-size-select').on('change', function () {
		$('.PartySize').val($(this).val());
	});

	//Restaurant Change
	$('.otw-reservation-restaurant').on('change', function () {
		$('.RestaurantID, .RestaurantReferralID, .rid').val($(this).val());
	});


	//City Change - Display Restaurant Finder
	$('.otw-reservation-city').on('change', function () {
		if ($(this).val() !== '') {

			$(this).parents('.otw-wrapper').children('.otw-restaurant-find-wrap').slideDown();

		} else {

			$(this).parents('.otw-wrapper').children('.otw-restaurant-find-wrap').slideUp();

		}

	});


	//Frontend Autocomplete functionality
	jQuery(".otw-restaurant-autocomplete").autocomplete({

		minLength: 2,

		source: function (request, response) {
			var element = this.element; // <-- this.element is the input the widget is bound to.
			var city = jQuery(element).parents('.otw-wrapper').find('.otw-reservation-city').val();
			city = city.replace(/\s/g, "%20"); // replace spaces

			otw_frontend_api_restaurant_autocomplete(request, response, city);

		},

		select: function (event, ui) {

			//Set Restaurant ID field when clicked
			jQuery(this).parents('.otw-wrapper').children('.RestaurantID, .RestaurantReferralID, .rid').val(ui.item.id);

		}


	});

	//Custom Autocomplete Return Values
	jQuery.ui.autocomplete.prototype._renderItem = function (ul, item) {

		var itemAddress = '';
		if (typeof(item.address) !== 'undefined' && item.address.length > 0) {
			itemAddress = item.address;
		}
		return jQuery("<li />")
				.data("item.autocomplete", item)
				.append("<a>" + "<span class='otw-item-val'>" + item.value + "</span><span class='otw-item-address'>" + itemAddress + "</span>" + "</a>")
				.appendTo(ul);

	};

	//Ensure width doesn't overlap the widget
	jQuery.ui.autocomplete.prototype._resizeMenu = function () {
		var ul = this.menu.element;
//        this.menu.element.outerWidth();
		ul.outerWidth(this.element.outerWidth());

	}


});


/**
 * Restaurant API Lookup
 *
 * Handle Autocomplete w/ Unofficial Open Table API
 */
function otw_frontend_api_restaurant_autocomplete(request, response, city) {
	//Replace Characters for Autocomplete
	request.term = request.term.split(', ').pop();
	request.term = request.term.replace("'", "%27"); // replace globally
	request.term = request.term.replace(/\s/g, "%20"); // replace spaces

	var data = {
		action    : 'open_table_api_action',
		city      : city,
		restaurant: request.term
	};

	//Empty array for restaurants name
	var restaurantsNameArray = [];

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function (jsonResponse) {

		jsonResponse = jQuery.parseJSON(jsonResponse);

		if (jsonResponse !== null) {
			//Consume Multidemensional Array in Object
			//@see: http://stackoverflow.com/questions/5181493/how-to-find-a-value-in-a-multidimensional-object-array-in-javascript
			jsonResponse.restaurants.filter(function (restaurant) {

				restaurantsNameArray.push({
					label  : restaurant.name,
					value  : restaurant.name,
					id     : restaurant.id,
					address: restaurant.address

				});
			});

		}

		response(restaurantsNameArray);

	});
}