<?php
/**
 * Plugin Name: Open Table Widget Pro
 * Plugin URI: http://wordimpress.com/plugins/open-table-widget-pro/
 * Description: <strong>Pro Version:</strong> Thank you for upgrading to Open Table Widget Pro. You have enabled additional features and enhancements. This plugin is actively developed and supported by WordImpress. Display an Open Table reservation widget for your restaurant, bar, nightclub, hotel or eatery.
 * Version: 1.8.1
 * Author: WordImpress
 * Author URI: https://wordimpress.com/
 * Text Domain: open-table-widget
 */

if ( ! defined( 'OTW_PLUGIN_VERSION' ) ) {
	define( 'OTW_PLUGIN_VERSION', '1.8.1' );
}
if ( ! defined( 'OTW_PLUGIN_NAME' ) ) {
	define( 'OTW_PLUGIN_NAME', 'open-table-widget' );
}
if ( ! defined( 'OTW_PLUGIN_NAME_PLUGIN' ) ) {
	define( 'OTW_PLUGIN_NAME_PLUGIN', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'OTW_PLUGIN_PATH' ) ) {
	define( 'OTW_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}
if ( ! defined( OTW_PLUGIN_URL ) ) {
	define( 'OTW_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
}

/**
 * Load Localized Translations if available.
 */
function otw_load_textdomain() {
	$domain  = 'open-table-widget';
	$mo_file = WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . get_locale() . '.mo';

	load_textdomain( $domain, $mo_file );
	load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

add_action( 'init', 'otw_load_textdomain' );

/**
 * Initialize OTW.
 *
 * @return Open_Table_Widget|OTW_Plugin_Framework
 */
function init_open_table_widget() {

	// Include Core Framework class.
	require_once 'classes/core.php';

	include_once( OTW_PLUGIN_PATH . '/inc/licence/licence.php' );

	// Include Licensing.
	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		// load our custom updater.
		include_once( OTW_PLUGIN_PATH . '/inc/licence/classes/EDD_SL_Plugin_Updater.php' );
	}

	global $open_table_widget;
	// Create plugin instance.
	$open_table_widget = new OTW_Plugin_Framework( __FILE__ );

	// Include options set.
	include_once 'inc/options.php';

	// Create options page
	$open_table_widget->add_options_page( array(), $open_table_widget_options );

	// Make plugin meta translatable
	__( 'Open Table Widget', $open_table_widget->textdomain );
	__( 'Devin Walker', $open_table_widget->textdomain );
	__( 'open-table-widget', $open_table_widget->textdomain );

	//Include the widget.
	if ( ! class_exists( 'Open_Table_Widget' ) ) {
		require 'classes/widget.php';
		require 'classes/shortcode.php';
		$otw_shortcode = new Open_Table_Widget_Shortcode();
	}

	return $open_table_widget;

}

add_action( 'widgets_init', 'init_open_table_widget' );
add_action( 'widgets_init', create_function( '', 'register_widget( "Open_Table_Widget" );' ) );


/**
 * Custom CSS for Options Page.
 *
 * @param $hook
 */
function otw_options_scripts( $hook ) {
	if ( $hook === 'settings_page_opentablewidgetpro' ) {
		wp_register_style( 'otw_custom_options_styles', plugin_dir_url( __FILE__ ) . '/assets/css/options.css' );
		wp_enqueue_style( 'otw_custom_options_styles' );
	}
}

add_action( 'admin_enqueue_scripts', 'otw_options_scripts' );