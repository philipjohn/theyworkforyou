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

// Go away
if ( !function_exists( 'add_action' ) ) {
	die('Naughty naughty.');
}

/**
 * Define the plugin dir for use elsewhere.
 */
define( 'TWFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once 'inc/admin/settings.php';

/**
 * Register the TWFY block.
 */
require_once 'inc/mps_recent_activity.block.php';