<?php

define("SV_DEBUG", false);

class SV_Utilities {

	private function __construct() {}

	public static function blog_has_credential(){
		$sv_key = self::get_option('sv_key');
		return isset($sv_key);
	}

	public static function get_plugin_settings(){
		return get_option('samba-videos');
	}

	public static function get_embed_default_params(){
		$plugin_settings = SV_Utilities::get_plugin_settings();
		if ( !empty($plugin_settings) && is_array($plugin_settings) ) {
			return SV_Utilities::getEmbedUrlParams($plugin_settings);
		} else {
			return null;
		}
	}

	public static function get_option($key){
		return is_multisite() ? get_blog_option(get_current_blog_id(), $key, null) : get_option($key, null);
	}

	public static function get_player_hash(){
		return SV_Utilities::get_option('sv_playerKey');
	}

	public static function add_option($key, $value){
		return is_multisite() ? add_blog_option(get_current_blog_id(), $key, $value) : add_option($key, $value);
	}

	public static function update_option($key, $value){
		return is_multisite() ? update_blog_option(get_current_blog_id(), $key, $value) : update_option($key, $value);
	}

	public static function delete_option($key){
		return is_multisite() ? delete_blog_option(get_current_blog_id(), $key) : delete_option($key);
	}

	public static function remove_prefix_sv($array){
		$cleanArray = array();
		foreach ($array as $key => $value) {
			$cleanArray[str_replace("sv_","",$key)] = $value;
		}
		return $cleanArray;
	}

	public static function add_prefix_sv($array){
		$prefixedArray = array();
		foreach ($array as $key => $value) {
			$prefixedArray["sv_".$key] = $value;
		}
		return $prefixedArray;
	}

	public static function getEmbedSrc( $mediaId, $customUrlParams ) {

		$settings = SV_Utilities::get_plugin_settings();
		$embed_default_params = SV_Utilities::get_embed_default_params();

		if ( !empty($embed_default_params)) {
			$urlParams = array_filter(array_merge($embed_default_params, $customUrlParams));
		} else {
			$urlParams = array_filter($customUrlParams);
		}

		//removing prefix sv_
		$urlParams = SV_Utilities::remove_prefix_sv($urlParams);

		$pattern = '(\\{[[:alpha:]]+_*[[:alpha:]]+\\})';
		$playerUrlConfig = '{player_url}/{player_hash}/{media_id}/'; //default

		if ( !empty($settings) && array_key_exists('playerProxy', $settings)) {
			if ($settings['playerProxy']) {
				if ( !empty($settings['configPlayerProxy']) ){
					$playerUrlConfig = $settings['configPlayerProxy'];
				}
			}
		}

		preg_match_all("/".$pattern."/is", $playerUrlConfig, $matches);

		foreach ($matches[0] as $value) {

			switch ($value) {
				case '{player_url}':
					$playerUrlConfig = str_replace( '{player_url}', SAMBA_PLAYER_URL, $playerUrlConfig);
				break;

				case '{player_proxy}':
					$playerUrlConfig = str_replace( '{player_proxy}', $settings['playerProxy'], $playerUrlConfig);
				break;
				case '{player_hash}':
					$player_hash = SV_Utilities::get_player_hash();
					if ( !empty($player_hash) ) {
						$playerUrlConfig = str_replace( '{player_hash}', $player_hash, $playerUrlConfig);
					}
				break;
				case '{media_id}':
					$playerUrlConfig = str_replace( '{media_id}', $mediaId, $playerUrlConfig);
				break;
				case '{pid}':
					$playerUrlConfig = str_replace( '{pid}', $settings['pid'], $playerUrlConfig);
				break;
			}
		}

		if ( !empty($urlParams) && !empty($playerUrlConfig)) {
			$urlParams = urlencode_deep($urlParams);
			$playerUrlConfig = add_query_arg($urlParams, $playerUrlConfig); 
			return $playerUrlConfig;
		} else {
			return null;
		}
	}

	public static function getEmbedUrlParams($params){

		unset($params['mediaid']);
		unset($params['playerProxy']);
		unset($params['configPlayerProxy']);
		unset($params['sv_pid']);
		unset($params['sv_width']);
		unset($params['sv_height']);

		return $params;
	}

	/**
	 * Set all the constants that are used in plugin.
	 *
	 * @param    string    $SV_DEBUG       Define if the SV's datas will be mocked.
	 * @since    1.0.0
	 */
	public static function set_constants() {

		define("SV_AUTHENTICATED_PATH", "v1");
		define("SV_START_PARAMETERS", "/?");

		if (SV_DEBUG) {
			try {

				$file = file_get_contents( plugins_url( 'credentials.json', __FILE__ ));

				if ( !empty($file) ) {
					$json_credentials = json_decode($file, true);
				} else {
					throw new Exception(__('Error Processing Request', 'samba-videos'), 1);
				}

				define("SAMBA_PLAYER_URL", $json_credentials['player_url']);
				define("URL_SV_API", $json_credentials['sv_api_url']);
				define("SV_ACCESS_TOKEN", $json_credentials['access_token']);
				define("SV_PID", $json_credentials['sv_pid']);
			} catch (Exception $e) {
				define("SAMBA_PLAYER_URL", null);
				define("URL_SV_API", null);
				define("SV_ACCESS_TOKEN", null);
				define("SV_PID", null);
				die;
			}
		} else {
			$sv_key = SV_Utilities::get_option('sv_key');

			define("SAMBA_PLAYER_URL", 'http://fast.player.liquidplatform.com/pApiv2/embed');
			define("URL_SV_API", "http://sambavideos.sambatech.com/");

			if ( !empty($sv_key) ) {
				define("SV_ACCESS_TOKEN", $sv_key);
				define("SV_PID", get_option('samba-videos')['sv_pid']);
			}
		}
	}

	public static function resetOptions(){

		

	}

}
