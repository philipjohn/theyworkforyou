<?php
/**
 * MPs Recent Activity block
 *
 * Contains the registration hook to create the MPs Recent Activity block.
 *
 * @since 0.4.0
 *
 * @package TheyWorkForYou
 * @subpackage Blocks
 */

function twfy_register_block_type() {
    register_block_type( TWFY_PLUGIN_DIR );
}
add_action( 'init', 'twfy_register_block_type' );