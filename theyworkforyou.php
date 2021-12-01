<?php
/*
Plugin Name: TheyWorkForYou for Wordpress
Plugin URI: http://philipjohn.me.uk/category/plugins/theyworkforyou/
Description: Provides tools for bloggers based on mySociety's TheyWorkForYou.com
Author: Philip John
Author URI: http://philipjohn.me.uk
Version: 1.0.0
Textdomain: theyworkforyou
*/

namespace TheyWorkForYou;

// Go away
if ( !function_exists( 'add_action' ) ) {
	die('Naughty naughty.');
}

/**
 * Define the plugin dir for use elsewhere.
 */
define( 'TWFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Add a settings page.
 */
require_once 'inc/admin/settings.php';

/**
 * Load the TWFY API library.
 */
require_once TWFY_PLUGIN_DIR . 'vendor/openpolitics/twfyapi/src/twfyapi.php';

/**
 * Set up our little API for grabbing data.
 */
require_once 'inc/data.php';

/**
 * Register the TWFY block.
 */
require_once 'inc/mps_recent_activity.block.php';