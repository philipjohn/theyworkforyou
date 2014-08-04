<?php
/**
 * TWFY Plugin Settings
 *
 * Adds the options page and settings fields
 *
 * @since 0.4.0
 *
 * @package TheyWorkForYou
 * @subpackage Core
 */

/**
 * Get the WSS library to make this whole thing easier.
 */
if ( ! class_exists('WordPress_SimpleSettings') )
	require( trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'lib/wordpress-simple-settings.php' );

class TWFY_Settings extends WordPress_SimpleSettings {
	var $prefix = 'twfy'; // this is super recommended

	function __construct() {
		parent::__construct(); // this is required

		// Actions
		add_action('admin_menu', array($this, 'menu') );

		$this->add_setting( 'twfy_api_key', '' );
		//register_activation_hook(__FILE__, array($this, 'activate') );
	}

	function menu () {
		add_options_page( __('TheyWorkForYou'), __('TheyWorkForYou'), 'manage_options', 'twfy', array($this, 'admin_page') );
	}

	function admin_page () {
		include 'settings.page.php';
	}

	function activate() {
		$this->add_setting( 'twfy_api_key', '' );
	}
}

$TWFY_Settings = new TWFY_Settings();