<?php

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../theyworkforyou.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

function _set_twfy_api_key( $value, $setting = '' ) {

	if ( 'twfy_api_key' == $setting )
		return 'CmbGj2CFU7JQEa8WtTBL2Hou';

	return $value;
}
tests_add_filter( 'twfy_get_setting', '_set_twfy_api_key' );

require $_tests_dir . '/includes/bootstrap.php';

