<?php

/**
 * The proxy-specific functionality of the plugin.
 *
 * @link       https://github.com/jefmoura
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 */

/**
 * The proxy-specific functionality of the plugin.
 *
 * Defines the methods to make HTTP requests on Samba Videos plataform
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 * @author     Jeferson Moura <jeferson.moura@sambatech.com.br>
 */
class Samba_Videos_Proxy {

	/**
	 * Build URL to make HTTP requests on Samba Videos plataform
	 *
	 * @since    1.0.0
	 */
	public function build_sv_url($request_params, $endpoint){
		$url = URL_SV_API . SV_AUTHENTICATED_PATH . $endpoint . SV_START_PARAMETERS;

		if ( !empty($request_params) ){
			$request_params = urlencode_deep($request_params);
			if ( !empty($request_params) ) {
				$url = add_query_arg($request_params, $url);
			}
		}

		return $url;
	}

	public function ajax_proxy_sv() {

		$request_params = wp_parse_args( stripslashes_deep( $_POST ), array(
			'title'  => null,
			'type' => 'VIDEO',
			'start' => 0,
			'pid' => (defined('SV_PID') ? SV_PID : 0),
			'published' => true,
			'access_token' => (defined('SV_ACCESS_TOKEN') ? SV_ACCESS_TOKEN : ''),
		) );
		
		if ( !empty($request_params) && is_array($request_params) ) {
			$url = $this->build_sv_url($request_params, '/medias');
		} else {
			$url = $this->build_sv_url(null, '/medias');
		}

		try {
	        $response = wp_remote_get($url, array(
        		'timeout'     => 1000
        	));
	    } catch ( Exception $ex ) {
	    	echo($ex);
	        $response = null;
	    }

	    //O método wp_json_encode estava retornando sempre um objeto com valores vazios 
	    //{ body: null, headers: null, status: null }

	    $proxy_response['body'] = $response['body'];
	    $proxy_response['headers'] = $response['headers'];

	    wp_send_json($proxy_response);

		/*header( 'Content-type: application/json' );
		if ( is_wp_error( $response ) ) {
			echo 'No data is available.';
			die;
		} else {
			die( wp_json_encode($response) );
		}*/
	}

	/**
	 * Get all user's projects from SambaVideos
	 *
	 * @since    1.0.0
	 */
	public function list_projetcs(){

		$request_params = array(
			'access_token' => (defined('SV_ACCESS_TOKEN') ? SV_ACCESS_TOKEN : ''),
		);

		if ( !empty($request_params) && is_array($request_params) ) {
			$url = $this->build_sv_url($request_params, '/projects');
		} else {
			$url = $this->build_sv_url(array(), '/projects');
		}
		
		$response = wp_remote_get($url);

		if ( !is_wp_error( $response ) ) {
			if ( !empty($response) ){
				if ( isset($response['body']) ){
					return $response['body'];
				}
			}
		}
		
		return "[]";
	}

}
