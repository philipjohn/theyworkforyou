<?php

namespace TheyWorkForYou;
use TWFYAPI;

error_log(var_export(['pj-debug',__FILE__,__LINE__,class_exists('TWFYAPI')],true));
error_log(var_export(['pj-debug',__FILE__,__LINE__,new TWFYAPI('foobar')],true));

class TWFY_WP_API {

    private $twfy_api;

    function __construct() {
        $options = get_option( 'twfy_settings ');
        if ( ! isset( $options['twfy_api_key'] ) ) {
            return;
        }

        // $this->twfy_api = new TWFYAPI( $options['twfy_api_key'] );
    }

    function init() {
        register_rest_route(
            'twfy/v1',
            '/get_mps/',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_mps' ],
                'permission_callback' => [ $this, 'permissions_check' ],
            ]
        );
    
    }
    
    function get_mps() {
        return \rest_ensure_response( $this->twfy_api )
            ->query( 'getMPs', array( 'output' => 'json' ) );
    }

}

$twfy_wp_api = new TWFY_WP_API();
// add_action( 'rest_api_init', [ $twfy_wp_api, 'init' ] );