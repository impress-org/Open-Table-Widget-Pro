<?php
/**
 * Class Open_Table_Widget_Shortcode
 *
 * @description: Open Table Shortcode Class
 * @since      : 1.0
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

		//Defaults shortcode vals
		$defaults = array(
			'title'           => 'Open Table Reservations',
			'display_option'  => '1',
			'restaurant_id'   => '49051',
			'restaurant_ids'  => '49051',
			'widget_style'    => 'minimal-light',
			'hide_labels'     => 'false',
			'lookup_city'     => 'San Diego',
			'widget_language' => 'ca-eng',
			'pre_content'     => '',
			'post_content'    => '',
			'label_multiple'  => '',
			'label_city'      => '',
			'label_date'      => '',
			'label_time'      => '',
			'label_party'     => '',
			'input_submit'    => '',
		);

		//extract shortcode arguments
		extract( shortcode_atts( $defaults, $atts ) );


		//declare variables
		$args = $instance = array();


		//Handle No Follow
		$hide_labels = check_shortcode_value( $hide_labels );


		/*
		 * Set up our Widget instance array
		 */
		//Single Restaurant Reservations
		if ( isset( $atts['display_option'] ) && $atts['display_option'] === '0' ) {

			$instance = array(
				'restaurant_id' => $atts['restaurant_id'],
			);


		} //Predefined Restaurants List
		elseif ( ! empty( $atts['display_option'] ) && $atts['display_option'] === '1' ) {

			$instance = array(
				'restaurant_ids' => $atts['restaurant_ids'],
			);

		} //City Lookup
		elseif ( ! empty( $atts['display_option'] ) && $atts['display_option'] === '2' ) {

			$instance = array(
				'lookup_city' => $atts['lookup_city'],
			);

		} //DEFAULTS (User has not properly set args)
		elseif ( empty( $atts ) || empty( $atts['display_option'] ) ) {

			$instance = $defaults;

		}

		//Global Options (non-dependant on display_option)
		$globals = array(
			'display_option'  => empty( $atts['display_option'] ) ? $display_option : $atts['display_option'], //default is 0
			'title'           => empty( $atts['title'] ) ? $title : $atts['title'],
			'hide_labels'     => $hide_labels,
			'widget_style'    => empty( $atts['widget_style'] ) ? $widget_style : $atts['widget_style'],
			'widget_language' => empty( $atts['widget_language'] ) ? $widget_language : $atts['widget_language'],
			'pre_content'     => empty( $atts['pre_content'] ) ? $pre_content : $atts['pre_content'],
			'post_content'    => empty( $atts['post_content'] ) ? $post_content : $atts['post_content'],
			'label_multiple'  => empty( $atts['label_multiple'] ) ? $label_multiple : $atts['label_multiple'],
			'label_city'      => empty( $atts['label_city'] ) ? $label_multiple : $atts['label_city'],
			'label_date'      => empty( $atts['label_date'] ) ? $label_date : $atts['label_date'],
			'label_time'      => empty( $atts['label_time'] ) ? $label_time : $atts['label_time'],
			'label_party'     => empty( $atts['label_party'] ) ? $label_party : $atts['label_party'],
			'input_submit'    => empty( $atts['input_submit'] ) ? $input_submit : $atts['input_submit'],
		);

		//merge instance with globals
		$instance = array_merge( $instance, $globals );

		// actual shortcode handling here
		//Using ob_start to output shortcode within content appropriately
		ob_start();
		parent::widget( $args, $instance );
		$shortcode = ob_get_contents();
		ob_end_clean();

		//Output our Widget
		return $shortcode;

	}

}


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