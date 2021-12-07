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
        \register_rest_route(
            'twfy/v1',
            '/get_mp_details_for_activity/(?P<id>[\d]+)/(?P<count>[\d]+)',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [ $this, 'rest_get_mp_details_for_activity' ],
                'permission_callback' => '__return_true',
                'args'                => [
                    'id' => [
                        'validate_callback' => 'is_numeric',
                    ],
                    'count' => [
                        'validate_callback' => 'is_numeric',
                    ],
                ]
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

    function get_mp_details_for_activity( $request ) {
        $id    = (int) $request['id'];
        $count = (int) $request['count'];
        
        $mp      = $this->get_mp_by_person_id( $id );
        $hansard = $this->trim_hansard_for_block_list(
            $this->get_hansard_by_person_id( $id, [ 'limit' => $count ] )->rows
        );
        
        return [ 'fullName' => $mp[0]->full_name, 'items' => $hansard ];
    }

    function rest_get_mp_details_for_activity( $request ) {
        return \rest_ensure_response(
            $this->get_mp_details_for_activity( $request )
        );
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

    function get_hansard_by_person_id( $id, $args = [] ) {
        $args = wp_parse_args( $args, [
            'limit' => 5,
            'order' => 'd',
        ] );
        return json_decode( $this->twfy_api->query( 'getHansard', [
            'person' => $id,
            'num'    => $args['limit'],
            'order'  => $args['order'],
            'output' => 'json',
        ] ) );
    }

    function get_mp_by_person_id( $id ) {
        return json_decode( $this->twfy_api->query( 'getMP', array( 'id' => $id, 'output' => 'json' ) ) );
    }

    function trim_hansard_for_block_list( $hansard_rows ) {

        $trimmed = [];

        for ( $i = 0; $i < count( $hansard_rows ); $i++ ) {
            $trimmed[ $i ] = [
                'id'      => esc_attr( $hansard_rows[ $i ]->gid ),
                'url'     => esc_url( 'https://theyworkforyou.com' . $hansard_rows[ $i ]->listurl ),
                'date'    => date( 'D, jS F Y', strtotime( $hansard_rows[ $i ]->hdate ) ),
                'context' => esc_html( $hansard_rows[ $i ]->parent->body ),
                'body'    => esc_html( strip_tags( $hansard_rows[ $i ]->extract ) ),
            ]; 
            
            if ( isset( $hansard_rows[ $i ]->htime ) ) {
                $trimmed[ $i ]['time'] = date( 'g:ia', strtotime( $hansard_rows[ $i ]->htime ) );
            }
        }

        return $trimmed;

    }

}

$twfy_wp_api = new TWFY_WP_API();
\add_action( 'rest_api_init', [ $twfy_wp_api, 'init' ] );