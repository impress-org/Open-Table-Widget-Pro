=== Open Table Widget Pro ===
Contributors: wordimpress
Donate link: http://wordimpress.com/
Tags: open table, open table widget, open table form, open table reservations, reservations, restaurant, open table shortcode
Requires at least: 4.1
Tested up to: 4.5.3
Stable tag: 1.8.3

Open Table Widget makes it a breeze for you to add powerful Open Table reservation forms to your website via an easy to use and intuitive widget.

== Description ==

= Thank you for using Open Table Widget Pro Version =

Open Table Widget Pro is a significant upgrade to Open Table Widget that adds many features to the plugin.

[youtube http://www.youtube.com/watch?v=kWAVSxuNCl0]

[View the Online Demo](http://opentablewidget.wordimpress.com/ "View the Online Demo of Open Table Widget Pro")

= Open Table Widget =

*Open Table Widget* is a powerful WordPress widget that allows you to create powerful restaurant reservation forms for your website. Easily configure reservation forms that will look and function amazingly. Help increase the number of reservations and ultimately revenue by utilizing the power of Open Table.

Using the widget is easy! This plugin is includes helpful tooltips, videos and in depth documentation to get you quickly up and running. Open Table Widget is for WordPress users of all skill levels. Don't know CSS? No problem! There's no code required. We've baked in several amazing looking prebuilt form layouts ("themes") to make integrating reservation forms into any theme that much easier.

This plugin is actively developed and maintained and we welcome all suggestions, feedback and comments. If you enjoy this plugin be sure to rate it 5 stars and as "Working" to help get the word out.

= Disclaimer =

Be sure to test the reservation form. While we've done our best to code it for use in different website environments, we provide this code "as-is" and make no warranties, representations, covenants or guarantees with regard to the reservation tools, and will not be liable for any direct, indirect, incidental or consequential damages or for loss of profit, revenue, data, business or use arising out of your use of the reservation tools.

The developer of this plugin is in no way affiliated with Open Table the company or its affiliates. The code contained herein is developed for free use and distribution in an effort to give back to the WordPress community.

= Features =

1. Powerful and Intuitive Widget
2. Twitter Bootstrap Datepicker
3. A lightweight, customizable jQuery timepicker plugin inspired by Google Calendar
4. Autocomplete Open Table Restaurant ID lookup in widget admin
5. Customize Pre and Post Widget Content for additional SEO and user related content

== Installation ==

1. Upload the `open-table-widget` folder and it's contents to the `/wp-content/plugins/` directory or install via the WP plugins panel in your WordPress admin dashboard
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it! You should now be able to use the widget.

Note: If you have WordPress 2.7 or above (and I hope you are) you can simply go to 'Plugins' &gt; 'Add New' in the WordPress admin and search for "Open Table Widget" and install it from there.

== Frequently Asked Questions ==

= Why should I use this plugin =

If you want to include reservation integration into Open Table restaurant from a WordPress powered site, this plugin makes it easy and flexible. Compared to Open Table's own Reservation widget it's night and day.

= Why does the widget look funny in my theme? =

Most likely your theme has conflicting CSS that is interfering with the themes included with the plugin. If you're handy with CSS this can be an easy fix. If you are new to CSS then try out the Bare Bones theme to see if that looks nicer. Otherwise, you may have to hit up your coding friends to help you out.

= Are style issue supported? =

Not for the free plugin. If you are experiencing a style issue and need help, either upgrade to the Premium version and we will do our darndest to get it set up nicely for your theme.

= Are there prebuilt styles included in the plugin? =

Yes, there are three basic themes included in the free version of the plugin. The premium version has many more options and themes.

== Screenshots ==

1. The "Minimal Light" widget theme view on the frontend of WordPress

2. The "Minimal Dark" widget theme view on the frontend of WordPress

3. A view of the widget in the WP Admin widgets view version 1.1 (it may look different depending on your version).

== Changelog ==

= 1.8.3 =
* Set front-end widget scripts to only load on the frontend. Necessary for themes like Divi that have backend builders that load widgets live in the post edit screen. https://github.com/WordImpress/Open-Table-Widget-Pro/issues/28
* Make Predefined field tooltip more accurate. https://github.com/WordImpress/Open-Table-Widget-Pro/issues/27

= 1.8.2 =
* Fix: Don't allow widget settings to be translated. Prevents widget language from being output in a translated format, thus breaking the JS.
* Fix: Also remove internationalization from Widget Theme settings since they are converted into markup.

= 1.8.1 =
* Hotfix for supporting international restaurants correctly.

= 1.8 =
* New: Uses Selectric.js instead of Bootstrap for select fields. This has better reliability with themes.
* Enhancement: Disable the keyboard for the datepicker on mobile devices.
* Enhancement: Set the date field to default to today.
* Enhancement: Selected date is highlighted clearly now.
* Enhancement: Datepicker.js updated to latest version.
* Fix: Disabling CSS or JS options in Settings won't negatively affect the other any longer.

= 1.7.4 =
* Removed protocol from form action for HTTPS compliance

= 1.7.3 =
* Add otw_load_textdomain() to load localized translations

= 1.7.2 =
* Fix: otw_get_cities() throwing a fatal_error when unable to curl data @see https://wordimpress.com/support/topic/cant-access-plugins-with-this-widget-running/#post-23659

= 1.7.1 =
* Fix: AJAX privileges error on frontend restaurant lookup functionality

= 1.7 =
* New: Updated Open Table branding to their new logo
* Fix: Fixed broked "activate your license" link in license notice
* Update: Prefixed the "dropdown" class usage to help out theme authors
* Update: Prefixed the "bootstrap-success" class usage to help out theme authors
* Update: Prefixed the "btn-"* class usage to help out theme authors
* Update: Prefixed the "selectpicker" class usage to help out theme authors
* Update: Prefixed the "datepicker-"* class usage and revised js script to help out theme authors

= 1.6.2 =
* Improved: Debugging now works from SCRIPT_DEBUG rather than plugin specific constant
* Improved: The datepicker now does not allow a past date to be selected
* Improved: The widget's reservation date field now has today set as the default date placeholder
* Fix: Broken CSS for licensing field within the plugin settings panel

= 1.6.1 =
* New: Set your default party size to any number you prefer; works with shortcode and widget
* Update: Minor style updates to admin widget form
* Update: Text domain to 'open-table-widget' rather than the old 'otw'
* Update: Increased shortcode's default max_seats variable to 12 by default

= 1.6.0.3 =
* Fix: Issue with the plugin settings page CSS 404 - it should look much better now!

= 1.6.0.2 =
* Update: Ensure update process from the free version to pro version doesn't drop user's options or kick user back to free version if license isn't activated.
* Update: Fixed issue with WP Engine caching and occasional issues with cached license activation responses
* Update: EDD_SL_Plugin_Updater.php to v1.5

= 1.6.0.1 =
* Hot Fix: Added Max Seats variable to shortcode

= 1.6 =
* Fixed: Replaced datepicker for better compatibility with jQueryUI
* Feature Added: Added Maximum Seats option
* Enhancement: Unified textdomain and added default English.po file
* Enhancement: Added German language (Special thanks to Quirin Pils (http://www.pixelchiefs.de)).

= 1.5.3 =
* Fixed: Issue with activating license
* New: Minor style improvements to the license key valid input returned
* New: Link to settings page from license key notice

= 1.5.2 =
* Fixed: PHP warning in handle_shortcode function
* Fixed: PHP warning - Strict Standards: Non-static method should not be called statically for shortcode

= 1.5.1 =
* Fix: Widget admin JS toggle now works with "accessibility mode" enabled
* Minor improvements to licensing code

= 1.5 =
* New: Reservation start time and end fields
* New: Reservation time increment field
* New: Reservation default time field
* New: Shortcode options for new time fields
* Updated: Shortcode documentation in the plugin docs
* Fixed: Issue with multiple widgets on page and conflicting restaurant selections

= 1.4 =
* New: Debug constant added for easier script and css debugging by developers
* Improved: datepicker JS minified
* Improved: Licensing improved greatly from

= 1.3 =
* Fixed: Issue with jquery autocomplete error when not using city lookup method
* Fixed: Issue with widget specific Location and Language selection not properly saving
* Fixed: Issue with widget title replacement method not working with various themes
* Improved: Datepicker handling method
* Improved: City lookup now specifically targets select form input to ensure compatibility with various themes
* Improved: Minor CSS updates to themes

= 1.2.1 =
* Licensing improvements for reliability

= 1.2 =
* New: Linked to video tutorial for finding Open Table restaurant ID
* Tested plugin with WordPress 3.8 for compatibility
* Updated: jQuery Datepicker plugin to latest version

= 1.1 =
* Fixed issue with Shortcode not displaying properly within post content

= 1.0 =
* Initial Pro version release