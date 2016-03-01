<?php
/**
 *  OTW Widget Frontend Display
 *
 * @description: Responsible for the frontend display of the Open Table Widget
 * @since      : 1.0
 * @created    : 9/10/13
 */
?>

<div class="otw-widget-form-wrap">
	<?php
	//Pre Form Content
	if ( ! empty( $preContent ) ) {
		?>
		<div class="otw-pre-form-content">
			<?php echo wpautop( $preContent ); ?>
		</div>
	<?php
	}
	$open_table_widget = new Open_Table_Widget();
	//Widget ID
	$args['widget_id'] = empty( $args['widget_id'] ) ? rand( 1, 9999 ) : $args['widget_id'];

	//Get Widget Res Data
	$reservationData = $open_table_widget->get_restaurant_data( $widgetLanguage ); ?>

	<form method="get" class="otw-widget-form" action="<?php echo $reservationData['action']; ?>" target="_blank">
		<div class="otw-wrapper">

			<?php

			/**
			 * Display Multiple Restaurants in Select
			 * if option set in widget
			 */
			if ( $displayOption === '1' ) {

				if ( ! empty( $restaurantIDs ) ) {
					$restaurantIDs = explode( ',', $restaurantIDs );
					?>
					<div class="otw-restaurant-wrap otw-input-wrap">
						<?php if ( $hideLabels !== '1' ) { ?>
							<label for="restaurant-<?php echo $args["widget_id"]; ?>"><?php
								if ( empty( $labelMultiple ) ) {
									_e( 'Select a Restaurant', 'open-table-widget' );
								} else {
									echo $labelMultiple;
								}
								?></label>
						<?php } ?>
						<select id="restaurant-<?php echo $args["widget_id"]; ?>" name="Restaurant" class="otw-reservation-restaurant selectpicker">
							<option value=""><?php _e( 'Select...', 'open-table-widget' ); ?></option>

							<?php foreach ( $restaurantIDs as $restaurant ) {
								$restaurantData = explode( '|', $restaurant ); ?>

								<option value="<?php echo $restaurantData[1]; ?>"><?php echo $restaurantData[0]; ?></option>

							<?php } ?>

						</select>

					</div>
				<?php } else { ?>
					<p class="otw-error otw-alert"><?php _e( 'Error: Restaurant IDs not properly input. Please check restaurant IDs field.', 'open-table-widget' ); ?></p>
				<?php } ?>
			<?php
			} //User Select List from City Options
			elseif ( $displayOption === '2' ) {

				//Compare selected cities list with transient
				$otwSelectedCityTransients = get_transient( 'otw_selected_cities' );


				//Check match and reset transient if not equal
				if ( $otwSelectedCityTransients !== $displayOption ) {
					//set selected cities transient
					set_transient( 'otw_selected_cities', $lookupCity, 12 * 12 * HOUR_IN_SECONDS );

				}


				//Get Admin selected Cities
				$cities = explode( ', ', $lookupCity );
				//			$restaurantArray = array();
				?>
				<div class="otw-input-wrap">
					<?php if ( $hideLabels !== '1' ) { ?>
						<label for="otw-city-<?php echo $args["widget_id"]; ?>"><?php
							if ( empty( $labelCity ) ) {
								_e( 'Select a City', 'open-table-widget' );
							} else {
								echo $labelCity;
							}
							?></label>
					<?php } ?>
					<select id="otw-city-<?php echo $args["widget_id"]; ?>" name="City" class="otw-reservation-city selectpicker">
						<option value=""><?php _e( 'Select a city...', 'open-table-widget' ); ?></option>

						<?php
						//loop through cities and query available restaurants
						foreach ( $cities as $city ) {

							if ( $city ) {
								?>
								<option value="<?php echo $city; ?>"><?php echo $city; ?></option>
							<?php } //endif city ?>
						<?php } //end foreach  ?>
					</select>
				</div>

				<div class="otw-input-wrap otw-restaurant-find-wrap">
					<?php if ( $hideLabels !== '1' ) { ?>
						<label for="otw-city-rest-<?php echo $args["widget_id"]; ?>"><?php
							if ( empty( $labelCityRest ) ) {
								_e( 'Find a Restaurant', 'open-table-widget' );
							} else {
								echo $labelCityRest;
							}
							?></label>
					<?php } ?>
					<input type="text" name="city-restaurant" placeholder="<?php _e( 'Restaurant Name', 'open-table-widget' ); ?>" class="otw-restaurant-autocomplete" />

				</div>

			<?php } //endif is multi-city option ?>
			<div class="otw-date-wrap otw-input-wrap">
				<?php if ( $hideLabels !== '1' ) { ?>
					<label for="date-<?php echo $args["widget_id"]; ?>"><?php
						if ( empty( $labelDate ) ) {
							_e( 'Date', 'open-table-widget' );
						} else {
							echo $labelDate;
						}
						?></label>
				<?php } ?>
				<input id="date-<?php echo $args["widget_id"]; ?>" name="startDate" class="otw-reservation-date" type="text" value="" autocomplete="off" data-date-format="<?php echo $reservationData['date_format']; ?>">
			</div>
			<div class="otw-time-wrap otw-input-wrap">
				<?php if ( $hideLabels !== '1' ) { ?>
					<label for="time-<?php echo $args["widget_id"]; ?>"><?php if ( empty( $labelTime ) ) {
							_e( 'Time', 'open-table-widget' );
						} else {
							echo $labelTime;
						} ?></label>
				<?php } ?>
				<?php
				//Time Select
				$timeDefault = ! empty( $timeDefault ) ? $timeDefault : '7:00PM';    ?>

				<select id="time-<?php echo $args["widget_id"]; ?>" name="ResTime" class="otw-reservation-time selectpicker">
					<?php
					//Time Options output
					$open_table_widget->open_table_reservaton_times( $timeStart, $timeEnd, $timeDefault, $reservationData['time_format'], $reservationData['time_format_val'], $timeIncrement ); ?>
				</select>

			</div>
			<div class="otw-party-size-wrap otw-input-wrap">
				<?php
				if ( $hideLabels !== '1' ) { ?>
					<label for="party-<?php echo $args["widget_id"]; ?>"><?php if ( empty( $labelParty ) ) {
							_e( 'Party Size', 'open-table-widget' );
						} else {
							echo $labelParty;
						}  ?></label>
				<?php } ?>

				<select id="party-<?php echo $args["widget_id"]; ?>" name="partySize" class="otw-party-size-select selectpicker">
					<?php
					foreach ( range( 1, $maxSeats ) as $seat ) {

						?>
						<option value="<?php echo $seat; ?>"
							<?php if ( $partySize == $seat ) {
								echo 'selected="selected"';
							} ?>><?php echo $seat; ?></option>
					<?php
					}

					?>
				</select>

			</div>

			<div class="otw-button-wrap">
				<input type="submit" class="<?php echo( $style == 'otw-bare-bones-style' ? 'otw-submit' : 'otw-submit-btn' ); ?>" value="<?php  if ( ! empty( $inputSubmit ) ) {
					echo $inputSubmit;
				} else {
					_e( 'Find a Table', 'open-table-widget' );
				}  ?>" />
			</div>
			<input type="hidden" name="RestaurantID" class="RestaurantID" value="<?php echo $restaurantID; ?>">
			<input type="hidden" name="rid" class="rid" value="<?php echo $restaurantID; ?>">
			<input type="hidden" name="GeoID" class="GeoID" value="0">
			<input type="hidden" name="txtDateFormat" class="txtDateFormat" value="<?php echo $reservationData['date_format']; ?>">
			<input type="hidden" name="RestaurantReferralID" class="RestaurantReferralID" value="<?php echo $restaurantID; ?>">
		</div>
	</form>
	<?php
	//Post Form Content
	if ( ! empty( $postContent ) ) {
		?>
		<div class="otw-post-form-content">
			<?php echo wpautop( $postContent ); ?>
		</div>
	<?php } ?>
	<div class="powered-by-open-table">
		<span class="powered-by-text"><?php _e( 'Powered By:', 'open-table-widget' ); ?></span></div>
</div><!-- /.otw-widget-form-wrap -->