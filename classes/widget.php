<?php
/**
 *  Open Table Widget
 *
 * @description: The Open Table Widget
 * @since: 1.0
 * @created: 8/28/13
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
                'classname' => 'open-table-widget',
                'description' => __('Display an Open Table reservation form for your restaurant using an easy to use and intuitive widget', 'otw')
            )
        );

        $this->options = get_option('opentablewidget_options');

        add_action('wp_enqueue_scripts', array($this, 'add_otw_widget_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'add_otw_admin_widget_scripts'));
        add_action('wp_ajax_open_table_api_action', array($this, 'otw_widget_request_open_table_api'));

    }

    //Load Widget JS Script ONLY on Widget page
    function add_otw_admin_widget_scripts($hook) {

        if ($hook == 'widgets.php') {

            //Enqueue
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-autocomplete');

            wp_enqueue_script('otw_widget_admin_scripts', plugins_url('assets/js/admin-widget.js', dirname(__FILE__)), array('jquery'));

            // in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
            wp_localize_script('otw_widget_admin_scripts', 'ajax_object',
                array('ajax_url' => admin_url('admin-ajax.php'), 'city_array' => $this->otw_get_cities()));

            wp_enqueue_style('otw_widget_admin_css', plugins_url('assets/css/admin-widget.css', dirname(__FILE__)));
            wp_enqueue_style('otw_widget_jqueryui_css', plugins_url('assets/css/jquery-ui.min.css', dirname(__FILE__)));

        } else {
            return;
        }
    }

    function otw_get_cities() {
        // Get any existing copy of our transient data
        $open_table_cities = get_transient('open_table_cities');
        if (!empty($open_table_cities)) {
            // It wasn't there, so regenerate the data and save the transient
            $open_table_cities = wp_remote_get('http://opentable.herokuapp.com/api/cities');

            set_transient('open_table_cities', $open_table_cities, 12 * 12 * HOUR_IN_SECONDS);
        }
        return $open_table_cities;

    }

    function otw_widget_request_open_table_api() {

        //get restaurant name
        $restaurant = urlencode($_POST['restaurant']);
        $city = urlencode($_POST['city']);

        if ($_POST['restaurant'] && empty($city)) {
            // Send API Call using WP's HTTP API
            $data = wp_remote_get('http://opentable.herokuapp.com/api/restaurants?name=' . $restaurant);

            // Handle OTW response data
            echo $data["body"];

        } elseif ($_POST['city']) {

            // Send API Call using WP's HTTP API
            $data = wp_remote_get('http://opentable.herokuapp.com/api/restaurants?city=' . $city .'&name='. $restaurant);

            // Handle OTW response data
            echo $data["body"];

        }

        die(); // this is required to return a proper result

    }


    /**
     * Adds Open Table Widget Stylesheets
     */

    function add_otw_widget_scripts() {

        /**
         * CSS
         */
        if ($this->options["disable_css"] !== "on") {
            wp_register_style('otw_widget', plugins_url('assets/css/open-table-widget.css', dirname(__FILE__)));
            wp_enqueue_style('otw_widget');
        }
        /**
         * JS
         */
        wp_enqueue_script('jquery');

        //Datepicker
        wp_register_script('otw_datepicker_js', plugins_url('assets/js/jquery.datepicker.min.js', dirname(__FILE__), array('jquery')));
        wp_enqueue_script('otw_datepicker_js');


        //Select Menus
        if ($this->options["disable_bootstrap_select"] !== "on") {

            wp_register_script('otw_select_js', plugins_url('assets/js/jquery.bootstrap-select.min.js', dirname(__FILE__), array('jquery')));
            wp_enqueue_script('otw_select_js');

        }

        if ($this->options["disable_bootstrap_dropdown"] !== "on" && $this->options["disable_bootstrap_select"] !== "on") {
            wp_register_script('otw_dropdown_js', plugins_url('assets/js/jquery.bootstrap-dropdown.min.js', dirname(__FILE__)));
            wp_enqueue_script('otw_dropdown_js');
        }


        //Open Table Widget Specific Scripts
        wp_register_script('otw-widget-js', plugins_url('assets/js/open-table-widget.js', dirname(__FILE__), array('jquery')));
        wp_enqueue_script('otw-widget-js');
        $jsParams = array(
            'restaurant_id' => ''
        );
        wp_localize_script('otw-widget-js', 'otwParams', $jsParams);

    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    function widget($args, $instance) {
        extract($args);
        if (isset($instance['title'])) $title = apply_filters('widget_title', $instance['title']);
        $displayOption = $instance['display_option'];
        $widgetStyle = $instance['widget_style'];
        $restaurantName = $instance['restaurant_name'];
        $restaurantID = $instance['restaurant_id'];
        $restaurantIDs = $instance['restaurant_ids'];
        $hideLabels = $instance['hide_labels'];
        $preContent = $instance['pre_content'];
        $postContent = $instance['post_content'];
        $labelMultiple = $instance['label_multiple'];
        $labelCity = $instance['label_city'];
        $labelDate = $instance['label_date'];
        $labelTime = $instance['label_time'];
        $labelParty = $instance['label_party'];
        $inputSubmit = $instance['input_submit'];
        $widgetLanguage = $instance['widget_language'];
        $lookupCity = $instance['lookup_city'];


        //Determine widget display option
        if ($displayOption == 2) {
            //widget needs autocomplete scripts
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_style('otw_widget_jqueryui_css', plugins_url('assets/css/jquery-ui.min.css', dirname(__FILE__)));

        }
        /*
         * Output Widget Content
         */
        //Widget Style
        $style = "otw-" . sanitize_title($widgetStyle) . "-style";


        /* Add the width from $widget_width to the class from the $before widget
        http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget
        */
        // no 'class' attribute - add one with the value of width
        if (strpos($before_widget, 'class') === false) {
            $before_widget = str_replace('>', 'class="' . $style . '"', $before_widget);
        } // there is 'class' attribute - append width value to it
        else {
            $before_widget = str_replace('class="', 'class="' . $style . ' ', $before_widget);
        }

        /* Before widget */
        echo $before_widget;

        // if the title is set & the user hasn't disabled title output
        if (!empty($title)) {
            /* Add the width from $widget_width to the class from the $before widget
           http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget
           */
            // no 'class' attribute - add one with the value of width
            if (strpos($before_title, 'class') === false) {
                $before_title = str_replace('>', 'class="otw-widget-title"', $before_title);
            } // there is 'class' attribute - append width value to it
            else {
                $before_title = str_replace('class="', 'class="otw-widget-title ', $before_title);
            }

            echo $before_title . $title . $after_title;
        }
        ?>

        <div class="otw-<?php echo sanitize_title($widgetStyle); ?>">

            <?php include(OTW_PLUGIN_PATH . '/inc/widget-frontend.php'); ?>

        </div>
        <?php

        if (isset($after_widget)) echo $after_widget;

    }


    /**
     * @DESC: Saves the widget options
     * @SEE WP_Widget::update
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['display_option'] = strip_tags($new_instance['display_option']);
        $instance['widget_style'] = strip_tags($new_instance['widget_style']);
        $instance['restaurant_name'] = strip_tags($new_instance['restaurant_name']);
        $instance['restaurant_id'] = strip_tags($new_instance['restaurant_id']);
        $instance['restaurant_ids'] = $new_instance['restaurant_ids'];
        $instance['hide_labels'] = strip_tags($new_instance['hide_labels']);
        $instance['pre_content'] = strip_tags($new_instance['pre_content']);
        $instance['post_content'] = strip_tags($new_instance['post_content']);
        $instance['label_multiple'] = strip_tags($new_instance['label_multiple']);
        $instance['label_city'] = strip_tags($new_instance['label_city']);
        $instance['label_date'] = strip_tags($new_instance['label_date']);
        $instance['label_time'] = strip_tags($new_instance['label_time']);
        $instance['label_party'] = strip_tags($new_instance['label_party']);
        $instance['input_submit'] = strip_tags($new_instance['input_submit']);
        $instance['widget_language'] = strip_tags($new_instance['widget_language']);
        $instance['lookup_city'] = strip_tags($new_instance['lookup_city']);
        return $instance;
    }


    /**
     * Back-end widget form.
     * @see WP_Widget::form()
     */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $displayOption = $instance['display_option'];
        $widgetStyle = esc_attr($instance['widget_style']);
        $restaurantName = esc_attr($instance['restaurant_name']);
        $restaurantID = esc_attr($instance['restaurant_id']);
        $restaurantIDs = $instance['restaurant_ids'];
        $hideLabels = esc_attr($instance['hide_labels']);
        $preContent = esc_attr($instance['pre_content']);
        $postContent = esc_attr($instance['post_content']);
        $labelMultiple = esc_attr($instance['label_multiple']);
        $labelCity = esc_attr($instance['label_city']);
        $labelDate = esc_attr($instance['label_date']);
        $labelTime = esc_attr($instance['label_time']);
        $labelParty = esc_attr($instance['label_party']);
        $inputSubmit = esc_attr($instance['input_submit']);
        $widgetLanguage = esc_attr($instance['widget_language']);
        $lookupCity = esc_attr($instance['lookup_city']);
        //Get the widget form
        $widgetPath = OTW_PLUGIN_PATH . '/inc/widget-form.php';
        if (file_exists($widgetPath)) {
            include($widgetPath);
        }


    } //end form function


} //end Open_Table_Widget Class
