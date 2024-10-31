<?php 

class Samba_Videos_App_Listen {
	/**
	*	@var string Pug Bomb Headquarters
	*/

	protected $endpoint = 'sambaapps';
	protected $method_activate = 'activate';
	protected $method_deactivate = 'deactivate';
	protected $method_status = 'status';
	
	
	/** Hook WordPress
	*	@return void
	*/
	public function __construct() {

		add_filter('query_vars', array($this, 'add_query_vars'), 0);
		add_action('parse_request', array($this, 'sniff_requests'), 0);
		add_action('init', array($this, 'add_endpoint'), 0);
	}	
	
	/** Add public query vars
	*	@param array $vars List of current public query vars
	*	@return array $vars 
	*/
	public function add_query_vars($vars) {
		$vars[] = $this->endpoint;
		$vars[] = 'method';
		return $vars;
	}
	
	public function add_endpoint() {

		add_rewrite_tag( '%user%', '([^&]+)' );
		add_rewrite_tag( '%access_token%', '([^&]+)' );
		add_rewrite_tag( '%status%', '([^&]+)' );

		add_rewrite_rule('^'.$this->endpoint.'/([^&]+)/?',
			'index.php?method=$matches[1]&user=$matches[2]&access_token=$matches[3]',
			'top');

		flush_rewrite_rules();
	}

	public function sniff_requests() {
		global $wp;

		if ( isset($wp->query_vars['method']) ) {

			switch ($wp->query_vars['method']) {
				case $this->method_activate:
					$this->handle_request_activate_token();
				break;
				case $this->method_deactivate:
					$this->handle_request_deactivate_token();
				break;
				case $this->method_status:
					$this->handle_request_status();
				break;
				default:
					$this->send_response('404 ERROR', array(
						'message' => 'Method not found'
					), 404);
				break;
			}
		}

	}

	protected function save_credential($access_token) {
		$result = SV_Utilities::add_option('sv_key', $access_token);
		if ( !empty($result) ) {
			return $result;
		} else {
			return false;
		}
	}

	protected function remove_credential($access_token) {
		
		$sv_key = SV_Utilities::get_option('sv_key');

		$result = false;

		if ($sv_key == $access_token){
			$result = SV_Utilities::delete_option('sv_playerKey');
			$result &= SV_Utilities::delete_option('sv_key');
		}
		
		return $result;
	}

	protected function handle_request_activate_token() {
		global $wp;
		$access_token = $wp->query_vars['access_token'];
		
		if( !isset($wp->query_vars['access_token']) || empty($access_token) ) {
			$this->send_response('400 ERROR', array(
				'message' => __('The parameter "access_token" has not been found.', 'samba-videos')
			), 400);
		} else {
			$sv_credential = SV_Utilities::blog_has_credential();
			if ( !empty($sv_credential) ) {
				$this->send_response('403 FORBIDDEN', array( 'message' => __('There is an access_token activated.', 'samba-videos') ) );
			} else {
				$this->save_credential( $access_token );
				$this->send_response('200 OK', array( 'message' => __('The access_token was added successfully.', 'samba-videos') ));
			}
		}
	}

	protected function handle_request_deactivate_token(){
		global $wp;
		$access_token = $wp->query_vars['access_token'];
		
		if (!isset($wp->query_vars['access_token']) || empty($access_token)) {
			$this->send_response('400 ERROR', array(
				'message' => __('The parameter "access_token" has not been found.', 'samba-videos')
			), 400);
		} else {

			if ( SV_Utilities::blog_has_credential() ) {
				$deleted = $this->remove_credential( $access_token );

				if ( $deleted ) {
					$this->send_response('200 OK', array( 'message' => __('The access_token was removed successfully.', 'samba-videos') ) );
				} else {
					$this->send_response('500 INTERNAL SERVER ERROR', array( 'message' => __('There has been an error in removing.', 'samba-videos') ) );
				}
				
			} else {
				$this->send_response('404 NOT FOUND', array( 'message' => __('This access_token was not still registered.', 'samba-videos')) );
			}
		}
	}


	protected function handle_request_status(){
		global $wp;
		$sv_key = SV_Utilities::get_option('sv_key');
		$this->send_response('200 OK', array( 'message' => "It's working!", 'parameters' => $wp->query_vars ));
	}
	
	/** Response Handler
	*	This sends a JSON response to the browser
	*/
	protected function send_response($msg, $parameters = '', $status_header = 200){
		$response['message'] = $msg;

		if( !empty($parameters) ) {
			$response['parameters'] = $parameters;
		}

		header('content-type: application/json; charset=utf-8');
		status_header( $status_header );
	    echo wp_json_encode($response)."\n";

	    exit;
	}
}
