<?php
/**
 * TWFY API wrapper
 *
 * Provides a wrapper around the TWFY API for use by the rest of the plugin, and extensions
 *
 * @since 0.4.0
 *
 * @package TheyWorkForYou
 * @subpackage Core
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

/**
 * Adds a wrapper to the TWFY API.
 *
 * @since 0.5.0
 * @access public
 */
Class TWFY_API {
	
	
	/**
	 * TWFY API key.
	 * 
	 * @since 0.4.0
	 * 
	 * @access private
	 * @var string $api_key API key for TheyWorkForYou as provided in settings
	 */
	private $api_key = '';
	
	/**
	 * Constructor.
	 *
	 * Sets the API key, by grabbing it from the settings.
	 *
	 * @since 0.5.0
	 */
	function __construct() {
		global $TWFY_Settings;
		
		// Get and set the API key
		$this->api_key = $TWFY_Settings->get_setting('twfy_api_key');
	}
	
	/**
	 * Get the cached data, if it exists.
	 * 
	 * Checks for a cached version of any request, returning it if so.
	 * Returns false if the cache is empty.
	 * 
	 * @since 0.5.0
	 * 
	 * @see generate_cache_id
	 * 
	 * @param string $cache_name Cache name generated by generate_cache_id
	 * 
	 * @return string|false Returns the cached data, or false if none exists
	 */
	function get_cache( $cache_name ) {
		
		if ( $cache = get_transient( $cache_name ) )
			return $cache;
		
		return false;
		
	}
	
	/**
	 * Caches a response from the TWFY API.
	 * 
	 * Sets a transient with our data and determines the cache lifetime
	 * based on which API method we've called.
	 * 
	 * @since 0.5.0
	 * 
	 * @param string $method The API method the data is generated by
	 * @param string $cache_name The twfy_{md5} cache name
	 * @param string $data Should be a bunch of XML, probably. Hopefully.
	 * 
	 * @return bool|WP_Error If the cache has been set, returns true
	 */
	function set_cache( $method, $cache_name, $data ) {
		
		switch ( $method ) {
			
			// @todo Add filters for the time limit on each method
			case "convertURL":
			case "getConstituency":
			case "getConstituencies":
			case "getPerson":
				$cached = set_transient( $cache_name, $data, strtotime('+1 month') );
				break;
				
			default:
				$cached = set_transient( $cache_name, $data, strtotime('+1 month') );
			
		}
		
		if ( ! $cached )
			return new WP_Error( 'cache_fail', __("Oops, we couldn't cache the API response.") );
		
		return true;
		
	}
	
	/**
	 * Generates the URL to pass to cURL.
	 * 
	 * @since 0.5.0
	 * 
	 * @param string $method The API method
	 * @param array $params GET/POST parameter to pass along
	 * 
	 * @return string TWFY API URL to be called, minus output format
	 */
	function generate_api_call( $method, $params = array() ) {
		
		if ( ! self::check_method( $method ) )
			return false;
		
		$url = 'http://www.theyworkforyou.com/api/';

		$url .= $method;

		$url .= '?key=' . $this->api_key;

		if ( ! empty( $params ) ):
		
			foreach ( $params as $param => $value ) {
				
				$param = urlencode( $param );
				$value = urlencode( $value );
				
				$url .= "&$param=$value";

			}
		
		endif;

		return $url;
		
	}
	
	/**
	 * Generates a simple ID to use as the cache name.
	 * 
	 * MD5's the API URL to produce something we can easily check on later.
	 * 
	 * @since 0.5.0
	 * 
	 * @see generate_api_call
	 * 
	 * @param string $api_call The URL generated by generate_api_call()
	 * 
	 * @return string A unique cache name for this API call
	 */
	function generate_cache_id( $api_call ) {
		
		// Sanitise, just in case
		$api_call = esc_url( $api_call );
		
		$call_hash = md5( $api_call );
		
		return "twfy_$call_hash";
		
	}
	
	/**
	 * Check API method.
	 * 
	 * A simple sanity check on the API method being requested,
	 * 
	 * @since 0.5.0
	 * 
	 * @param string $method The API method
	 * 
	 * @return bool True if allowed, False if not
	 */
	function check_method( $method ) {
		
		$allowed_methods = array(
				'convertURL',
				'getConstituency',
				'getConstituencies',
				'getPerson',
				'getMP',
				'getMPinfo',
				'getMPsInfo',
				'getMPs',
				'getLord',
				'getLords',
				'getMLA',
				'getMLAs',
				'getMSP',
				'getMSPs',
				'getGeometry',
				'getBoundary',
				'getCommittee',
				'getDebates',
				'getWrans',
				'getWMS',
				'getHansard',
				'getComments',
				);
		
		if ( in_array( $method, $allowed_methods ) )
			return true;
		
		return false;
		
	}
	
	/**
	 * Fires off the request and returns the output
	 * 
	 * @since 0.5.0
	 * 
	 * @param string $method The TWFY API method
	 * @param array $params An array of parameters to send along
	 * 
	 * @return string JSON response
	 */
	function get( $method, $params ) {

		$api_call = self::generate_api_call( $method, $params );

		$cache_name = self::generate_cache_id( $api_call );

		if ( $cache = self::get_cache( $cache_name ) ):
			$data = $cache;

		else :

			$data = self::get( $api_call );
			self::set_cache( $method, $cache_name, $data );

		endif;
		
		// @todo Do we allow for other formats?
		
		$url = $api_call . '&output=json';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close($ch);

		// @todo We need to check if it is actually a JSON string
		return json_decode( $data );
		
	}
	
	/**
	 * Validate a postcode.
	 * 
	 * Does a simple(!) regex to see if something is a UK postcode
	 * 
	 * @since 0.5.0
	 * 
	 * @param string $postcode String to check
	 * @return boolean
	 */
	function is_postcode( $postcode ) {
		
		$postcode = strtoupper(str_replace(' ','',$postcode));
		
		if ( preg_match("/^[A-Z]{1,2}[0-9]{2,3}[A-Z]{2}$/",$postcode) || preg_match("/^[A-Z]{1,2}[0-9]{1}[A-Z]{1}[0-9]{1}[A-Z]{2}$/",$postcode) || preg_match("/^GIR0[A-Z]{2}$/",$postcode) )
			return true;
			
		return false;
	}
	
	/**
	 * Converts a parliament.uk Hansard URL into a TheyWorkForYou one, if possible.
	 * 
	 * @since 0.5.0
	 * 
	 * @see http://www.theyworkforyou.com/api/docs/convertURL
	 * 
	 * @param string $url The Hansard URL to convert to a TWFY one.
	 * 
	 * @return object An object with two properties; gid and url
	 */
	function convertURL( $url ) {
		
		$url = esc_url( $url );

		$params = array( 'url' => $url );

		return self::get( __FUNCTION__, $params );
		
	}
	
	/**
	 * Fetch a UK Parliament constituency.
	 * 
	 * @since 0.5.0
	 * 
	 * @see http://www.theyworkforyou.com/api/docs/getConstituency
	 * 
	 * @param string $search Either a postcode or a name to search on
	 * @return object Constituency data - see TWFY API reference
	 */
	function getConstituency( $search ) {
		
		$search = esc_html( $search );
		
		$params = ( self::is_postcode( $search ) ) ? array( 'postcode' => $search ) : $params = array( 'name' => $search );

		return self::get( __FUNCTION__, $params );
		
	}
	
	/**
	 * Fetch a list of UK Parliament constituencies.
	 * 
	 * @since 0.5.0
	 * 
	 * @see http://www.theyworkforyou.com/api/docs/getConstituencies
	 * 
	 * @param string $search Either a postcode or a name to search on
	 * @return object Constituency data - see TWFY API reference
	 */
	function getConstituencies( $search = '', $date = null ) {
		
		$params = array();
		
		if ( ! is_null( $date ) )
			$params['date'] = esc_attr( $date );
		
		if ( ! empty( $search ) )
			$params['search'] = esc_html( $search );

		return self::get( __FUNCTION__, $params );
		
	}
	
	/**
	 * Fetch a particular person.
	 * 
	 * @since 0.5.0
	 * 
	 * @see http://www.theyworkforyou.com/api/docs/getPerson
	 * 
	 * @param string $id A TWFY Person ID
	 * @return object Person data - see TWFY API reference
	 */
	function getPerson( $id ) {

		$params = array( 'id' => intval( $id ) );

		return self::get( __FUNCTION__, $params );
		
	}

	/**
	 * Fetch a particular MP.
	 *
	 * @since 0.5.0
	 *
	 * @see http://www.theyworkforyou.com/api/docs/getMP
	 *
	 * @param array $options An array of possible parameters. See TWFY API docs
	 * @return object MP data - see TWFY API docs
	 */
	function getMP( $options = array() ) {

		if ( isset( $options['postcode'] ) && ! self::is_postcode( $options['postcode'] ) )
			return new WP_Error( 'invalid_postcode', __('Sorry, that\'s an invalid postcode') );

		if ( isset( $options['constituency'] ) )
			$options['constituency'] = esc_html( $options['constituency'] );

		if ( isset( $options['id'] ) && ! intval( $options['id'] ) )
			return new WP_Error( 'invalid_mp_id', __('Dude, that\'s not a valid ID... sort it out, yeah?') );

		// @todo Figure out what always_return does and check that
		// @todo What is the 'extra' parameter all about?

		return self::get( __FUNCTION__, $options );

	}
	
	/**
	 * Fetch extra information for a particular person.
	 * 
	 * @since 0.5.0
	 * 
	 * @see http://www.theyworkforyou.com/api/docs/getMPInfo
	 * 
	 * @param array $options An array of possible parameters. See TWFY API docs
	 * @return object MP data - see TWFY API docs
	 */
	function getMPInfo( $id, $fields = array() ) {
		
		$params = array();
		
		$params['id'] = intval( $id );
		
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				$params['fields'] .= '';
			}
		}

		return self::get( __FUNCTION__, $params );
		
	}

	/**
	 * Fetch extra information for a particular group of MPs.
	 *
	 * @since 0.5.0
	 *
	 * @see http://www.theyworkforyou.com/api/docs/getMPsInfo
	 *
	 * @param array $ids An array of Person IDs
	 * @param array $options An array of possible parameters. See TWFY API docs
	 * @return object MP data - see TWFY API docs
	 */
	function getMPsInfo( $ids, $fields = array() ) {

		$params = array();

		// Need an ID parameter
		$params['id'] = array();
		foreach ( $ids as $id ) {

			$params['id'][] = intval( $id );

		}

		// IDs needs to be a comma-separated
		$params['id'] = implode( ',', $params['id'] );

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				$params['fields'] .= '';
			}
		}

		return self::get( __FUNCTION__, $params );

	}

	/**
	 * Fetch a list of MPs.
	 *
	 * @since 0.5.0
	 *
	 * @see http://www.theyworkforyou.com/api/docs/getMPs
	 *
	 * @param array $options Associative array of parameters
	 * @return object MPs data - see TWFY API docs
	 */
	function getMPs( $options = array() ) {

		if ( isset( $options['party'] ) )
			$params['party'] = wp_kses( $options['party'] );

		if ( isset( $options['date'] ) )
			$params['date'] = esc_attr( $options['date'] );

		if ( isset( $options['search'] ) )
			$params['search'] = wp_kses( $options['search'] );

		return self::get( __FUNCTION__, $params );

	}

	/**
	 * Fetch a particular Lord.
	 *
	 * @since 0.5.0
	 *
	 * @see http://www.theyworkforyou.com/api/docs/getLord
	 *
	 * @param array $id Person ID for the particular Lord you want
	 * @return object Lord data - see TWFY API docs
	 */
	function getLord( $id = null ) {

		if ( ! intval( $id ) )
			return new WP_Error( 'invalid_id', __('Sorry, that\'s not a number!') );

		return self::get( __FUNCTION__, $options );

	}

	/**
	 * Fetch a list of Lords.
	 *
	 * @since 0.5.0
	 *
	 * @see http://www.theyworkforyou.com/api/docs/getLords
	 *
	 * @param array $options Associative array of parameters
	 * @return object Lords data - see TWFY API docs
	 */
	function getLords( $options = array() ) {

		if ( isset( $options['party'] ) )
			$params['party'] = wp_kses( $options['party'] );

		if ( isset( $options['date'] ) )
			$params['date'] = esc_attr( $options['date'] );

		if ( isset( $options['search'] ) )
			$params['search'] = wp_kses( $options['search'] );

		return self::get( __FUNCTION__, $params );

	}

	/**
	 * Fetch a particular MLA.
	 *
	 * @since 0.5.0
	 *
	 * @see http://www.theyworkforyou.com/api/docs/getMLA
	 *
	 * @param array $options An array of possible parameters. See TWFY API docs
	 * @return object MLA data - see TWFY API docs
	 */
	function getMLA( $options = array() ) {

		if ( isset( $options['postcode'] ) && ! self::is_postcode( $options['postcode'] ) )
			return new WP_Error( 'invalid_postcode', __('Sorry, that\'s an invalid postcode') );

		if ( isset( $options['constituency'] ) )
			$options['constituency'] = esc_html( $options['constituency'] );

		if ( isset( $options['id'] ) && ! intval( $options['id'] ) )
			return new WP_Error( 'invalid_mla_id', __('Dude, that\'s not a valid ID... sort it out, yeah?') );

		return self::get( __FUNCTION__, $options );

	}

	/**
	 * Fetch a list of MLAs.
	 *
	 * @since 0.5.0
	 *
	 * @see http://www.theyworkforyou.com/api/docs/getMLAs
	 *
	 * @param array $options Associative array of parameters
	 * @return object MLAs data - see TWFY API docs
	 */
	function getMLAs( $options = array() ) {

		if ( isset( $options['party'] ) )
			$params['party'] = wp_kses( $options['party'] );

		if ( isset( $options['date'] ) )
			$params['date'] = esc_attr( $options['date'] );

		if ( isset( $options['search'] ) )
			$params['search'] = wp_kses( $options['search'] );

		return self::get( __FUNCTION__, $params );

	}
	
}