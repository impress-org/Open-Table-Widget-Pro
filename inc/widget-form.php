<?php
/*
 *  @description: Widget form options in WP-Admin
 *  @since 1.0
 *  @created: 08/08/13
 */

?>

<!-- Title --><p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title' ); ?>:</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<!-- Listing Options --><p class="widget-api-option">
	<label for="<?php echo $this->get_field_id( 'display_option' ); ?>"><?php _e( 'Display Option', 'otw' ); ?>:</label><br />

    <span class="otw-method-span single-option-wrap">
        <input type="radio" name="<?php echo $this->get_field_name( 'display_option' ); ?>" class="<?php echo $this->get_field_id( 'display_option' ); ?> search-api-option" value="0" <?php checked( '0', $displayOption ); ?>><span class="otw-method-label"><?php _e( 'Single Restaurant Reservation', 'otw' ); ?>
				<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'This option will only allow reservations for a single selected restaurant.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></span>
    </span><br />
    <span class="otw-method-span multiple-option-wrap">
    <input type="radio" name="<?php echo $this->get_field_name( 'display_option' ); ?>" class="<?php echo $this->get_field_id( 'display_option' ); ?> business-api-option" value="1" <?php checked( '1', $displayOption ); ?>><span class="otw-method-label"><?php _e( 'Predefined Restaurants', 'otw' ); ?>
				<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'This option will allow reservations for multiple predefined restaurants.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></span>
    </span><br />
    <span class="otw-method-span user-option-wrap">
        <input type="radio" name="<?php echo $this->get_field_name( 'display_option' ); ?>" class="<?php echo $this->get_field_id( 'display_option' ); ?> search-api-option" value="2" <?php checked( '2', $displayOption ); ?>><span class="otw-method-label"><?php _e( 'User Lookup Reservations', 'otw' ); ?>
				<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'This option will allow the user to select a city and then lookup restaurants for reservations within their chosen city.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></span>
    </span>
</p>


<div class="otw-toggle-option-1 toggle-item <?php if ( $displayOption == "0" ) {
	echo 'toggled';
} ?>">

	<p class="otw-usage-description"><?php _e( '<span>Usage Description: </span>Select a single restaurant for reservations.', 'otw' ); ?></p>


	<!-- Restaurant Name -->
	<p>
		<label for="<?php echo $this->get_field_id( 'restaurant_name' ); ?>"><?php _e( 'Restaurant Name', 'otw' ); ?>:</label>
		<input class="widefat otw-auto-complete-1" id="<?php echo $this->get_field_id( 'restaurant_name' ); ?>" name="<?php echo $this->get_field_name( 'restaurant_name' ); ?>" type="text" placeholder="<?php _e( 'Type Restaurant Name', 'otw' ); ?>" value="<?php echo $restaurantName; ?>" />
	</p>

	<!-- Restaurant ID --><p>
		<label for="<?php echo $this->get_field_id( 'restaurant_id' ); ?>"><?php _e( 'Open Table Restaurant ID:', 'otw' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'This is your Open Table Restaurant ID used for reservations. Use the search field above to locate your restaurant.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></label>
		<input class="widefat restaurant-id" id="<?php echo $this->get_field_id( 'restaurant_id' ); ?>" name="<?php echo $this->get_field_name( 'restaurant_id' ); ?>" type="text" value="<?php echo $restaurantID; ?>" />
		<span class="otw-small-descption"><a href="http://wordimpress.com/docs/open-table-widget/#finding-your-open-table-restaurant-id" target="_blank" title="View tutorial" class="new-window">Need help finding your restaurant ID?</a></span>

	</p>

</div>


<div class="otw-toggle-option-2 toggle-item <?php if ( $displayOption == "1" ) {
	echo 'toggled';
} ?>">

	<p class="otw-usage-description"><?php _e( '<span>Usage Description: </span>Create a list of restaurants for users to select from when making reservations. Drag and drop to reorder the restaurants.', 'otw' ); ?></p>

	<!-- Restaurant Names -->
	<p>
		<label for="<?php echo $this->get_field_id( 'restaurant_names' ); ?>"><?php _e( 'Add Restaurant', 'otw' ); ?>:<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'Lookup a Restaurant by their ID to add to the list of available restaurants below.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></label>
		<input class="widefat otw-auto-complete-2" id="<?php echo $this->get_field_id( 'restaurant_names' ); ?>" name="<?php echo $this->get_field_name( 'restaurant_names' ); ?>" type="text" placeholder="<?php _e( 'Type Restaurant Name', 'otw' ); ?>" />
	</p>

	<!-- Restaurant IDs -->
	<p style="margin:0;padding:0;">
		<label for="<?php echo $this->get_field_id( 'restaurant_ids' ); ?>"><?php _e( 'Open Table Restaurants:', 'otw' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'Use the Restaurant ID lookup field above to find restaurants and their corresponding IDs. Drag-and-drop the restaurants below to order how they appear in the widget select. Use this field to fine tune as needed.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></label>
	</p>


	<div class="sortable-wrap restaurant-ids-wrap">
		<input class="widefat restaurant-ids-hidden" id="<?php echo $this->get_field_id( 'restaurant_ids' ); ?>" name="<?php echo $this->get_field_name( 'restaurant_ids' ); ?>" type="text" value="<?php echo $restaurantIDs; ?>" />


		<ul class="sortable">
			<?php
			$restaurantsArray = explode( ',', $restaurantIDs );
			foreach ( $restaurantsArray as $restaurant ) {
				$restaurantData = explode( '|', $restaurant );

				if ( ! empty( $restaurantData[0] ) ) {
					?>

					<li class="ui-state-default" id="<?php echo $restaurantData[0]; ?>|<?php echo $restaurantData[1]; ?>">
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

	<p class="otw-usage-description"><?php _e( '<span>Usage Description:</span>Allow the user to select from a list of cities and then search for specific restaurants to create a reservation.', 'otw' ); ?></p>

	<!-- Lookup City -->
	<p>
		<label for="<?php echo $this->get_field_id( 'lookup_city' ); ?>"><?php _e( 'City', 'otw' ); ?>:<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'Add a user restaurant lookup based on Open Table restaurant cities. Type the name of the cities you wish to display below separated by commas.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></label>
		<input class="widefat otw-auto-complete-3" id="<?php echo $this->get_field_id( 'lookup_city' ); ?>" name="<?php echo $this->get_field_name( 'lookup_city' ); ?>" type="text" placeholder="<?php _e( 'Type City Name', 'otw' ); ?>" value="<?php echo $lookupCity; ?>" />
	</p>


</div>


<h4 class="otw-widget-toggler"><?php _e( 'Display Options', 'otw' ); ?>:<span></span></h4>

<div class="display-options toggle-item">


	<!-- Widget Theme -->
	<p>
		<label for="<?php echo $this->get_field_id( 'widget_style' ); ?>"><?php _e( 'Widget Theme' ); ?>:</label>
		<select name="<?php echo $this->get_field_name( 'widget_style' ); ?>" id="#" class="widefat profield">
			<?php
			$options = array( __( 'Bare Bones', 'otw' ), __( 'Minimal Light', 'otw' ), __( 'Minimal Dark', 'otw' ), __( 'Shadow Light', 'otw' ), __( 'Shadow Dark', 'otw' ), __( 'Inset Light', 'otw' ), __( 'Inset Dark', 'otw' ) );
			//Counter for Option Values
			$counter = 0;

			foreach ( $options as $option ) {
				echo '<option value="' . $option . '" id="' . $option . '"', $widgetStyle == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				$counter ++;
			}
			?>
		</select>
	</p>


	<!-- Hide Form Labels -->
	<p>
		<input id="<?php echo $this->get_field_id( 'hide_labels' ); ?>" class="reviews-toggle" name="<?php echo $this->get_field_name( 'hide_labels' ); ?>" type="checkbox" value="1" <?php checked( '1', $hideLabels ); ?>/>
		<label for="<?php echo $this->get_field_id( 'hide_labels' ); ?>"><?php _e( 'Hide Form Labels', 'otw' ); ?>
			<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'The labels appear above the reservation form inputs. Check this option if you would like to hide the labels.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></label>
	</p>


</div>

<h4 class="otw-widget-toggler"><?php _e( 'Content Options:', 'otw' ); ?><span></span></h4>

<div class="display-options toggle-item">

	<!-- Widget Language -->
	<p>
		<label for="<?php echo $this->get_field_id( 'widget_language' ); ?>"><?php _e( 'Location and Language' ); ?>:<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'Provide the location and language of the restaurant(s) to send users on Open Table. The restaurant(s) you select must be within the location provided or the reservation link will not work. Be sure to test thoroughly.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></label>
		<select name="<?php echo $this->get_field_name( 'widget_language' ); ?>" id="#" class="widefat profield">
			<?php
			$options = array(
				array(
					__( 'ca-eng', 'otw' ),
					__( 'Canada - English', 'otw' )
				),
//              Can't find any restaurants using French Canada
//                array(
//                    __('ca-fre', 'otw'),
//                    __('Canada - French', 'otw')
//                ),
				array(
					__( 'ger-eng', 'otw' ),
					__( 'Germany - English', 'otw' )
				),
				array(
					__( 'ger-ger', 'otw' ),
					__( 'Germany - German', 'otw' )
				),
				array(
					__( 'uk', 'otw' ),
					__( 'United Kingdom', 'otw' )
				),
				array(
					__( 'us', 'otw' ),
					__( 'United States', 'otw' )
				),
				array(
					__( 'jp-eng', 'otw' ),
					__( 'Japan - English', 'otw' )
				),
				array(
					__( 'jp-jp', 'otw' ),
					__( 'Japan - Japanese', 'otw' )
				),
				array(
					__( 'mx-eng', 'otw' ),
					__( 'Mexico - English', 'otw' )
				),
				array(
					__( 'mx-eng', 'otw' ),
					__( 'Mexico - Spanish', 'otw' )
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
		<label for="<?php echo $this->get_field_id( 'label_multiple' ); ?>"><?php _e( 'Predefined Restaurants Label', 'otw' ); ?>:<img src="<?php echo OTW_PLUGIN_URL . '/assets/images/help.png' ?>" title="<?php _e( 'Only displays when Predefined Restaurants display option is selected.', 'otw' ); ?>" class="tooltip-info" width="16" height="16" /></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_multiple' ); ?>" name="<?php echo $this->get_field_name( 'label_multiple' ); ?>" type="text" placeholder="<?php _e( 'Select a Restaurant', 'otw' ); ?>" value="<?php echo $labelMultiple; ?>" />
	</p>
	<!-- Select Cities Label -->
	<p>
		<label for="<?php echo $this->get_field_id( 'label_city' ); ?>"><?php _e( 'User Lookup Cities Label', 'otw' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_city' ); ?>" name="<?php echo $this->get_field_name( 'label_city' ); ?>" type="text" placeholder="<?php _e( 'Select a City', 'otw' ); ?>" value="<?php echo $labelCity; ?>" />
	</p>

	<!-- Date Label -->
	<p>
		<label for="<?php echo $this->get_field_id( 'label_date' ); ?>"><?php _e( 'Custom Date Label', 'otw' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_date' ); ?>" name="<?php echo $this->get_field_name( 'label_date' ); ?>" type="text" placeholder="<?php _e( 'Date', 'otw' ); ?>" value="<?php echo $labelDate; ?>" />
	</p>

	<!-- Time Label -->
	<p>
		<label for="<?php echo $this->get_field_id( 'label_time' ); ?>"><?php _e( 'Custom Time Label', 'otw' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_time' ); ?>" name="<?php echo $this->get_field_name( 'label_time' ); ?>" type="text" placeholder="<?php _e( 'Time', 'otw' ); ?>" value="<?php echo $labelTime; ?>" />
	</p>
	<!-- Party Size Label -->
	<p>
		<label for="<?php echo $this->get_field_id( 'label_party' ); ?>"><?php _e( 'Custom Party Size Label', 'otw' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'label_party' ); ?>" name="<?php echo $this->get_field_name( 'label_party' ); ?>" type="text" placeholder="<?php _e( 'Party Size', 'otw' ); ?>" value="<?php echo $labelParty; ?>" />
	</p>

	<!-- Submit Button Text -->
	<p>
		<label for="<?php echo $this->get_field_id( 'input_submit' ); ?>"><?php _e( 'Submit Button Text', 'otw' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'input_submit' ); ?>" name="<?php echo $this->get_field_name( 'input_submit' ); ?>" type="text" placeholder="<?php _e( 'Find a Table', 'otw' ); ?>" value="<?php echo $inputSubmit; ?>" />
	</p>


	<!-- Pre Widget Content -->
	<p>
		<label for="<?php echo $this->get_field_id( 'pre_content' ); ?>"><?php _e( 'Pre Form Content', 'otw' ); ?>:</label>
		<textarea class="widefat" id="#" name="<?php echo $this->get_field_name( 'pre_content' ); ?>" rows="3" cols="25"><?php echo $preContent; ?></textarea>
	</p>

	<!-- Post Widget Content -->
	<p>
		<label for="<?php echo $this->get_field_id( 'post_content' ); ?>"><?php _e( 'Post Form Content', 'otw' ); ?>:</label>
		<textarea class="widefat" id="#" name="<?php echo $this->get_field_name( 'post_content' ); ?>" rows="3" cols="25"><?php echo $postContent; ?></textarea>
	</p>

</div>

