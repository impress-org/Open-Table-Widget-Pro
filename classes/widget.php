<?php

/**
 *  Open Table Widget
 *
 * @description: The Open Table Widget
 * @since      : 1.0
 * @created    : 8/28/13
 */
class Open_Table_Widget extends WP_Widget {

	var $options; //Plugin Options from Options Panel

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

		add_action( 'wp_enqueue_scripts', array( $this, 'add_otw_widget_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_otw_admin_widget_scripts' ) );
		add_action( 'wp_ajax_open_table_api_action', array( $this, 'otw_widget_request_open_table_api' ) );

	}

	//Load Widget JS Script ONLY on Widget page
	function add_otw_admin_widget_scripts( $hook ) {

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

			// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
			wp_localize_script( 'otw_widget_admin_scripts', 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'city_array' => $this->otw_get_cities() ) );

			wp_enqueue_style( 'otw_widget_admin_css', plugins_url( 'assets/css/admin-widget' . $suffix . '.css', dirname( __FILE__ ) ) );
			wp_enqueue_style( 'otw_widget_jqueryui_css', plugins_url( 'assets/css/jquery-ui-custom' . $suffix . '.css', dirname( __FILE__ ) ) );

		}

	}

	function otw_get_cities() {
		// Get any existing copy of our transient data
		$open_table_cities = get_transient( 'open_table_cities' );

		if ( $open_table_cities === false || $open_table_cities['response'] === 500 ) {
			// It wasn't there, so regenerate the data and save the transient
			$open_table_cities = wp_remote_get( 'http://opentable.herokuapp.com/api/cities' );
			set_transient( 'open_table_cities', $open_table_cities, 12 * 12 * HOUR_IN_SECONDS );
		}

		return $open_table_cities;

	}

	function otw_widget_request_open_table_api() {

		//get restaurant name
		$restaurant = empty( $_POST['restaurant'] ) ? '' : stripslashes( htmlentities( $_POST['restaurant'], ENT_QUOTES ) );
		$city       = empty( $_POST['city'] ) ? '' : stripslashes( htmlentities( $_POST['city'], ENT_QUOTES ) );


		if ( $_POST['restaurant'] && empty( $city ) ) {
			// Send API Call using WP's HTTP API
			$data = wp_remote_get( 'http://opentable.herokuapp.com/api/restaurants?name=' . $restaurant );

			// Handle OTW response data
			echo $data["body"];

		} elseif ( $_POST['city'] ) {

			// Send API Call using WP's HTTP API
			$data = wp_remote_get( 'http://opentable.herokuapp.com/api/restaurants?city=' . $city . '&name=' . $restaurant );

			// Handle OTW response data
			echo $data["body"];

		}

		die(); // this is required to return a proper result

	}


	/**
	 * Adds Open Table Widget Stylesheets
	 */

	function add_otw_widget_scripts() {

		$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		//Determine whether to display minified scripts/css or not (debugging true sets it)
		if ( $script_debug == true ) {
			$otw_css                 = plugins_url( 'assets/css/open-table-widget.css', dirname( __FILE__ ) );
			$otw_datepicker          = plugins_url( 'assets/js/datepicker.js', dirname( __FILE__ ) );
			$otw_select_js           = plugins_url( 'assets/js/jquery.bootstrap-select.js', dirname( __FILE__ ) );
			$otw_bootstrap_dropdowns = plugins_url( 'assets/js/jquery.bootstrap-dropdown.min.js', dirname( __FILE__ ) );
			$otw_widget_js           = plugins_url( 'assets/js/open-table-widget.js', dirname( __FILE__ ) );
		} else {
			$otw_css                 = plugins_url( 'assets/css/open-table-widget.min.css', dirname( __FILE__ ) );
			$otw_datepicker          = plugins_url( 'assets/js/datepicker.min.js', dirname( __FILE__ ) );
			$otw_select_js           = plugins_url( 'assets/js/jquery.bootstrap-select.min.js', dirname( __FILE__ ) );
			$otw_bootstrap_dropdowns = plugins_url( 'assets/js/jquery.bootstrap-dropdown.min.js', dirname( __FILE__ ) );
			$otw_widget_js           = plugins_url( 'assets/js/open-table-widget.min.js', dirname( __FILE__ ) );
		}

		/**
		 * CSS
		 */
		if ( $this->options["disable_css"] !== "on" ) {
			wp_register_style( 'otw_widget', $otw_css );
			wp_enqueue_style( 'otw_widget' );
		}
		/**
		 * JS
		 */
		wp_enqueue_script( 'jquery' );

		//Datepicker
		wp_register_script( 'otw_datepicker_js', $otw_datepicker, array( 'jquery' ) );
		wp_enqueue_script( 'otw_datepicker_js' );


		//Select Menus
		if ( $this->options["disable_bootstrap_select"] !== "on" ) {

			wp_register_script( 'otw_select_js', $otw_select_js, array( 'jquery' ) );
			wp_enqueue_script( 'otw_select_js' );

		}

		//bootstrap dropdowns
		if ( $this->options["disable_bootstrap_dropdown"] !== "on" && $this->options["disable_bootstrap_select"] !== "on" ) {
			wp_register_script( 'otw_dropdown_js', $otw_bootstrap_dropdowns );
			wp_enqueue_script( 'otw_dropdown_js' );
		}


		//Open Table Widget Specific Scripts
		wp_register_script( 'otw-widget-js', $otw_widget_js, array( 'jquery' ) );
		wp_enqueue_script( 'otw-widget-js' );

		//Widget ID
		$args['widget_id'] = empty( $args['widget_id'] ) ? rand( 1, 9999 ) : $args['widget_id'];

		$jsParams = array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'restaurant_id' => '',
		);
		wp_localize_script( 'otw-widget-js', 'otwParams', $jsParams );

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
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

		/* Before widget */
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

	}


	/**
	 * @DESC: Saves the widget options
	 * @SEE WP_Widget::update
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
	 * @see WP_Widget::form()
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


		//Get the widget form
		$widgetPath = OTW_PLUGIN_PATH . '/inc/widget-form.php';
		if ( file_exists( $widgetPath ) ) {
			include( $widgetPath );
		}


	} //end form function


	/**
	 * Time Function
	 */
	function open_table_reservaton_times( $start, $end, $defaultTime, $timeFormat, $timeFormatVal, $increment ) {

		//Time Loop
		//@SEE: http://stackoverflow.com/questions/6530836/php-time-loop-time-one-and-half-of-hour
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


	function open_table_get_res_data( $widgetLanguage ) {

		$action = $dateFormat = $timeFormat = $timeFormatVal = '';

		switch ( $widgetLanguage ) {
			case 'ca-eng':
				$action        = 'http://www.opentable.com/restaurant-search.aspx';
				$dateFormat    = 'mm/dd/yyyy';
				$timeFormat    = 'g:i a';
				$timeFormatVal = 'g:ia';
				break;
			case 'ger-eng':
				$action        = 'http://www.opentable.de/en-GB/restaurant-search.aspx';
				$dateFormat    = 'mm/dd/yyyy';
				$timeFormat    = 'g:i a';
				$timeFormatVal = 'g:ia';
				break;
			case 'ger-ger':
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

		return array(
			'action'          => $action,
			'date_format'     => $dateFormat,
			'time_format'     => $timeFormat,
			'time_format_val' => $timeFormatVal
		);

	}


} //end Open_Table_Widget Class
