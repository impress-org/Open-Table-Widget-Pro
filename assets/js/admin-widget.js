/**
 *  Open Table Widget JS: WP Admin
 *
 *  @description: JavaScripts for the admin side of the widget
 *  @author: Devin Walker
 *  @created: 8/29/13
 *  @since: 1.0
 */

jQuery.noConflict();

jQuery( document ).ready( function ( $ ) {

	/*
	 * Initialize the API Request Method widget radio input toggles
	 */
	otwAutoComplete();
	otwWidgetToggles();
	otwWidgetTooltips();


} );

function otwAutoComplete() {

	//Autocomplete 1 - For Single Restaurant section
	jQuery( ".otw-auto-complete-1" ).autocomplete( {

		minLength: 2,

		source: function ( request, response ) {

			otw_handle_api_restaurant_autocomplete( request, response );

		},

		select: function ( event, ui ) {

			//Set Restaurant ID field when clicked
			jQuery( this ).parent().next().children( '.restaurant-id' ).val( ui.item.id );

		}

	} );


	//Autocomplete 2 - For Multiple Restaurant section
	jQuery( ".otw-auto-complete-2" ).autocomplete( {

		minLength: 2,

		source: function ( request, response ) {

			otw_handle_api_restaurant_autocomplete( request, response );

		},

		select: function ( event, ui ) {


			//Replace Commas in clicked value
			var clickedVal = ui.item.value.replace( ",", "" );
			var hiddenVal = clickedVal + "|" + ui.item.id;
			var hiddenValInput = jQuery( this ).parents( '.otw-toggle-option-2' ).find( '.restaurant-ids-hidden' );
			var currentHiddenVals = hiddenValInput.val();
			var currentSortables = jQuery( this ).parents( '.otw-toggle-option-2' ).find( '.sortable ' );
			var newSortableLi = jQuery( '<li class="ui-state-default" id="' + hiddenVal + '"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' + clickedVal + '<span class="ui-icon ui-icon-close"></li>' );

			//Append Sortables
			jQuery( currentSortables ).append( newSortableLi ).sortable( 'refresh' );

			//Append Hidden Vals
			if ( currentHiddenVals.length > 0 ) {
				hiddenValInput.val( currentHiddenVals + "," + hiddenVal )
			} else {
				hiddenValInput.val( hiddenVal )
			}

			//Clear this clicked value
			jQuery( this ).val( '' );
			return false;

		}


	} );

	//Autocomplete 3 - For Postal Code Restaurant section

	try {
		var citiesJSON = jQuery.parseJSON( ajax_object.city_array.body );

	} catch ( e ) {
		// not json
		console.log( 'There was a problem receiving the cities list from the Open Table API' );
	}

	jQuery( ".otw-auto-complete-3" ).autocomplete( {

		minLength: 2,

		source: function ( request, response ) {

			console.log( request.term );
			// delegate back to autocomplete, but extract the last term
			response( jQuery.ui.autocomplete.filter(
				citiesJSON.cities, extractLast( request.term ) ) );
		},

		select: function ( event, ui ) {

			var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// add placeholder to get the comma-and-space at the end
			terms.push( "" );
			this.value = terms.join( ", " );
			return false;

		}

	} );


	//Custom Autocomplete Return Values
	//@see: http://stackoverflow.com/questions/7205699/jquery-autocomplete-render-item-is-not-executed
	jQuery.ui.autocomplete.prototype._renderItem = function ( ul, item ) {
		var itemAddress = '';
		if ( typeof(item.address) !== 'undefined' && item.address.length > 0 ) {
			itemAddress = "<br/>" + item.address + "</a>";
		}
		return jQuery( "<li />" )
			.data( "item.autocomplete", item )
			.append( "<a>" + item.value + itemAddress )
			.appendTo( ul );
	};

	//Sortables
	jQuery( ".sortable-wrap ul" ).sortable( {
		cursor     : 'move',
		placeholder: 'ui-state-highlight',
		update     : function () {
			var orderData = jQuery( this ).sortable( "toArray" );
			jQuery( this ).parents( '.sortable-wrap' ).children( '.restaurant-ids-hidden' ).val( orderData );
		}
	} );
	jQuery( ".sortable-wrap ul" ).disableSelection();

	//Handle Removing Items
	jQuery( ".sortable-wrap .ui-icon-close" ).click( function () {

		var sortableWrap = jQuery( this ).parents( 'ul' );
		//fade out clicked sortable and reset IDs
		jQuery( this ).parent().fadeOut( 300, function () {

			jQuery( this ).remove();
			var orderData = jQuery( sortableWrap ).sortable( "toArray" );
			jQuery( sortableWrap ).parents( '.sortable-wrap' ).children( '.restaurant-ids-hidden' ).val( orderData );
		} );

	} );

}

/**
 * Function to Refresh jQuery toggles for Yelp Widget Pro upon saving specific widget
 */
jQuery( document ).ajaxSuccess( function ( e, xhr, settings ) {
	otwAutoComplete();
	otwWidgetToggles();
	otwWidgetTooltips();
} );

/**
 * Restaurant API Lookup
 *
 * Handle Autocomplete w/ Unofficial Open Table API
 */
function otw_handle_api_restaurant_autocomplete( request, response ) {

	//Replace Characters for Autocomplete
	request.term = request.term.split( ', ' ).pop();
//	request.term = request.term.replace("'", "%27"); // replace apostrophes globally
	request.term = request.term.replace( /\s/g, "%20" ); // replace spaces

	var data = {
		action    : 'open_table_api_action',
		restaurant: request.term
	};

	//Empty array for restaurants name
	var restaurantsNameArray = [];

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post( ajaxurl, data, function ( jsonResponse ) {

		jsonResponse = jQuery.parseJSON( jsonResponse );

		if ( jsonResponse !== null ) {
			//Consume Multidemensional Array in Object
			//@see: http://stackoverflow.com/questions/5181493/how-to-find-a-value-in-a-multidimensional-object-array-in-javascript
			jsonResponse.restaurants.filter( function ( restaurant ) {

				restaurantsNameArray.push( {
					label  : restaurant.name,
					value  : restaurant.name,
					id     : restaurant.id,
					address: restaurant.address

				} );
			} );

		}

		response( restaurantsNameArray );

	} );
}


function otwWidgetToggles() {

	//Widget Display Options Toggle
	jQuery( '.widget-api-option .otw-method-span:not("clickable")' ).each( function () {

		jQuery( this ).addClass( "clickable" ).unbind( "click" ).click( function () {
			jQuery( this ).parent().parent().find( '.toggled' ).slideUp().removeClass( 'toggled' );
			jQuery( this ).find( 'input' ).attr( 'checked', 'checked' );
			if ( jQuery( this ).hasClass( 'single-option-wrap' ) ) {
				jQuery( this ).parent().next( '.otw-toggle-option-1' ).slideToggle().toggleClass( 'toggled' );
			} else if ( jQuery( this ).hasClass( 'multiple-option-wrap' ) ) {
				jQuery( this ).parent().next().next( '.otw-toggle-option-2' ).slideToggle().toggleClass( 'toggled' );
			} else {
				jQuery( this ).parent().next().next().next( '.otw-toggle-option-3' ).slideToggle().toggleClass( 'toggled' );
			}
		} );
	} );


	//Advanced Options Toggle (Bottom-gray panels)
	jQuery( '.otw-widget-toggler:not("clickable")' ).each( function () {

		jQuery( this ).addClass( "clickable" ).unbind( "click" ).click( function () {
			jQuery( this ).toggleClass( 'toggled' );
			jQuery( this ).next().slideToggle();
		} )

	} );


}


/**
 * Helper Function
 */
function split( val ) {
	return val.split( /,\s*/ );
}
/**
 * Helper Function
 */
function extractLast( term ) {
	return split( term ).pop();
}


//Tooltips
function otwWidgetTooltips() {
	//Tooltips for admins
	jQuery( '.tooltip-info' ).tipsy( {
		fade    : true,
		html    : true,
		gravity : 's',
		delayOut: 1000,
		delayIn : 500
	} );
}

// tipsy, facebook style tooltips for jquery
// version 1.0.0a
// (c) 2008-2010 jason frame [jason@onehackoranother.com]
// released under the MIT licence

(function ( $ ) {

	function maybeCall( thing, ctx ) {
		return (typeof thing == 'function') ? (thing.call( ctx )) : thing;
	};

	function isElementInDOM( ele ) {
		while ( ele = ele.parentNode ) {
			if ( ele == document ) return true;
		}
		return false;
	};

	function Tipsy( element, options ) {
		this.$element = $( element );
		this.options = options;
		this.enabled = true;
		this.fixTitle();
	};

	Tipsy.prototype = {
		show: function () {
			var title = this.getTitle();
			if ( title && this.enabled ) {
				var $tip = this.tip();

				$tip.find( '.tipsy-inner' )[this.options.html ? 'html' : 'text']( title );
				$tip[0].className = 'tipsy'; // reset classname in case of dynamic gravity
				$tip.remove().css( {
					top       : 0,
					left      : 0,
					visibility: 'hidden',
					display   : 'block'
				} ).prependTo( document.body );

				var pos = $.extend( {}, this.$element.offset(), {
					width : this.$element[0].offsetWidth,
					height: this.$element[0].offsetHeight
				} );

				var actualWidth = $tip[0].offsetWidth,
					actualHeight = $tip[0].offsetHeight,
					gravity = maybeCall( this.options.gravity, this.$element[0] );

				var tp;
				switch ( gravity.charAt( 0 ) ) {
					case 'n':
						tp = {
							top : pos.top + pos.height + this.options.offset,
							left: pos.left + pos.width / 2 - actualWidth / 2
						};
						break;
					case 's':
						tp = {
							top : pos.top - actualHeight - this.options.offset,
							left: pos.left + pos.width / 2 - actualWidth / 2
						};
						break;
					case 'e':
						tp = {
							top : pos.top + pos.height / 2 - actualHeight / 2,
							left: pos.left - actualWidth - this.options.offset
						};
						break;
					case 'w':
						tp = {
							top : pos.top + pos.height / 2 - actualHeight / 2,
							left: pos.left + pos.width + this.options.offset
						};
						break;
				}

				if ( gravity.length == 2 ) {
					if ( gravity.charAt( 1 ) == 'w' ) {
						tp.left = pos.left + pos.width / 2 - 15;
					} else {
						tp.left = pos.left + pos.width / 2 - actualWidth + 15;
					}
				}

				$tip.css( tp ).addClass( 'tipsy-' + gravity );
				$tip.find( '.tipsy-arrow' )[0].className = 'tipsy-arrow tipsy-arrow-' + gravity.charAt( 0 );
				if ( this.options.className ) {
					$tip.addClass( maybeCall( this.options.className, this.$element[0] ) );
				}

				if ( this.options.fade ) {
					$tip.stop().css( {
						opacity   : 0,
						display   : 'block',
						visibility: 'visible'
					} ).animate( {opacity: this.options.opacity} );
				} else {
					$tip.css( {visibility: 'visible', opacity: this.options.opacity} );
				}
			}
		},

		hide: function () {
			if ( this.options.fade ) {
				this.tip().stop().fadeOut( function () {
					$( this ).remove();
				} );
			} else {
				this.tip().remove();
			}
		},

		fixTitle: function () {
			var $e = this.$element;
			if ( $e.attr( 'title' ) || typeof($e.attr( 'original-title' )) != 'string' ) {
				$e.attr( 'original-title', $e.attr( 'title' ) || '' ).removeAttr( 'title' );
			}
		},

		getTitle: function () {
			var title, $e = this.$element, o = this.options;
			this.fixTitle();
			var title, o = this.options;
			if ( typeof o.title == 'string' ) {
				title = $e.attr( o.title == 'title' ? 'original-title' : o.title );
			} else if ( typeof o.title == 'function' ) {
				title = o.title.call( $e[0] );
			}
			title = ('' + title).replace( /(^\s*|\s*$)/, "" );
			return title || o.fallback;
		},

		tip: function () {
			if ( !this.$tip ) {
				this.$tip = $( '<div class="tipsy"></div>' ).html( '<div class="tipsy-arrow"></div><div class="tipsy-inner"></div>' );
				this.$tip.data( 'tipsy-pointee', this.$element[0] );
			}
			return this.$tip;
		},

		validate: function () {
			if ( !this.$element[0].parentNode ) {
				this.hide();
				this.$element = null;
				this.options = null;
			}
		},

		enable       : function () {
			this.enabled = true;
		},
		disable      : function () {
			this.enabled = false;
		},
		toggleEnabled: function () {
			this.enabled = !this.enabled;
		}
	};

	$.fn.tipsy = function ( options ) {

		if ( options === true ) {
			return this.data( 'tipsy' );
		} else if ( typeof options == 'string' ) {
			var tipsy = this.data( 'tipsy' );
			if ( tipsy ) tipsy[options]();
			return this;
		}

		options = $.extend( {}, $.fn.tipsy.defaults, options );

		function get( ele ) {
			var tipsy = $.data( ele, 'tipsy' );
			if ( !tipsy ) {
				tipsy = new Tipsy( ele, $.fn.tipsy.elementOptions( ele, options ) );
				$.data( ele, 'tipsy', tipsy );
			}
			return tipsy;
		}

		function enter() {
			var tipsy = get( this );
			tipsy.hoverState = 'in';
			if ( options.delayIn == 0 ) {
				tipsy.show();
			} else {
				tipsy.fixTitle();
				setTimeout( function () {
					if ( tipsy.hoverState == 'in' ) tipsy.show();
				}, options.delayIn );
			}
		};

		function leave() {
			var tipsy = get( this );
			tipsy.hoverState = 'out';
			if ( options.delayOut == 0 ) {
				tipsy.hide();
			} else {
				setTimeout( function () {
					if ( tipsy.hoverState == 'out' ) tipsy.hide();
				}, options.delayOut );
			}
		};

		if ( !options.live ) this.each( function () {
			get( this );
		} );

		if ( options.trigger != 'manual' ) {
			var binder = options.live ? 'live' : 'bind',
				eventIn = options.trigger == 'hover' ? 'mouseenter' : 'focus',
				eventOut = options.trigger == 'hover' ? 'mouseleave' : 'blur';
			this[binder]( eventIn, enter )[binder]( eventOut, leave );
		}

		return this;

	};

	$.fn.tipsy.defaults = {
		className: null,
		delayIn  : 0,
		delayOut : 0,
		fade     : false,
		fallback : '',
		gravity  : 'n',
		html     : false,
		live     : false,
		offset   : 0,
		opacity  : 0.8,
		title    : 'title',
		trigger  : 'hover'
	};

	$.fn.tipsy.revalidate = function () {
		$( '.tipsy' ).each( function () {
			var pointee = $.data( this, 'tipsy-pointee' );
			if ( !pointee || !isElementInDOM( pointee ) ) {
				$( this ).remove();
			}
		} );
	};

	// Overwrite this method to provide options on a per-element basis.
	// For example, you could store the gravity in a 'tipsy-gravity' attribute:
	// return $.extend({}, options, {gravity: $(ele).attr('tipsy-gravity') || 'n' });
	// (remember - do not modify 'options' in place!)
	$.fn.tipsy.elementOptions = function ( ele, options ) {
		return $.metadata ? $.extend( {}, options, $( ele ).metadata() ) : options;
	};

	$.fn.tipsy.autoNS = function () {
		return $( this ).offset().top > ($( document ).scrollTop() + $( window ).height() / 2) ? 's' : 'n';
	};

	$.fn.tipsy.autoWE = function () {
		return $( this ).offset().left > ($( document ).scrollLeft() + $( window ).width() / 2) ? 'e' : 'w';
	};

	/**
	 * yields a closure of the supplied parameters, producing a function that takes
	 * no arguments and is suitable for use as an autogravity function like so:
	 *
	 * @param margin (int) - distance from the viewable region edge that an
	 *        element should be before setting its tooltip's gravity to be away
	 *        from that edge.
	 * @param prefer (string, e.g. 'n', 'sw', 'w') - the direction to prefer
	 *        if there are no viewable region edges effecting the tooltip's
	 *        gravity. It will try to vary from this minimally, for example,
	 *        if 'sw' is preferred and an element is near the right viewable
	 *        region edge, but not the top edge, it will set the gravity for
	 *        that element's tooltip to be 'se', preserving the southern
	 *        component.
	 */
	$.fn.tipsy.autoBounds = function ( margin, prefer ) {
		return function () {
			var dir = {ns: prefer[0], ew: (prefer.length > 1 ? prefer[1] : false)},
				boundTop = $( document ).scrollTop() + margin,
				boundLeft = $( document ).scrollLeft() + margin,
				$this = $( this );

			if ( $this.offset().top < boundTop ) dir.ns = 'n';
			if ( $this.offset().left < boundLeft ) dir.ew = 'w';
			if ( $( window ).width() + $( document ).scrollLeft() - $this.offset().left < margin ) dir.ew = 'e';
			if ( $( window ).height() + $( document ).scrollTop() - $this.offset().top < margin ) dir.ns = 's';

			return dir.ns + (dir.ew ? dir.ew : '');
		}
	};

})( jQuery );