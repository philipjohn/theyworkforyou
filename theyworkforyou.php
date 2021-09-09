<?php
/*
Plugin Name: TheyWorkForYou for Wordpress
Plugin URI: http://philipjohn.me.uk/category/plugins/theyworkforyou/
Description: Provides tools for bloggers based on mySociety's TheyWorkForYou.com
Author: Philip John
Author URI: http://philipjohn.me.uk
Version: 0.4.2

Future features list;
 * Custom date format

*/
/*  Copyright 2009  Philip John Ltd  (email : talkto@philipjohn.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the Do What The Fuck You Want To Public License
    (WTFPL).

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

    You should have received a copy of the Do What The Fuck You Want To
    Public License along with this program; if not, see
    http://wtfpl.net
*/

// Go away
if ( !function_exists( 'add_action' ) ) {
	die('Naughty naughty.');
}

/**
 * Load the Settings page up
 */
require_once 'inc/settings.php';


/**
 * Only allow the configuration of widgets etc when an API key is set
 */
if ( $TWFY_Settings->get_setting('twfy_api_key') ) {

	/**
	 * Get the MPs Recent Activity widget
	 */
	require_once 'inc/mps_recent_activity.widget.php';

} else {

	/**
	 * Remind the user to add their API key
	 *
	 * Adds an admin notice to to the dashboard, prompting users to enter their
	 * API key. Not entering one disables widgets etc.
	 *
	 * @since 0.4.0
	 *
	 * @see admin_notices
	 * @url http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 */
	function twfy_admin_notice() {
		?>
	    <div class="updated">
	        <p><?php _e( 'Uh-oh, you haven\'t entered your TheyWorkForYou API Key! Please <a href="options-general.php?page=twfy">do so</a>.' ); ?></p>
	    </div>
	    <?php
	}
	add_action( 'admin_notices', 'twfy_admin_notice' );


} // twfy_api_key

/**
 * Add a settings link to the plugin page
 *
 * Provides quicker and easier access to the settings page after activating the plugin.
 *
 * @see plugin_action_links_{$plugin}
 * @url http://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
 *
 * @param array $links Links for this plugin
 * @return array Modified links for this plugin
 */
function twfy_add_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=twfy">Settings</a>';
	array_push( $links, $settings_link );
	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'twfy_add_settings_link' );

?>
