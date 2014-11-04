<?php

/**
 *  WordImpress Licensing
 *
 * @description: Handles licencing for WordImpress products.
 */
class Open_Table_License {

	function __construct( $licence_args ) {

		$this->plugin_basename     = $licence_args['plugin_basename'];
		$this->settings_page       = $licence_args['settings_page'];
		$this->item_name           = $licence_args['item_name'];
		$this->store_url           = $licence_args['store_url'];
		$this->licence_key_setting = $licence_args['licence_key_setting'];
		$this->licence_key_option  = $licence_args['licence_key_option'];
		$this->licence_key_status  = $licence_args['licence_key_status']; //legacy

		add_action( 'admin_init', array( $this, 'edd_wordimpress_register_option' ) );
		add_action( 'admin_init', array( $this, 'edd_wordimpress_activate_license' ) );
		add_action( 'admin_init', array( $this, 'edd_wordimpress_deactivate_license' ) );

		//enqueue Licence assets
		add_action( 'admin_enqueue_scripts', array( $this, 'register_licence_assets' ) );
		//AJAX Activate license
		add_action( 'wp_ajax_wordimpress_activate_license', array( $this, 'ajax_activate_license' ) );
		//disable on deactivation
		register_deactivation_hook( $this->plugin_basename, array( $this, 'plugin_deactivated' ) );

		//Admin Notices
		add_action( 'admin_notices', array( $this, 'edd_wordimpress_license_admin_notices' ) );
		add_action( 'admin_init', array( $this, 'edd_wordimpress_license_admin_notices_ignore' ) );

	}

	/**
	 * Admin Notices for Licensing
	 */
	function edd_wordimpress_license_admin_notices() {
		global $current_user;
		$user_id = $current_user->ID;
		// Check that the user hasn't already clicked to ignore the message and that they have appropriate permissions
		if ( ! get_user_meta( $user_id, $this->licence_key_setting . '_license_ignore_notice' ) && current_user_can( 'install_plugins' ) ) {

			//check to see if the license is activated
			$license             = get_option( $this->licence_key_option );
			$legacyLicenseStatus = get_option( $this->licence_key_status );
			if ( ! empty( $license["license_status"] ) ) {
				$status = $license["license_status"];
			} elseif ( isset( $legacyLicenseStatus ) ) {
				$status = $legacyLicenseStatus;
			} else {
				$status = 'invalid';
			}

			//display notice if no license valid or found
			if ( $status == 'invalid' || empty( $status ) ) {
				echo '<div class="updated error"><p>';
				parse_str( $_SERVER['QUERY_STRING'], $params ); //ensures we're not redirect for admin pages using query string; ie '?=opentablewidget'

				$settings_link = '<a href="options-general.php?page=opentablewidgetpro">' . __( 'activate your license', 'open-table-widget' ) . '</a>';
				$hide_notice   = '<a href="?' . http_build_query( array_merge( $params, array( $this->licence_key_setting . '_license_ignore_notice' => '0' ) ) ) . '" rel="nofollow"> ' . __( 'Hide Notice', 'open-table-widget' ) . '</a>';

				printf(
					__( 'Please %1$s for ' . $this->item_name . ' to receive support and updates. | %2$s' ), $settings_link, $hide_notice
				);
				echo "</p></div>";
			}

		}
	}

	/**
	 * Set Usermeta to ignore the
	 */
	function edd_wordimpress_license_admin_notices_ignore() {
		global $current_user;
		$user_id = $current_user->ID;
		/* If user clicks to ignore the notice, add that to their user meta */
		if ( isset( $_GET[$this->licence_key_setting . '_license_ignore_notice'] ) && $_GET[$this->licence_key_setting . '_license_ignore_notice'] == '0' ) {
			add_user_meta( $user_id, $this->licence_key_setting . '_license_ignore_notice', 'true', true );
		}
	}


	/**
	 * Register assets
	 *
	 * Loads JS and CSS for licence form
	 *
	 */
	function register_licence_assets( $hook ) {

		if ( $hook == $this->settings_page ) {
			//JS for AJAX Activation
			wp_register_script( 'wordimpress_licencing_js', OTW_PLUGIN_URL . '/inc/licence/assets/js/licence.js' );
			wp_enqueue_script( 'wordimpress_licencing_js' );

			// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
			wp_localize_script(
				'wordimpress_licencing_js', 'ajax_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' )
				)
			);

			//CSS
			wp_register_style( 'wordimpress_licencing_css', OTW_PLUGIN_URL . '/inc/licence/assets/css/licence.css' );
			wp_enqueue_style( 'wordimpress_licencing_css' );

		}

	}

	/************************************
	 * this illustrates how to activate
	 * a license key
	 *************************************/
	function edd_wordimpress_activate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['edd_license_activate'] ) ) {

			//run a quick security check
			if ( ! check_admin_referer( 'edd_wordimpress_nonce', 'edd_wordimpress_nonce' ) ) {
				return false;
			} // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = $this->get_license();

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->item_name ), // the name of our product in EDD
				'url'        => home_url() // the name of our product in EDD

			);

			// Call the custom API.
			$response = wp_remote_post( add_query_arg( $api_params, $this->store_url ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );


			// $license_data->license will be either "valid" or "inactive"
			update_option(
				$this->licence_key_option,
				array(
					'license_key'        => $license,
					'license_item_name'  => $license_data->item_name,
					'license_expiration' => $license_data->expires,
					'license_status'     => $license_data->license,
					'license_name'       => $license_data->customer_name,
					'license_email'      => $license_data->customer_email,
					'license_payment_id' => $license_data->payment_id,
					'license_error'      => isset( $license_data->error ) ? $license_data->error : '',
				)
			);

		}
	}


	/***********************************************
	 * Illustrates how to deactivate a license key.
	 * This will descrease the site count
	 ***********************************************/

	function edd_wordimpress_deactivate_license( $plugin_deactivate = false ) {

		// listen for our activate button to be clicked
		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] === $this->licence_key_setting && isset( $_POST['edd_license_deactivate'] ) || isset( $_POST['option_page'] ) && $_POST['option_page'] === $this->licence_key_setting && $plugin_deactivate === true ) {


			// run a quick security check
			if ( ! current_user_can( 'activate_plugins' ) && ! check_admin_referer( 'edd_wordimpress_nonce', 'edd_wordimpress_nonce' ) ) {
				return;
			} // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = $this->get_license();

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->item_name ) // the name of our product in EDD
			);

			// Call the custom API.
			$response = wp_remote_post( add_query_arg( $api_params, $this->store_url ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if ( $license_data->license == 'deactivated' || $license_data->license == 'failed' ) {
				delete_option( $this->licence_key_option );
				delete_option( $this->licence_key_status );
			}

		}
	}


	/**
	 * Get License
	 *
	 * Returns the license if in options
	 */
	function get_license() {
		if ( ! empty( $_POST[$this->licence_key_option]['license_key'] ) ) {
			$license = ! empty( $_POST[$this->licence_key_option]['license_key'] ) ? trim( $_POST[$this->licence_key_option]['license_key'] ) : '';
		} else {
			$current_options = get_option( $this->licence_key_option );
			$license         = $current_options["license_key"];
		}

		return $license;
	}


	/**
	 * Handles the output of the licence form in options
	 */
	function edd_wordimpress_license_page() {

		$license = get_option( $this->licence_key_option );
		$status  = isset( $license["license_status"] ) ? $license["license_status"] : 'invalid'; ?>

		<div class="edd-wordimpress-license-wrap">
			<h2><?php _e( 'Plugin License', 'open-table-widget' ); ?></h2>

			<?php
			//valid license
			if ( $status !== false && $status == 'valid' ) {
				?>

				<div class="license-stats list-group">
					<p class="list-group-item"><strong><?php _e( 'License Status:', 'open-table-widget' ); ?></strong>
						<span style="color: #468847;"><?php echo strtoupper( $license['license_status'] ); ?></span>
						<strong>(<?php echo $this->time_left_on_license( $license['license_expiration'] );
							_e( ' Days Remaining', 'open-table-widget' ); ?>)</strong></p>

					<p class="list-group-item">
						<strong><?php _e( 'License Expiration:', 'open-table-widget' ); ?></strong> <?php echo $license['license_expiration']; ?>
					</p>

					<p class="list-group-item">
						<strong><?php _e( 'License Owner:', 'open-table-widget' ); ?></strong> <?php echo $license['license_name']; ?></p>

					<p class="list-group-item">
						<strong><?php _e( 'License Email:', 'open-table-widget' ); ?></strong> <?php echo $license['license_email']; ?></p>

					<p class="list-group-item">
						<strong><?php _e( 'License Payment ID:', 'open-table-widget' ); ?></strong> <?php echo $license['license_payment_id']; ?>
					</p>
				</div>

				<p class="alert alert-success license-status"><?php _e( 'Your license is active and you are receiving updates.', 'open-table-widget' ); ?></p>

			<?php
			} //Reached Activation?
			elseif ( $status == 'invalid' && isset( $license['license_error'] ) && $license['license_error'] == 'no_activations_left' ) {
				?>

				<p class="alert alert-red license-status"><?php _e( 'The license you entered has reached the activation limit. To purchase more licenses please visit WordImpress.', 'open-table-widget' ); ?></p>

			<?php } elseif ( $status == 'invalid' && isset( $license['license_error'] ) && $license["license_error"] == 'missing' ) { ?>

				<p class="alert alert-red license-status"><?php _e( 'There was a problem with the license you entered. Please check that your license key is active and valid then reenter it below. If you are having trouble please contact support for assistance.', 'open-table-widget' ); ?></p>

			<?php } else { ?>

				<p class="alert alert-red license-status"><?php _e( 'Activate your license to receive automatic plugin updates for the life of your license.', 'open-table-widget' ); ?></p>

			<?php } ?>


			<form method="post" action="options.php">

				<?php settings_fields( $this->licence_key_setting ); ?>

				<input id="<?php echo $this->licence_key_option; ?>[license_key]" name="<?php echo $this->licence_key_option; ?>[license_key]" <?php echo ( $status !== false && $status == 'valid' ) ? 'type="password"' : 'type="text"'; ?> class="licence-input <?php echo ( $status !== false && $status == 'valid' ) ? ' license-active' : ' license-inactive'; ?>" value="<?php if ( $status !== false && $status == 'valid' ) {
					echo $license['license_key'];
				} ?>" autocomplete="off" />
				<label class="description licence-label" for="<?php echo $this->licence_key_option; ?>"><?php if ( $status !== false && $status == 'valid' ) {
						_e( 'Your licence is active and valid.', 'open-table-widget' );
					} else {
						_e( 'Enter your license key to receive updates and support', 'open-table-widget' );
					} ?></label>


				<?php if ( $status !== false && $status == 'valid' ) { ?>
					<?php wp_nonce_field( 'edd_wordimpress_nonce', 'edd_wordimpress_nonce' ); ?>
					<input type="submit" class="button-secondary deactivate-license-btn" name="edd_license_deactivate" value="<?php _e( 'Deactivate License', 'open-table-widget' ); ?>" />
				<?php
				} else {
					wp_nonce_field( 'edd_wordimpress_nonce', 'edd_wordimpress_nonce' ); ?>
					<input type="submit" class="button-secondary activate-license-btn" name="edd_license_activate" value="<?php _e( 'Activate License', 'open-table-widget' ); ?>" />
				<?php } ?>


				<?php //submit_button(); ?>

			</form>

		</div>
	<?php
	}


	/**
	 * Registers the Settings
	 */
	function edd_wordimpress_register_option() {
		// creates our settings in the options table
		register_setting( $this->licence_key_setting, $this->licence_key_setting );
	}

	/**
	 * Returns Remaining Number of Days License is Active
	 *
	 * @param $exp_date
	 *
	 * @return float
	 */
	function time_left_on_license( $exp_date ) {
		$now       = time(); // or your date as well
		$your_date = strtotime( $exp_date );
		$datediff  = abs( $now - $your_date );

		return floor( $datediff / ( 60 * 60 * 24 ) );
	}


	/************************************
	 * this illustrates how to check if
	 * a license key is still valid
	 * the updater does this for you,
	 * so this is only needed if you
	 * want to do something custom
	 *************************************/

	function edd_sample_check_license() {

		global $wp_version;

		$license = trim( get_option( $this->licence_key_option ) );

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name )
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, $this->store_url ), array( 'timeout' => 15, 'sslverify' => false ) );


		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data->license == 'valid' ) {
			echo 'valid';
			exit;
			// this license is still valid
		} else {
			echo 'invalid';
			exit;
			// this license is no longer valid
		}
	}

	/**
	 * Disable license on deactivation
	 *
	 * @see: http://wordpress.stackexchange.com/questions/25910/uninstall-activate-deactivate-a-plugin-typical-features-how-to/25979#25979
	 */
	public
	function plugin_deactivated() {
		// This will run when the plugin is deactivated, use to delete the database
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );

		return $this->edd_wordimpress_deactivate_license( $plugin_deactivate = true );
	}


}