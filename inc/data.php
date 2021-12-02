<?php

namespace TheyWorkForYou;
use OpenPolitics\TWFYAPI\TWFYAPI;
use WP_REST_Server;

class TWFY_WP_API {

    protected $twfy_api;
    protected $cache_group = 'theyworkforyou';

    function __construct() {
        $options = \get_option( 'twfy_settings ');
        if ( ! isset( $options['twfy_api_key'] ) ) {
            return;
        }

        $this->twfy_api = new TWFYAPI( $options['twfy_api_key'] );
    }

    function init() {
        \register_rest_route(
            'twfy/v1',
            '/get_mps_names_for_dropdown/',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_mps_names_for_dropdown' ],
                'permission_callback' => '__return_true',
            ]
        );
    
    }

    function get_mps_names_for_dropdown() {

        // Grab the data from the cache if we can, to avoid processing.
        $mps = \wp_cache_get( __FUNCTION__, $this->cache_group );

        if ( ! $mps ) {
            $mps = $this->get_mps();
    
            // Sort the array so that MPs are listed alphabetically by name.
            usort( $mps, function( $a, $b ) { return $a->name > $b->name; } );
    
            // Remove the cruft we don't need, to reduce the size of the API response.
            foreach ( $mps as $mp ) {
                unset(
                    $mp->member_id,
                    $mp->party,
                    $mp->constituency,
                    $mp->office
                );
            }
            
            // Cache this so we don't need to do this processing every time.
            \wp_cache_set( __FUNCTION__, $mps, $this->cache_group, DAY_IN_SECONDS );
        }

        return \rest_ensure_response( $mps );
    }
    
    function get_mps() {

        // Grab MPs from the cache to save API requests.
        $mps = \wp_cache_get( __FUNCTION__, $this->cache_group );

        if ( ! $mps ) {
            $api_response = $this->twfy_api->query( 'getMPs', array( 'output' => 'json' ) );
            $mps = json_decode( $api_response );
            \wp_cache_set( __FUNCTION__, $mps, $this->cache_group, DAY_IN_SECONDS );
        }

        return $mps;
    }

    function get_hansard_by_person_id( $id ) {
        return json_decode( $this->twfy_api->query( 'getHansard', array( 'person' => $id, 'output' => 'json' ) ) );
    }

    function get_mp_by_person_id( $id ) {
        return json_decode( $this->twfy_api->query( 'getMP', array( 'id' => $id, 'output' => 'json' ) ) );
    }

}

$twfy_wp_api = new TWFY_WP_API();
\add_action( 'rest_api_init', [ $twfy_wp_api, 'init' ] );