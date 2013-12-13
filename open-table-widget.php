<?php
/*
Plugin Name: Open Table Widget
Plugin URI: http://wordimpress.com/plugins/open-table-widget/
Description: Display an Open Table reservation widget for your restaurant, bar, nightclub, hotel or eatery.
Version: 1.0
Author: Devin Walker
Author URI: http://imdev.in/
Text Domain: otw
*/


define( 'OTW_PLUGIN_NAME', 'open-table-widget' );
define( 'OTW_PLUGIN_NAME_PLUGIN', plugin_basename( __FILE__ ) );
define( 'OTW_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . OTW_PLUGIN_NAME );
define( 'OTW_PLUGIN_URL', WP_PLUGIN_URL . '/' . OTW_PLUGIN_NAME );

function init_open_table_widget() {

	// Include Core Framework class
	require_once 'classes/core.php';

	// Include Licensing
	require_once 'licence/licence.php';



	// Create plugin instance
	$open_table_widget = new WordImpress_Plugin_Framework( __FILE__ );

	// Include options set
	include_once 'inc/options.php';

	// Create options page
	$open_table_widget->add_options_page( array(), $open_table_widget_options );

	// Make plugin meta translatable
	__( 'Open Table Widget', $open_table_widget->textdomain );
	__( 'Devin Walker', $open_table_widget->textdomain );
	__( 'otw', $open_table_widget->textdomain );

	//Include the widget
	if ( ! class_exists( 'Open_Table_Widget' ) ) {
		require 'classes/widget.php';

	}

	return $open_table_widget;

}

/*
 * @DESC: Register Open Table widget
 */
add_action( 'widgets_init', 'init_open_table_widget' );
add_action( 'widgets_init', create_function( '', 'register_widget( "Open_Table_Widget" );' ) );


/**
 * Custom CSS for Options Page
 */
add_action( 'admin_enqueue_scripts', 'otw_options_scripts' );

function otw_options_scripts( $hook ) {

	if ( 'settings_page_opentablewidget' != $hook ) {
		return;
	} else {
		wp_register_style( 'otw_custom_options_styles', plugin_dir_url( __FILE__ ) . '/assets/css/options.css' );
		wp_enqueue_style( 'otw_custom_options_styles' );

	}


}

/**
 * Filter Update Checks
 *
 * Adds licence key to params
 *
 * @param $queryArgs
 *
 * @return mixed
 */
function wsh_filter_update_checks( $queryArgs ) {
	$options = get_option( 'yelp_widget_settings' );
	if ( ! empty( $options['yelp_widget_premium_licence'] ) ) {
		$queryArgs['licence_key'] = $options['yelp_widget_premium_licence'];
	}

	return $queryArgs;
}