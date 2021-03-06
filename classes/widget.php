<?php

/**
 * Class Open_Table_Widget
 *
 *  Open Table Widget
 *
 *  The Open Table Widget
 */
class Open_Table_Widget extends WP_Widget {

	/**
	 * Plugin Options from Options Panel.
	 *
	 * @var mixed|void
	 */
	var $options;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'otw_widget', // Base ID
			'Open Table Widget', // Name
			array(
				'classname'   => 'open-table-widget',
				'description' => __( 'Display an Open Table reservation form for your restaurant using an easy to use and intuitive widget', 'open-table-widget' )
			)
		);

		$this->options = get_option( 'opentablewidgetpro_options' );

		//Scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_widget_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_widget_scripts' ) );
		add_action( 'wp_ajax_open_table_api_action', array( $this, 'request_open_table_api' ) );
		add_action( 'wp_ajax_nopriv_open_table_api_action', array( $this, 'request_open_table_api' ) );

	}

	/**
	 * Load Widget JS Script ONLY on Widget page.
	 *
	 * @param $hook
	 */
	function admin_widget_scripts( $hook ) {

		if ( $hook == 'widgets.php' ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			//Enqueue
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-widget' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			wp_enqueue_script( 'otw_widget_admin_scripts', plugins_url( 'assets/js/admin-widget' . $suffix . '.js', dirname( __FILE__ ) ), array(
				'jquery',
				'jquery-ui-autocomplete'
			) );

			// In javascript, object properties are
			// accessed as ajax_object.ajax_url, ajax_object.we_value
			wp_localize_script( 'otw_widget_admin_scripts', 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'city_array' => $this->get_cities() ) );

			wp_enqueue_style( 'otw_widget_admin_css', plugins_url( 'assets/css/admin-widget' . $suffix . '.css', dirname( __FILE__ ) ) );
			wp_enqueue_style( 'otw_widget_jqueryui_css', plugins_url( 'assets/css/jquery-ui-custom' . $suffix . '.css', dirname( __FILE__ ) ) );

		}

	}

	/**
	 * Get Cities.
	 *
	 * @return array|mixed|WP_Error
	 */
	function get_cities() {

		$open_table_cities = get_transient( 'open_table_cities' );

		//Error check for transient.
		if ( is_wp_error( $open_table_cities ) ) {
			echo esc_html__( 'Open Table API Error', 'open-table-widget' ) . ': ' . $open_table_cities->get_error_message();
			delete_transient( 'open_table_cities' );

			return false;
		}

		// Get any existing copy of our transient data
		if ( false === $open_table_cities || isset( $open_table_cities['response'] ) && $open_table_cities['response'] === 500 ) {
			// It wasn't there, so regenerate the data and save the transient
			$response = wp_remote_get( 'http://opentable.herokuapp.com/api/cities' );

			//Proper error checking.
			if ( is_wp_error( $response ) ) {
				echo esc_html__( 'Open Table API Error', 'open-table-widget' ) . ': ' . $response->get_error_message();
				delete_transient( 'open_table_cities' );

				return false;
			}

			//Set transient if no a error.
			set_transient( 'open_table_cities', $response, 12 * 12 * HOUR_IN_SECONDS );

		}

		return apply_filters( 'otw_get_cities_response', $open_table_cities );

	}

	/**
	 * Open Table API Request.
	 */
	public function request_open_table_api() {

		//Get restaurant name.
		$restaurant = empty( $_POST['restaurant'] ) ? '' : stripslashes( htmlentities( $_POST['restaurant'], ENT_QUOTES ) );
		$city       = empty( $_POST['city'] ) ? '' : stripslashes( htmlentities( $_POST['city'], ENT_QUOTES ) );

		if ( $_POST['restaurant'] && empty( $city ) ) {
			// Send API Call using WP's HTTP API.
			$data = wp_remote_get( 'http://opentable.herokuapp.com/api/restaurants?name=' . $restaurant );
			//Proper error checking.
			if ( is_wp_error( $data ) ) {
				echo esc_html__( 'Open Table API Error', 'open-table-widget' ) . ': ' . $data->get_error_message();
			}
			// Handle OTW response data.
			echo $data['body'];

		} elseif ( $_POST['city'] ) {

			// Send API Call using WP's HTTP API.
			$data = wp_remote_get( 'http://opentable.herokuapp.com/api/restaurants?city=' . $city . '&name=' . $restaurant );
			//Proper error checking.
			if ( is_wp_error( $data ) ) {
				echo esc_html__( 'Open Table API Error', 'open-table-widget' ) . ': ' . $data->get_error_message();

				return false;
			}
			// Handle OTW response data.
			echo $data['body'];

		}

		die(); // this is required to return a proper result.

	}


	/**
	 * Frontend Scripts.
	 *
	 * Adds Open Table Widget Stylesheets.
	 */
	public function frontend_widget_scripts() {

		//Determine whether to display minified scripts/css or not (debugging true sets it)
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == false ? '' : '.min';

		$otw_css            = plugins_url( 'assets/css/open-table-widget' . $suffix . '.css', dirname( __FILE__ ) );
		$otw_select_css     = plugins_url( 'assets/css/selectric' . $suffix . '.css', dirname( __FILE__ ) );
		$otw_datepicker_css = plugins_url( 'assets/css/otw-datepicker' . $suffix . '.css', dirname( __FILE__ ) );

		$otw_datepicker = plugins_url( 'assets/js/datepicker' . $suffix . '.js', dirname( __FILE__ ) );
		$otw_select_js  = plugins_url( 'assets/js/jquery.selectric' . $suffix . '.js', dirname( __FILE__ ) );
		$otw_widget_js  = plugins_url( 'assets/js/open-table-widget' . $suffix . '.js', dirname( __FILE__ ) );

		/**
		 *   Register all Styles/Scripts for later use
		 */
		wp_register_style( 'otw_widget', $otw_css, null, OTW_PLUGIN_VERSION, 'screen' );
		wp_register_style( 'otw_select_css', $otw_select_css, null, OTW_PLUGIN_VERSION, 'screen' );
		wp_register_style( 'otw_datepicker_css', $otw_datepicker_css, null, OTW_PLUGIN_VERSION, 'screen' );

		wp_register_script( 'otw-widget-js', $otw_widget_js, array( 'jquery' ), OTW_PLUGIN_VERSION );
		wp_register_script( 'otw_datepicker_js', $otw_datepicker, array(
			'jquery',
			'otw-widget-js'
		), OTW_PLUGIN_VERSION );
		wp_register_script( 'otw_select_js', $otw_select_js, array( 'jquery' ) );

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}
		$align          = empty( $instance['align'] ) ? '' : $instance['align'];
		$maxWidth       = empty( $instance['max_width'] ) ? '' : $instance['max_width'];
		$displayOption  = ! isset( $instance['display_option'] ) ? '' : $instance['display_option'];
		$widgetStyle    = empty( $instance['widget_style'] ) ? '' : $instance['widget_style'];
		$restaurantName = empty( $instance['restaurant_name'] ) ? '' : $instance['restaurant_name'];
		$restaurantID   = empty( $instance['restaurant_id'] ) ? '' : $instance['restaurant_id'];
		$restaurantIDs  = empty( $instance['restaurant_ids'] ) ? '' : $instance['restaurant_ids'];
		$hideLabels     = empty( $instance['hide_labels'] ) ? '' : $instance['hide_labels'];
		$preContent     = empty( $instance['pre_content'] ) ? '' : $instance['pre_content'];
		$postContent    = empty( $instance['post_content'] ) ? '' : $instance['post_content'];
		$labelMultiple  = empty( $instance['label_multiple'] ) ? '' : $instance['label_multiple'];
		$labelCity      = empty( $instance['label_city'] ) ? '' : $instance['label_city'];
		$labelDate      = empty( $instance['label_date'] ) ? '' : $instance['label_date'];
		$labelTime      = empty( $instance['label_time'] ) ? '' : $instance['label_time'];
		$labelParty     = empty( $instance['label_party'] ) ? '' : $instance['label_party'];
		$inputSubmit    = empty( $instance['input_submit'] ) ? '' : $instance['input_submit'];
		$widgetLanguage = empty( $instance['widget_language'] ) ? '' : $instance['widget_language'];
		$lookupCity     = empty( $instance['lookup_city'] ) ? '' : $instance['lookup_city'];
		$timeStart      = empty( $instance['time_start'] ) ? '' : $instance['time_start'];
		$timeEnd        = empty( $instance['time_end'] ) ? '' : $instance['time_end'];
		$timeDefault    = empty( $instance['time_default'] ) ? '' : $instance['time_default'];
		$timeIncrement  = empty( $instance['time_increment'] ) ? '' : $instance['time_increment'];
		$partySize      = empty( $instance['party_size'] ) ? '' : $instance['party_size'];
		$maxSeats       = empty( $instance['max_seats'] ) ? '' : $instance['max_seats'];

		//CSS
		if ( $this->options['disable_css'] !== 'on' ) {
			wp_enqueue_style( 'otw_widget' );
		}

		//Datepicker
		if ( ! is_admin() ) {
            wp_enqueue_script( 'otw_datepicker_js' );
            wp_enqueue_style( 'otw_datepicker_css' );

            // Only enqueue the selectric dropdown if the setting is NOT "on"
            $selectric = $this->options["disable_bootstrap_select"];

            if ( $selectric !== "on" ) {
                wp_enqueue_script( 'otw_select_js' );
                wp_enqueue_style( 'otw_select_css' ); ?>

                <script>
                    jQuery(function ($) {
                        $('.otw-wrapper select').selectric();
                    });
                </script>
            <?php }

		    //Open Table Widget Specific Scripts

	        wp_enqueue_script( 'otw-widget-js' );


	        //Widget ID
	        $args['widget_id'] = empty( $args['widget_id'] ) ? rand( 1, 9999 ) : $args['widget_id'];

	        $jsParams = array(
		        'ajax_url'      => admin_url( 'admin-ajax.php' ),
		        'restaurant_id' => '',
	        );
	        wp_localize_script( 'otw-widget-js', 'otwParams', $jsParams );

	        //Determine widget display option
	        if ( $displayOption == '2' ) {
		        //widget needs autocomplete scripts
		        wp_enqueue_script( 'jquery-ui-autocomplete' );
		        wp_enqueue_style( 'otw_widget_jqueryui_css', plugins_url( 'assets/css/jquery-ui-custom.min.css', dirname( __FILE__ ) ) );

	        }
	        /*
			 * Output Widget Content
			 */
	        //Widget Style
	        $style = "otw-" . sanitize_title( $widgetStyle ) . "-style";

	        /* Add the width from $widget_width to the class from the $before widget
			http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget
			*/

	        // no 'class' attribute - add one with the value of width
	        if ( ! empty( $before_widget ) && strpos( $before_widget, 'class' ) === false ) {
		        $before_widget = str_replace( '>', 'class="' . $style . '"', $before_widget );
	        } // there is 'class' attribute - append width value to it
            elseif ( ! empty( $before_widget ) && strpos( $before_widget, 'class' ) !== false ) {
		        $before_widget = str_replace( 'class="', 'class="' . $style . ' ', $before_widget );
	        } //no 'before_widget' at all so wrap widget with div
	        else {
		        $before_widget = '<div class="open-table-widget">';
		        $before_widget = str_replace( 'class="', 'class="' . $style . ' ', $before_widget );
	        }


	        /* Alignment (adds class) */
	        if ( ! empty( $align ) ) {
		        $before_widget = str_replace( 'class="', 'class="otw-widget-align-' . $align . ' ', $before_widget );
	        }
	        /* Max Width (adds inline style) */
	        if ( ! empty( $maxWidth ) ) {
		        $before_widget = str_replace( '">', '" style="max-width:' . $maxWidth . ';">', $before_widget );
	        }

	        // Before widget
	        echo $before_widget;

	        // if the title is set & the user hasn't disabled title output
	        if ( ! empty( $title ) ) {
		        /* Add class to before_widget from within a custom widget
			 http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget
			 */
		        // no 'class' attribute - add one with the value of width
		        if ( ! empty( $before_title ) && strpos( $before_title, 'class' ) === false ) {
			        $before_title = str_replace( '>', ' class="otw-widget-title">', $before_title );
		        } //widget title has 'class' attribute
                elseif ( ! empty( $before_title ) && strpos( $before_title, 'class' ) !== false ) {
			        $before_title = str_replace( 'class="', 'class="otw-widget-title ', $before_title );
		        } //no 'title' at all so wrap widget with div
		        else {
			        $before_title = '<h3 class="">';
			        $before_title = str_replace( 'class="', 'class="otw-widget-title ', $before_title );
		        }
		        $after_title = empty( $after_title ) ? '</h3>' : $after_title;

		        echo $before_title . $title . $after_title;
	        }

	        ?>

            <div class="otw-<?php echo sanitize_title( $widgetStyle ); ?>">

		        <?php include( OTW_PLUGIN_PATH . '/inc/widget-frontend.php' ); ?>

            </div>
	        <?php

	        //after widget
	        echo ! empty( $after_widget ) ? $after_widget : '</div>';
        } // end if ( ! is_admin() );
	}


	/**
	 * Saves the widget options.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance                    = $old_instance;
		$instance['title']           = strip_tags( $new_instance['title'] );
		$instance['align']           = strip_tags( $new_instance['align'] );
		$instance['max_width']       = strip_tags( $new_instance['max_width'] );
		$instance['display_option']  = strip_tags( $new_instance['display_option'] );
		$instance['widget_style']    = strip_tags( $new_instance['widget_style'] );
		$instance['restaurant_name'] = strip_tags( $new_instance['restaurant_name'] );
		$instance['restaurant_id']   = strip_tags( $new_instance['restaurant_id'] );
		$instance['restaurant_ids']  = $new_instance['restaurant_ids'];
		$instance['hide_labels']     = strip_tags( $new_instance['hide_labels'] );
		$instance['pre_content']     = strip_tags( $new_instance['pre_content'] );
		$instance['post_content']    = strip_tags( $new_instance['post_content'] );
		$instance['label_multiple']  = strip_tags( $new_instance['label_multiple'] );
		$instance['label_city']      = strip_tags( $new_instance['label_city'] );
		$instance['label_date']      = strip_tags( $new_instance['label_date'] );
		$instance['label_time']      = strip_tags( $new_instance['label_time'] );
		$instance['label_party']     = strip_tags( $new_instance['label_party'] );
		$instance['input_submit']    = strip_tags( $new_instance['input_submit'] );
		$instance['widget_language'] = strip_tags( $new_instance['widget_language'] );
		$instance['lookup_city']     = strip_tags( $new_instance['lookup_city'] );
		$instance['time_start']      = strip_tags( $new_instance['time_start'] );
		$instance['time_end']        = strip_tags( $new_instance['time_end'] );
		$instance['time_default']    = strip_tags( $new_instance['time_default'] );
		$instance['time_increment']  = strip_tags( $new_instance['time_increment'] );
		$instance['party_size']      = strip_tags( $new_instance['party_size'] );
		$instance['max_seats']       = strip_tags( $new_instance['max_seats'] );

		return $instance;
	}


	/**
	 * Back-end widget form.
	 *
	 * @param array $instance
	 */
	function form( $instance ) {
		$title          = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$displayOption  = ! isset( $instance['display_option'] ) ? '' : $instance['display_option'];
		$align          = empty( $instance['align'] ) ? '' : $instance['align'];
		$widgetStyle    = empty( $instance['widget_style'] ) ? '' : esc_attr( $instance['widget_style'] );
		$restaurantName = empty( $instance['restaurant_name'] ) ? '' : esc_attr( $instance['restaurant_name'] );
		$restaurantID   = empty( $instance['restaurant_id'] ) ? '' : esc_attr( $instance['restaurant_id'] );
		$restaurantIDs  = empty( $instance['restaurant_ids'] ) ? '' : esc_attr( $instance['restaurant_ids'] );
		$hideLabels     = empty( $instance['hide_labels'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$preContent     = empty( $instance['pre_content'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$postContent    = empty( $instance['post_content'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$labelMultiple  = empty( $instance['label_multiple'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$labelCity      = empty( $instance['label_city'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$labelDate      = empty( $instance['label_date'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$labelTime      = empty( $instance['label_time'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$labelParty     = empty( $instance['label_party'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$inputSubmit    = empty( $instance['input_submit'] ) ? '' : esc_attr( $instance['hide_labels'] );
		$widgetLanguage = empty( $instance['widget_language'] ) ? '' : esc_attr( $instance['widget_language'] );
		$lookupCity     = empty( $instance['lookup_city'] ) ? '' : esc_attr( $instance['lookup_city'] );
		$timeStart      = empty( $instance['time_start'] ) ? '7:00pm' : esc_attr( $instance['time_start'] );
		$timeEnd        = empty( $instance['time_end'] ) ? '11:45pm' : esc_attr( $instance['time_end'] );
		$timeDefault    = empty( $instance['time_default'] ) ? '7:00pm' : esc_attr( $instance['time_default'] );
		$timeIncrement  = empty( $instance['time_increment'] ) ? '30' : esc_attr( $instance['time_increment'] );
		$partySize      = empty( $instance['party_size'] ) ? '4' : esc_attr( $instance['party_size'] );
		$maxSeats       = empty( $instance['max_seats'] ) ? '30' : esc_attr( $instance['max_seats'] );


		//Get the widget form.
		$widgetPath = OTW_PLUGIN_PATH . '/inc/widget-form.php';
		if ( file_exists( $widgetPath ) ) {
			include( $widgetPath );
		}


	}


	/**
	 * Time Function.
	 *
	 * @param $start
	 * @param $end
	 * @param $defaultTime
	 * @param $timeFormat
	 * @param $timeFormatVal
	 * @param $increment
	 */
	function open_table_reservaton_times( $start, $end, $defaultTime, $timeFormat, $timeFormatVal, $increment ) {

		/**
		 * Time Loop
		 * @see: http://stackoverflow.com/questions/6530836/php-time-loop-time-one-and-half-of-hour
		 */
		$inc   = ! empty( $increment ) ? intval( $increment ) * 60 : 15 * 60;
		$start = ! empty( $start ) ? strtotime( $start ) : ( strtotime( '12AM' ) ); // 6  AM
		$end   = ! empty( $end ) ? strtotime( $end ) : ( strtotime( '11:59PM' ) ); // 10 PM

		//default time
		$defaultTime = ! empty( $defaultTime ) ? strtotime( $defaultTime ) : strtotime( "7:00pm" );
		$defaultTime = date( $timeFormatVal, $defaultTime );

		for ( $i = $start; $i <= $end; $i += $inc ) {
			// to the standard format
			$time      = date( $timeFormat, $i );
			$timeValue = date( $timeFormatVal, $i );

			echo "<option value=\"$timeValue\" " . ( ( $timeValue == $defaultTime ) ? ' selected="selected" ' : "" ) . ">$time</option>" . PHP_EOL;

		}


	}

	/**
	 * Get Restaurant Data.
	 *
	 * @param $widgetLanguage
	 *
	 * @return array
	 */
	function get_restaurant_data( $widgetLanguage ) {

		$action = $dateFormat = $timeFormat = $timeFormatVal = '';

		switch ( $widgetLanguage ) {
			case 'ca-eng':
				$action        = 'http://www.opentable.com/restaurant-search.aspx';
				$dateFormat    = 'mm/dd/yyyy';
				$timeFormat    = 'g:i a';
				$timeFormatVal = 'g:ia';
				break;
			case 'ger-eng':
			case 'de-eng' :
				$action        = 'http://www.opentable.de/en-GB/restaurant-search.aspx';
				$dateFormat    = 'mm/dd/yyyy';
				$timeFormat    = 'g:i a';
				$timeFormatVal = 'g:ia';
				break;
			case 'ger-ger':
			case 'de-de' :
				$action     = 'http://www.opentable.de/restaurant-search.aspx';
				$dateFormat = 'dd.mm.yyyy';
				$timeFormat = $timeFormatVal = 'G:i';
				break;
			case 'uk':
				$action     = 'http://www.toptable.co.uk/restaurant-search.aspx';
				$dateFormat = 'dd/mm/yyyy';
				$timeFormat = $timeFormatVal = 'G:i';
				break;
			case 'mx-mx':
				$action        = 'http://www.opentable.com.mx/restaurant-search.aspx';
				$dateFormat    = 'dd/mm/yyyy';
				$timeFormat    = 'g:i a';
				$timeFormatVal = 'g:ia';
				break;
			case 'mx-eng':
				$action        = 'http://www.opentable.com.mx/en-US/restaurant-search.aspx';
				$dateFormat    = 'mm/dd/yyyy';
				$timeFormat    = 'g:i a';
				$timeFormatVal = 'g:ia';
				break;
			case 'jp-jp':
				$action     = 'http://www.opentable.jp/restaurant-search.aspx';
				$dateFormat = 'yyyy/mm/dd';
				$timeFormat = $timeFormatVal = 'G:i';
				break;
			case 'jp-eng':
				$action     = 'http://www.opentable.jp/en-GB/single.aspx';
				$dateFormat = 'yyyy/mm/dd';
				$timeFormat = $timeFormatVal = 'G:i';
				break;
			//usa
			default:
				$action        = 'http://www.opentable.com/restaurant-search.aspx';
				$dateFormat    = 'mm/dd/yyyy';
				$timeFormat    = 'g:i a';
				$timeFormatVal = 'g:ia';
		}

		return apply_filters( 'open_table_restaurant_data', array(
			'action'          => $action,
			'date_format'     => $dateFormat,
			'time_format'     => $timeFormat,
			'time_format_val' => $timeFormatVal
		) );

	}

}