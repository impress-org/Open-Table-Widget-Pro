<?php
/*
 * Open Table Widget Admin Form
 *
 * Widget form options in WP-Admin
 */
?>

<!-- Title -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:', 'open-table-widget' ); ?>
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
	       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
</p>

<!-- Listing Options -->
<p class="widget-api-option">
	<label
		for="<?php echo $this->get_field_id( 'display_option' ); ?>"><?php _e( 'Display Option:', 'open-table-widget' ); ?>
	</label><br/>

	<span class="otw-method-span single-option-wrap">
        <input type="radio" name="<?php echo $this->get_field_name( 'display_option' ); ?>"
               class="<?php echo $this->get_field_id( 'display_option' ); ?> display-option-0"
               value="0" <?php checked( '0', $displayOption ); ?>><span
			class="otw-method-label"><?php _e( 'Single Restaurant Reservation', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'This option will only allow reservations for a single selected restaurant.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></span>
    </span><br/>
	<span class="otw-method-span multiple-option-wrap">
    <input type="radio" name="<?php echo $this->get_field_name( 'display_option' ); ?>"
           class="<?php echo $this->get_field_id( 'display_option' ); ?> display-option-1"
           value="1" <?php checked( '1', $displayOption ); ?>><span
			class="otw-method-label"><?php _e( 'Predefined Restaurants', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'This option will allow reservations for multiple predefined restaurants.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></span>
    </span><br/>
	<span class="otw-method-span user-option-wrap">
        <input type="radio" name="<?php echo $this->get_field_name( 'display_option' ); ?>"
               class="<?php echo $this->get_field_id( 'display_option' ); ?> display-option-2"
               value="2" <?php checked( '2', $displayOption ); ?>><span
			class="otw-method-label"><?php _e( 'User Lookup Reservations', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'This option will allow the user to select a city and then lookup restaurants for reservations within their chosen city.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></span>
    </span>
</p>


<div class="otw-toggle-option-1 toggle-item <?php if ( $displayOption == "0" ) {
	echo 'toggled';
} ?>">

	<p class="otw-usage-description"><?php _e( '<span>Usage Description: </span>Select a single restaurant for reservations.', 'open-table-widget' ); ?></p>

	<!-- Restaurant Name -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'restaurant_name' ); ?>"><?php _e( 'Restaurant Name:', 'open-table-widget' ); ?>
		</label>
		<input class="widefat otw-auto-complete-1" id="<?php echo $this->get_field_id( 'restaurant_name' ); ?>"
		       name="<?php echo $this->get_field_name( 'restaurant_name' ); ?>" type="text"
		       placeholder="<?php _e( 'Type Restaurant Name', 'open-table-widget' ); ?>"
		       value="<?php echo $restaurantName; ?>"/>
	</p>

	<!-- Restaurant ID -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'restaurant_id' ); ?>"><?php _e( 'Open Table Restaurant ID:', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'This is your Open Table Restaurant ID used for reservations. Use the search field above to locate your restaurant.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></label>
		<input class="widefat restaurant-id" id="<?php echo $this->get_field_id( 'restaurant_id' ); ?>"
		       name="<?php echo $this->get_field_name( 'restaurant_id' ); ?>" type="text"
		       value="<?php echo $restaurantID; ?>"/>
		<span class="otw-small-descption"><a
				href="https://wordimpress.com/documentation/open-table-widget/finding-your-open-table-restaurant-id/"
				target="_blank" title="View tutorial"
				class="new-window">Need help finding your restaurant ID?</a></span>
	</p>

</div>


<div class="otw-toggle-option-2 toggle-item <?php if ( $displayOption == "1" ) {
	echo 'toggled';
} ?>">

	<p class="otw-usage-description"><?php _e( '<span>Usage Description: </span>Create a list of restaurants for users to select from when making reservations. Drag and drop to reorder the restaurants.', 'open-table-widget' ); ?></p>

	<!-- Restaurant Names -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'restaurant_names' ); ?>"><?php _e( 'Add Restaurant:', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'Lookup a Restaurant by their ID to add to the list of available restaurants below.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></label>
		<input class="widefat otw-auto-complete-2" id="<?php echo $this->get_field_id( 'restaurant_names' ); ?>"
		       name="<?php echo $this->get_field_name( 'restaurant_names' ); ?>" type="text"
		       placeholder="<?php _e( 'Type Restaurant Name', 'open-table-widget' ); ?>"/>
	</p>

	<!-- Restaurant IDs -->
	<p style="margin:0;padding:0;">
		<label
			for="<?php echo $this->get_field_id( 'restaurant_ids' ); ?>"><?php _e( 'Open Table Restaurants:', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'Use the Restaurant ID lookup field above to find restaurants and their corresponding IDs. Drag-and-drop the restaurants below to order how they appear in the widget select. Use this field to fine tune as needed.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></label>
	</p>


	<div class="sortable-wrap restaurant-ids-wrap">
		<input class="widefat restaurant-ids-hidden" id="<?php echo $this->get_field_id( 'restaurant_ids' ); ?>"
		       name="<?php echo $this->get_field_name( 'restaurant_ids' ); ?>" type="text"
		       value="<?php echo $restaurantIDs; ?>"/>


		<ul class="sortable">
			<?php
			$restaurantsArray = explode( ',', $restaurantIDs );
			foreach ( $restaurantsArray as $restaurant ) {
				$restaurantData = explode( '|', $restaurant );

				if ( ! empty( $restaurantData[0] ) ) {
					?>

					<li class="ui-state-default"
					    id="<?php echo $restaurantData[0]; ?>|<?php echo $restaurantData[1]; ?>">
						<span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php echo $restaurantData[0]; ?>
						<span class="ui-icon ui-icon-close"></span></li>

				<?php } ?>

			<?php } ?>
		</ul>

	</div>


</div>

<div class="otw-toggle-option-3 toggle-item pro-only <?php if ( $displayOption == "2" ) {
	echo 'toggled';
} ?>">

	<p class="otw-usage-description"><?php _e( '<span>Usage Description:</span>Allow the user to select from a list of cities and then search for specific restaurants to make a reservation.', 'open-table-widget' ); ?></p>

	<!-- Lookup City -->
	<p>
		<label for="<?php echo $this->get_field_id( 'lookup_city' ); ?>"><?php _e( 'City:', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'Add a user restaurant lookup based on Open Table restaurant cities. Type the name of the cities you wish to display below separated by commas.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></label>
		<input class="widefat otw-auto-complete-3" id="<?php echo $this->get_field_id( 'lookup_city' ); ?>"
		       name="<?php echo $this->get_field_name( 'lookup_city' ); ?>" type="text"
		       placeholder="<?php _e( 'Type City Name', 'open-table-widget' ); ?>" value="<?php echo $lookupCity; ?>"/>
	</p>


</div>


<h4 class="otw-widget-toggler"><?php _e( 'Display Options', 'open-table-widget' ); ?>:<span></span></h4>

<div class="display-options toggle-item">


	<!-- Widget Theme -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'widget_style' ); ?>"><?php _e( 'Widget Theme:', 'open-table-widget' ); ?>
		</label>
		<select name="<?php echo $this->get_field_name( 'widget_style' ); ?>" class="widefat profield">
			<?php
			$options = array(
				__( 'Bare Bones', 'open-table-widget' ),
				__( 'Minimal Light', 'open-table-widget' ),
				__( 'Minimal Dark', 'open-table-widget' ),
				__( 'Shadow Light', 'open-table-widget' ),
				__( 'Shadow Dark', 'open-table-widget' ),
				__( 'Inset Light', 'open-table-widget' ),
				__( 'Inset Dark', 'open-table-widget' )
			);
			//Counter for Option Values
			$counter = 0;

			foreach ( $options as $option ) {
				echo '<option value="' . $option . '" id="' . $option . '"', $widgetStyle == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				$counter ++;
			}
			?>
		</select>
	</p>

	<!-- Time Range -->

	<div class="time-range-wrap clearfix">
		<div class="time-range-left">
			<p>
				<label
					for="<?php echo $this->get_field_id( 'time_start' ); ?>"><?php _e( 'Time Start:', 'open-table-widget' ); ?>
					<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
					     title="<?php _e( 'The reservation time select start value. Please ensure this value is before the Time End value.', 'open-table-widget' ); ?>"
					     class="tooltip-info" width="16" height="16"/></label>
				<?php
				//Time loop
				$start = '12AM';
				$end   = '11:45PM';

				//Get language set in widget and in global options
				$language = 'us';
				if ( ! empty( $widgetLanguage ) ) {
					$language = $widgetLanguage;
				} elseif ( ! empty( $this->options['default-location'] ) ) {
					$language = $this->options['default-location'];
				}
				$reservationData = $this->get_restaurant_data( $language );

				//Set Time Format according to options
				$timeFormat = $timeFormatVal = 'g:ia';
				if ( ! empty( $reservationData['time_format'] ) ) {
					$timeFormat = $reservationData['time_format'];
				}
				if ( ! empty( $reservationData['time_format_val'] ) ) {
					$timeFormat = $reservationData['time_format_val'];
				}
				//Output time select
				?>
				<select name="<?php echo $this->get_field_name( 'time_start' ); ?>" class="widefat profield">
					<?php
					$this->open_table_reservaton_times( $start, $end, $timeStart, $timeFormat, $timeFormatVal, $timeIncrement );
					?>
				</select></p>
		</div>

		<div class="time-range-right">
			<p>
				<label
					for="<?php echo $this->get_field_id( 'time_end' ); ?>"><?php _e( 'Time End:', 'open-table-widget' ); ?>
					<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
					     title="<?php _e( 'The reservation time select end value. Please ensure this value is after the Time Start value.', 'open-table-widget' ); ?>"
					     class="tooltip-info" width="16" height="16"/></label>
				<?php
				//Time loop
				$start = ! empty( $timeStart ) ? $timeStart : '12AM';
				$end   = '11:00PM';

				?>
				<select name="<?php echo $this->get_field_name( 'time_end' ); ?>" class="widefat profield">
					<?php
					$this->open_table_reservaton_times( $start, $end, $timeEnd, $timeFormat, $timeFormatVal, $timeIncrement );
					?>
				</select></p>
		</div>
	</div>

	<!-- Time Range 2 -->
	<div class="time-range-wrap clearfix">
		<div class="time-range-left field-left">
			<p>
				<label
					for="<?php echo $this->get_field_id( 'time_default' ); ?>"><?php _e( 'Default Time:', 'open-table-widget' ); ?>
					<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
					     title="<?php _e( 'This is the default reservation time selected.', 'open-table-widget' ); ?>"
					     class="tooltip-info" width="16" height="16"/></label>
				<?php
				//Time loop
				$start = ! empty( $timeStart ) ? $timeStart : '12AM';
				$end   = ! empty( $timeEnd ) ? $timeEnd : '11:59PM';
				//Output time select
				?>
				<select name="<?php echo $this->get_field_name( 'time_default' ); ?>" class="widefat profield">
					<?php
					$this->open_table_reservaton_times( $start, $end, $timeDefault, $timeFormat, $timeFormatVal, $timeIncrement );
					?>
				</select></p>
		</div>

		<div class="time-range-right field-right">
			<p>
				<label
					for="<?php echo $this->get_field_id( 'time_increment' ); ?>"><?php _e( 'Time Increment:', 'open-table-widget' ); ?>
					<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
					     title="<?php _e( 'This option effects many reservations per hour are displayed within the reservation time select.', 'open-table-widget' ); ?>"
					     class="tooltip-info" width="16" height="16"/></label>

				<select name="<?php echo $this->get_field_name( 'time_increment' ); ?>" class="widefat profield">
					<?php
					$options = array(
						array(
							__( '15', 'open-table-widget' ),
							__( '15 Minutes', 'open-table-widget' ),
						),
						array(
							__( '30', 'open-table-widget' ),
							__( '30 Minutes', 'open-table-widget' ),
						),
						array(
							__( '60', 'open-table-widget' ),
							__( '1 Hour', 'open-table-widget' ),
						),
					);
					foreach ( $options as $option ) {
						echo '<option value="' . $option[0] . '" id="' . $option[0] . '"', $timeIncrement == $option[0] ? ' selected="selected"' : '', '>', $option[1], '</option>';
					}
					?>
				</select></p>

		</div>
	</div>

	<!-- Default Party Size -->
	<div class="part-size clearfix">

		<div class="default-party-size field-left">
			<p>
				<label
					for="<?php echo $this->get_field_id( 'party_size' ); ?>"><?php _e( 'Party Size:', 'open-table-widget' ); ?>
					<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
					     title="<?php _e( 'Set the default party size for this reservation widget.', 'open-table-widget' ); ?>"
					     class="tooltip-info" width="16" height="16"/></label>

				<input class="widefat" id="<?php echo $this->get_field_id( 'party_size' ); ?>"
				       name="<?php echo $this->get_field_name( 'party_size' ); ?>" type="number" placeholder="4"
				       value="<?php echo $partySize; ?>"/>
			</p>

		</div>


		<!-- Max Seats -->
		<div class="max-seats field-right">

			<p>
				<label
					for="<?php echo $this->get_field_id( 'max_seats' ); ?>"><?php _e( 'Seats Available:', 'open-table-widget' ); ?>
					<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
					     title="<?php _e( 'Here you can set the maximum number of seats you have available at your restaurant', 'open-table-widget' ); ?>"
					     class="tooltip-info" width="16" height="16"/></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'max_seats' ); ?>"
				       name="<?php echo $this->get_field_name( 'max_seats' ); ?>" type="number" placeholder="6"
				       value="<?php echo $maxSeats; ?>"/>
			</p>

		</div>

	</div>


	<!-- Hide Form Labels -->
	<p>
		<input id="<?php echo $this->get_field_id( 'hide_labels' ); ?>" class="reviews-toggle"
		       name="<?php echo $this->get_field_name( 'hide_labels' ); ?>" type="checkbox"
		       value="1" <?php checked( '1', $hideLabels ); ?>/>
		<label
			for="<?php echo $this->get_field_id( 'hide_labels' ); ?>"><?php _e( 'Hide Form Labels', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'The labels appear above the reservation form inputs. Check this option if you would like to hide the labels.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></label>
	</p>


</div>

<h4 class="otw-widget-toggler"><?php _e( 'Content Options:', 'open-table-widget' ); ?><span></span></h4>

<div class="display-options toggle-item">

	<!-- Widget Language -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'widget_language' ); ?>"><?php _e( 'Location and Language:', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'Provide the location and language of the restaurant(s) to send users on Open Table. The restaurant(s) you select must be within the location provided or the reservation link will not work. Be sure to test thoroughly.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></label>

		<select name="<?php echo $this->get_field_name( 'widget_language' ); ?>" id="#" class="widefat profield">
			<?php
			$options = array(
				array(
					__( 'ca-eng', 'open-table-widget' ),
					__( 'Canada - English', 'open-table-widget' )
				),
				array(
					__( 'ger-eng', 'open-table-widget' ),
					__( 'Germany - English', 'open-table-widget' )
				),
				array(
					__( 'ger-ger', 'open-table-widget' ),
					__( 'Germany - German', 'open-table-widget' )
				),
				array(
					__( 'uk', 'open-table-widget' ),
					__( 'United Kingdom', 'open-table-widget' )
				),
				array(
					__( 'us', 'open-table-widget' ),
					__( 'United States', 'open-table-widget' )
				),
				array(
					__( 'jp-eng', 'open-table-widget' ),
					__( 'Japan - English', 'open-table-widget' )
				),
				array(
					__( 'jp-jp', 'open-table-widget' ),
					__( 'Japan - Japanese', 'open-table-widget' )
				),
				array(
					__( 'mx-eng', 'open-table-widget' ),
					__( 'Mexico - English', 'open-table-widget' )
				),
				array(
					__( 'mx-eng', 'open-table-widget' ),
					__( 'Mexico - Spanish', 'open-table-widget' )
				),

			);
			//Counter for Option Values
			$counter = 0;

			foreach ( $options as $option ) {
				echo '<option value="' . $option[0] . '" id="' . $option[0] . '"';
				if ( empty( $widgetLanguage ) && ! empty( $this->options['default-location'] ) && $option[0] == $this->options['default-location'] ) {
					echo ' selected="selected" ';
				} elseif ( $widgetLanguage == $option[0] ) {
					echo ' selected="selected" ';
				}
				echo '>', $option[1], '</option>';

				$counter ++;
			}
			?>
		</select>
	</p>
	<!-- Select Predefined Restaurants Label -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'label_multiple' ); ?>"><?php _e( 'Predefined Restaurants Label:', 'open-table-widget' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>"
			     title="<?php _e( 'Only displays when Predefined Restaurants display option is selected.', 'open-table-widget' ); ?>"
			     class="tooltip-info" width="16" height="16"/></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_multiple' ); ?>"
		       name="<?php echo $this->get_field_name( 'label_multiple' ); ?>" type="text"
		       placeholder="<?php _e( 'Select a Restaurant', 'open-table-widget' ); ?>"
		       value="<?php echo $labelMultiple; ?>"/>
	</p>
	<!-- Select Cities Label -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'label_city' ); ?>"><?php _e( 'User Lookup Cities Label', 'open-table-widget' ); ?>
			:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_city' ); ?>"
		       name="<?php echo $this->get_field_name( 'label_city' ); ?>" type="text"
		       placeholder="<?php _e( 'Select a City', 'open-table-widget' ); ?>" value="<?php echo $labelCity; ?>"/>
	</p>

	<!-- Date Label -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'label_date' ); ?>"><?php _e( 'Custom Date Label:', 'open-table-widget' ); ?>
		</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_date' ); ?>"
		       name="<?php echo $this->get_field_name( 'label_date' ); ?>" type="text"
		       placeholder="<?php _e( 'Date', 'open-table-widget' ); ?>" value="<?php echo $labelDate; ?>"/>
	</p>

	<!-- Time Label -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'label_time' ); ?>"><?php _e( 'Custom Time Label:', 'open-table-widget' ); ?>
		</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_time' ); ?>"
		       name="<?php echo $this->get_field_name( 'label_time' ); ?>" type="text"
		       placeholder="<?php _e( 'Time', 'open-table-widget' ); ?>" value="<?php echo $labelTime; ?>"/>
	</p>
	<!-- Party Size Label -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'label_party' ); ?>"><?php _e( 'Custom Party Size Label:', 'open-table-widget' ); ?>
		</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_party' ); ?>"
		       name="<?php echo $this->get_field_name( 'label_party' ); ?>" type="text"
		       placeholder="<?php _e( 'Party Size', 'open-table-widget' ); ?>" value="<?php echo $labelParty; ?>"/>
	</p>

	<!-- Submit Button Text -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'input_submit' ); ?>"><?php _e( 'Submit Button Text:', 'open-table-widget' ); ?>
		</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'input_submit' ); ?>"
		       name="<?php echo $this->get_field_name( 'input_submit' ); ?>" type="text"
		       placeholder="<?php _e( 'Find a Table', 'open-table-widget' ); ?>" value="<?php echo $inputSubmit; ?>"/>
	</p>


	<!-- Pre Widget Content -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'pre_content' ); ?>"><?php _e( 'Pre Form Content:', 'open-table-widget' ); ?>
		</label>
		<textarea class="widefat" id="#" name="<?php echo $this->get_field_name( 'pre_content' ); ?>" rows="3"
		          cols="25"><?php echo $preContent; ?></textarea>
	</p>

	<!-- Post Widget Content -->
	<p>
		<label
			for="<?php echo $this->get_field_id( 'post_content' ); ?>"><?php _e( 'Post Form Content:', 'open-table-widget' ); ?>
		</label>
		<textarea class="widefat" id="#" name="<?php echo $this->get_field_name( 'post_content' ); ?>" rows="3"
		          cols="25"><?php echo $postContent; ?></textarea>
	</p>

</div>


<div class="powered-by">
	<p><?php _e( 'Powered by:', 'otw' ); ?></p>
	<img src="<?php echo OTW_PLUGIN_URL; ?>/assets/images/open-table-logo-transparent-150.png"
	     alt="Powered by Open Table"/>
</div>