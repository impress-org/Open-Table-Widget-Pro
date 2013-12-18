<?php
/**
 *  WordImpress Licensing
 *
 * @description: Handles licencing for WordImpress products.
 */

class WordImpress_Licensing {


	private $wordimpress_api_base = 'http://wordimpress.com/'; //used to query API

	private $wordimpress_user_account_page = 'http://wordimpress.com/my-account/'; //used to query API

	private $product_id = 'Open Table Widget'; //used to target specific product

	private $settings_page = 'settings_page_opentablewidgetpro'; //used to enqueue JS only for that page

	/**
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * @var string
	 */
	public $wordimpress_version_name = 'open_table_widget_version_name';


	function __construct() {


		$this->settings            = get_option( 'opentablewidget_options' );
		$this->wordimpress_api_url = $this->wordimpress_api_base . '?wc-api=am-software-api';
		$this->transient_timeout   = 60 * 60 * 12;
		$this->textdomain          = 'otw';
		$this->plugin_base         = OTW_PLUGIN_NAME_PLUGIN;


		if ( is_admin() && ! $this->is_licence_expired() ) {

			// Checks for software updates
			require_once( plugin_dir_path( __FILE__ ) . 'classes/class-wc-plugin-update.php' );

			// Load update class to update $this plugin
			$this->load_plugin_self_updater();

		}


		// Add an extra row to the plugin screen
		add_action( 'after_plugin_row_' . OTW_PLUGIN_NAME_PLUGIN, array( $this, 'plugin_row' ), 11 );

		// AJAX licence activation and deactivation
		add_action( 'wp_ajax_wordimpress_activate_licence', array( $this, 'ajax_activate_licence' ) );
		add_action( 'wp_ajax_wordimpress_deactivate_licence', array( $this, 'ajax_deactivate_licence' ) );

		//enqueue Licence assets
		add_action( 'admin_enqueue_scripts', array( $this, 'register_licence_assets' ) );

		//prevent WordPress.org license checks
//		add_filter( 'http_request_args', array( $this, 'wordimpress_prevent_wordpress_update_check' ), 10, 2 );


	}

	/**
	 * Prevent WordPress.org Licence Checks
	 *
	 * @param $r
	 * @param $url
	 *
	 * @return mixed
	 */
	function wordimpress_prevent_wordpress_update_check( $r, $url ) {

		if ( 0 !== strpos( $url, 'https://api.wordpress.org/plugins/' ) ) {
			return $r;
		}
		$my_plugin = OTW_PLUGIN_NAME_PLUGIN;
		$plugins   = unserialize( $r['body']['plugins'] );

		unset(
		$my_plugin,
		$plugins->active[array_search( $my_plugin, $plugins->active )]
		);
		$r['body']['plugins'] = serialize( $plugins );

		return $r;

	}


	/**
	 * Register assets
	 */
	function register_licence_assets( $hook ) {
		if ( $hook == $this->settings_page ) {
			//JS
			wp_register_script( 'wordimpress_licencing_js', plugins_url( 'licence/assets/js/licence.js', dirname( __FILE__ ) ) );
			wp_enqueue_script( 'wordimpress_licencing_js' );

			//CSS
			wp_register_style( 'wordimpress_licencing_css', plugins_url( 'licence/assets/css/licence.css', dirname( __FILE__ ) ) );
			wp_enqueue_style( 'wordimpress_licencing_css' );

		}

	}


	/**
	 * Check for software updates
	 */
	public function load_plugin_self_updater() {

		$upgrade_url       = $this->wordimpress_api_base; // URL to access the Update API Manager.
		$plugin_name       = OTW_PLUGIN_NAME_PLUGIN; // same as plugin slug. if a theme use a theme name like 'twentyeleven'
		$product_id        = 'Open Table Widget'; // Software Title
		$api_key           = $this->get_licence_key(); // API License Key
		$activation_email  = $this->settings['licence_email']; // License Email
		$renew_license_url = $this->wordimpress_user_account_page; // URL to renew a license
		$instance          = $this->settings['instance']; // Instance ID (unique to each blog activation)
		$domain            = site_url(); // blog domain name
		$software_version  = get_option( $this->wordimpress_version_name ); // The software version
		$plugin_or_theme   = 'plugin'; // 'theme' or 'plugin'


		new API_Manager_Example_Update_API_Check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, 'otw' );
	}


	/**
	 * Activate Plugin Licence
	 */
	function ajax_activate_licence() {


		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-wc-api-manager-passwords.php' );

		$API_Manager_Example_Password_Management = new API_Manager_Example_Password_Management();

		// Generate a unique installation $instance id
		if ( empty( $this->settings['instance'] ) ) {
			$this->settings['instance'] = $API_Manager_Example_Password_Management->generate_password( 12, false );
		}
//		echo $this->settings['instance'];
		//set initial args array
//		$this->settings['instance'] =  'N8HtoKBhiHcf';

		$args = array(
			'request'     => 'activation',
			'licence_key' => urlencode( $_POST['licence_key'] ),
			'email'       => urlencode( $_POST['licence_email'] ),
			'platform'    => urlencode( site_url( '', 'http' ) ),
			'product_id'  => urlencode( $this->product_id ),
			'instance'    => $this->settings['instance'] //Generated Password
		);

		//check for licence constant
		if ( $this->is_licence_constant() ) {
			$args['licence_key'] = $this->get_licence_key();
		}

		//action: make API request
		$response = $this->wordimpress_api_request( 'activation', $args );
//		echo $args;
		//for debugging (displays in console)
		echo $response;

		//JSON decode the response for JS
		$response = json_decode( $response, true );

		//check response is error free
		if ( $response && ! isset( $response['errors'] ) ) {
			//check for licence constant
			if ( ! $this->is_licence_constant() ) {
				//no constant so set licence setting
				$this->settings['licence_key'] = $_POST['licence_key'];
			}


			// Set Transient
			if ( $response['activated'] == true ) {
				set_site_transient( 'wordimpress_licence_response', 'active', $this->transient_timeout );

				//update options with licence email and licence
				$this->settings['licence_email'] = $_POST['licence_email'];

			}


			// get current plugin version
			$curr_ver = get_option( $this->wordimpress_version_name );

			// checks if the current plugin version is lower than the version being installed
			if ( version_compare( $this->version, $curr_ver, '>' ) ) {
				// update the version
				update_option( $this->wordimpress_version_name, $this->version );
			}


			//update options
			update_option( 'opentablewidget_options', $this->settings );

		}
		//pz out
		exit;
	}

	/**
	 * Deactivate Plugin Licence
	 */
	function ajax_deactivate_licence() {

		//set initial args array
		$args = array(
			'request'     => 'deactivation',
			'email'       => urlencode( $_POST['licence_email'] ),
			'licence_key' => urlencode( $_POST['licence_key'] ),
			'product_id'  => urlencode( $this->product_id ),
			'platform'    => urlencode( site_url( '', 'http' ) ),
			'instance'    => $this->settings['instance'] //GENERATE PASSWORD HERE
		);

		//check for licence constant
		if ( $this->is_licence_constant() ) {
			$args['licence_key'] = $this->get_licence_key();
		}

		//action: make API request
		$response = $this->wordimpress_api_request( 'deactivation', $args );
//		echo $args;
		//for debugging (displays in console)
		echo $response;

		//JSON decode the response for JS
		$response = json_decode( $response, true );

		//check response is error free
		if ( $response && ! isset( $response['errors'] ) ) {
			//check for licence constant
			if ( ! $this->is_licence_constant() ) {
				//no constant so REMOVE licence key from settings
				$this->settings['licence_key'] = '';
			}

			//remove email and transient vals
			$this->settings['licence_email'] = '';
			set_site_transient( 'wordimpress_licence_response', 'inactive' );

			//update options
			update_option( 'opentablewidget_options', $this->settings );
		}
		//pz out
		exit;
	}


	/**
	 * API Request
	 *
	 * Talks to WordImpress server to check licence
	 *
	 * @param       $request
	 * @param array $args
	 *
	 * @return mixed|string|void
	 */
	function wordimpress_api_request( $request, $args = array() ) {

		$url = $this->get_wordimpress_api_url( $request, $args );

		$response = wp_remote_get( $url, array(
			'timeout'  => 30,
			'blocking' => true
		) );

		if ( is_wp_error( $response ) || (int) $response['response']['code'] < 200 || (int) $response['response']['code'] > 399 ) {
			return json_encode( array( 'errors' => array( 'connection_failed' => $url . 'Could not connect to wordimpress.com.' ) ) );
		}

		return $response['body'];
	}


	/**
	 * Get API URL
	 *
	 * @param       $request
	 * @param array $args
	 *
	 * @return string
	 */
	function get_wordimpress_api_url( $request, $args = array() ) {
		$url             = $this->wordimpress_api_url;
		$args['request'] = $request;
		$url             = add_query_arg( $args, $url );

		return $url;
	}

	/**
	 * Conditional for licence Constant
	 * @return bool
	 */
	function is_licence_constant() {
		return defined( 'OPEN_TABLE_LICENCE' );
	}

	/**
	 * Get licence Key
	 * @return mixed
	 */
	function get_licence_key() {

		$licence_key = false;

		if ( $this->is_licence_constant() ) {
			$licence_key = OPEN_TABLE_LICENCE;
		} elseif ( isset( $this->settings['licence_key'] ) && ! empty( $this->settings['licence_key'] ) ) {

			$licence_key = $this->settings['licence_key'];
		}

		return $licence_key;
	}

	/**
	 * Conditional licence Expired Check
	 *
	 * @param bool $skip_transient_check
	 *
	 * @return array|mixed
	 */
	function is_licence_expired( $skip_transient_check = false ) {
		$licence            = $this->get_licence_key();
		$is_licence_expired = true;
		if ( ! $skip_transient_check ) {
			$transient = get_site_transient( 'wordimpress_licence_response' );
			if ( false !== $transient && $transient == 'active' ) {
				$is_licence_expired = false;
			}
		} else {
			$is_licence_expired = $this->check_licence( $licence );
		}

		return $is_licence_expired;
	}


	function check_licence( $licence_key ) {
		// testing only 1st line = valid licence 2nd line = not valid licence
		// return json_encode( array( 'ok' => 'ok' ) );
		// return json_encode( array( 'errors' => array( 'standard' => 'oh no! licence is not working.') ) );
		if ( empty( $licence_key ) ) {
			return false;
		}
		$args = array(
			'wc-api'      => 'wordimpress_licence_check',
			'product_id'  => urlencode( $this->product_id ),
			'licence_key' => $this->get_licence_key(),
			'email'       => $this->settings['licence_email'],
		);

		$url = $this->wordimpress_api_base;
		$url = add_query_arg( $args, $url );

		$request = wp_remote_get( $url, array(
			'timeout'  => 30,
			'blocking' => true
		) );

		//responses: active or inactive;
		$response = wp_remote_retrieve_body( $request );
		set_site_transient( 'wordimpress_licence_response', $response, $this->transient_timeout );

		return $response;
	}

	/*
	* Shows a message below the plugin on the plugins page when:
	*	1. the licence hasn't been activated
	* 2.
	*	*/
	function plugin_row() {
		$licence          = $this->get_licence_key();
		$licence_response = $this->is_licence_expired();

		global $open_table_widget;

		if ( $licence === false ) {
			$settings_link = sprintf( '<a href="%s">%s</a>', $open_table_widget->admin_url, __( 'Settings', $this->textdomain ) );
			$message       = 'To finish activating the plugin please go to ' . $settings_link . ' and enter your licence key.';
		} else {
			return;
		} ?>

		<tr class="plugin-update-tr <?php echo $this->textdomain . '-plugin-custom'; ?>">
			<td colspan="3" class="plugin-update" style="background:#FFF;">
				<div style="background-color: #FCF3EF; border: 0; font-size: 13px;font-weight: 400;margin: 6px 12px 8px;padding: 6px 12px;">
					<div class="<?php echo $this->textdomain . 'licence-error-notice'; ?>"><?php echo $message; ?></div>
				</div>
			</td>
		</tr>


	<?php

	}

	/**
	 * Outputs the licensing fields in the admin section
	 */
	function licence_fields() {
		?>

		<h3><?php echo $this->product_id; ?> <?php _e( 'licence', $this->textdomain ); ?></h3>

		<div class="inside">

			<?php
			//do licensing
			$licence = $this->get_licence_key();
			$licenceEmail = empty( $this->settings['licence_email'] ) ? '' : $this->settings['licence_email'];

			if ( $this->is_licence_constant() ) {
				?>
				<p>
					<?php _e( 'The licence key is currently defined in wp-config.php.', $this->textdomain ); ?>
				</p>
			<?php } else { ?>

				<input type="text" class="licence-input licence-email<?php echo ( $this->is_licence_expired() == false ) ? ' input-active-licence' : ''; ?>" autocomplete="off" placeholder="licence Email" value="<?php echo esc_attr( $licenceEmail ); ?>" />
				<input type="text" class="licence-input licence-key<?php echo ( $this->is_licence_expired() == false ) ? ' input-active-licence' : ''; ?>" autocomplete="off" placeholder="licence Key" value="<?php echo esc_attr( $licence ); ?>" />

				<div class="licence-button-wrap">
					<button class="button register-licence <?php echo ( $this->is_licence_expired() == false ) ? 'licence-hidden' : ''; ?>" type="submit"> <?php _e( 'Activate licence', $this->textdomain ); ?> </button>
					<button class="button deactivate-licence <?php echo ( $this->is_licence_expired() == true ) ? 'licence-hidden' : ''; ?>" type="submit"> <?php _e( 'Deactivate licence', $this->textdomain ); ?> </button>
				</div>

				<div class="licence-status-wrap">
					<?php
					if ( $this->is_licence_expired() == true ) {
						?>
						<p class="licence-status alert alert-danger">Please activate your licence to use this plugin and receive updates.</p>
					<?php } else { ?>
						<p class="licence-status alert alert-success">Your licence is active and you are receiving plugin updates.</p>
					<?php } ?>
				</div>
			<?php } ?>

		</div>

	<?php
	}

}

$wordimpress_licensing = new WordImpress_Licensing();