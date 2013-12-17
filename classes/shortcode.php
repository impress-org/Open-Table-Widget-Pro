<?php
/**
 * Class Yelp_Shortcode
 *
 * @description: Yelp Main Shortcode Class
 * @since      : 1.2
 * @created    : 3/20/13
 */

class Open_Table_Widget_Shortcode extends Open_Table_Widget {

	/**
	 * Init shortcode
	 */
	function __construct() {
		parent::__construct();
		add_shortcode( 'open-table-widget', array( __CLASS__, 'handle_shortcode' ) );

	}

	function handle_shortcode( $atts ) {
		$open_table_widget = new Open_Table_Widget();
		//Only Load scripts when widget or shortcode is active
		$open_table_widget->add_otw_widget_scripts();


		$defaults = array(
			'title'          => 'Open Table Reservations',
			'display_option' => '1',
			'restaurant_id'  => '106672',
		);

		//extract shortcode arguments
		extract( shortcode_atts( $defaults, $atts ) );

		echo "<pre>";
		var_dump( $atts );
		echo "</pre>";

		$args = array();


		//Handle No Follow
//		$no_follow = check_shortcode_value( $no_follow );


		/*
		 * Set up our Widget instance array
		 */
		//Single Restaurant Reservations
		if ( ! empty( $atts['display_option'] ) && $atts['display_option'] === '1' ) {

			$instance = array(
				'title'          => $atts['title'],
				'display_option' => $atts['display_option'],
				'restaurant_id'  => $atts['restaurant_id'],

			);

		} //Search API
		elseif ( ! empty( $atts['term'] ) ) {

			$instance = array(
				'term' => empty( $atts['term'] ) ? '' : $atts['term'],

			);

		} //DEFAULTS (User has no args)
		elseif ( empty( $atts ) ) {

			$instance = $defaults;

		}

		echo "<pre>";
		var_dump( $instance );
		echo "</pre>";

		// actual shortcode handling here
		//Using ob_start to output shortcode within content appropriatly
		ob_start();
		parent::widget( $args, $instance );
		$shortcode = ob_get_contents();
		ob_end_clean();

		//Output our Widget
		return $shortcode;

	}

}

//Open_Table_Widget_Shortcode::init();

/*
 * Check Value
 *
 * Helper Function
 */
function check_shortcode_value( $attr ) {

	if ( $attr === "true" || $attr === "1" ) {
		$attr = "1";
	} else {
		$attr = '0';
	}

	return $attr;

}