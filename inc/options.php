<?php
$open_table_widget_options = array(
    array('name' => __('About', $open_table_widget->textdomain), 'type' => 'opentab'),
    array('type' => 'about'),
    array('type' => 'closetab', 'actions' => false),

    //Widgets Default Tab
    array(
        'name' => __('Widget Defaults', $open_table_widget->textdomain),
        'type' => 'opentab'
    ),
    array(
        'name' => __('Widget Default Location', $open_table_widget->textdomain),
        'desc' => __('Select the default location and language you would like users to see on Open Table. Please note that this can be defined per widget.', $open_table_widget->textdomain),
        'options' => array(
            'ca-eng' => __('Canada - English', $open_table_widget->textdomain),
//            'ca-fre' => __('Canada - French', $open_table_widget->textdomain),
            'ger-ger' => __('Germany - German', $open_table_widget->textdomain),
            'ger-eng' => __('Germany - English', $open_table_widget->textdomain),
            'uk' => __('United Kingdom', $open_table_widget->textdomain),
            'us' => __('United States', $open_table_widget->textdomain),
            'jp-jp' => __('Japan - Japanese', $open_table_widget->textdomain),
            'jp-eng' => __('Japan - English', $open_table_widget->textdomain),
            'mx-mx' => __('Mexico - Spanish', $open_table_widget->textdomain),
            'mx-eng' => __('Mexico - English', $open_table_widget->textdomain),
        ),
        'std' => 'us',
        'id' => 'default-location',
        'type' => 'select'
    ),
    array('type' => 'closetab'),

    //Advanced Options
    array(
        'name' => __('Advanced Options', $open_table_widget->textdomain),
        'type' => 'opentab'
    ),

    array(
        'name' => __('Disable Plugin CSS', $open_table_widget->textdomain),
        'desc' => __('Useful to style your own widget and for theme integration and optimization.', $open_table_widget->textdomain),
        'std' => '',
        'id' => 'disable_css',
        'type' => 'checkbox',
        'label' => __('Yes', $open_table_widget->textdomain)
    ),
    array(
        'name' => __('Disable Bootstrap select fields', $open_table_widget->textdomain),
        'desc' => __('The select fields will be replaced by standard HTML select fields rather than the Twitter Bootstrap ones. For more information about the bootstrap dropdowns.', $open_table_widget->textdomain),
        'std' => '',
        'id' => 'disable_bootstrap_select',
        'type' => 'checkbox',
        'label' => __('Yes', $open_table_widget->textdomain)
    ),
    array(
        'name' => __('Disable Bootstrap dropdown JS', $open_table_widget->textdomain),
        'desc' => __('Themes built with Twitter Bootstrap will already have support for the Bootstrap dropdowns. If this is your case, check this to remove the dropdown script from being output.', $open_table_widget->textdomain),
        'std' => '',
        'id' => 'disable_bootstrap_dropdown',
        'type' => 'checkbox',
        'label' => __('Yes', $open_table_widget->textdomain)
    ),
    array('type' => 'closetab')
);