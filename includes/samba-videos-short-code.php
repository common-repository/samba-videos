<?php

function samba_player_short_code($custom_attrs){

	if ( !isset($custom_attrs['mediaid']) )
		return "";

	//getting and removing mediaId
	$mediaId = $custom_attrs['mediaid'];
	unset($custom_attrs['mediaid']);

	//changing "autostart" to "autoStart"
	$custom_attrs['autoStart'] = $custom_attrs['autostart'];
	unset($custom_attrs['autostart']);

	$custom_attrs = SV_Utilities::add_prefix_sv($custom_attrs);
	$defaults = SV_Utilities::get_plugin_settings();

	foreach ($defaults as $key => $value) {
		switch ($key) {
			case 'sv_html5':
			case 'sv_autoStart':
			case 'sv_resume':
			case 'sv_cast':
				$defaults[$key] = ($value) ? 'true' : 'false';
			break;
		}
	}

	if ( !empty($defaults) ) {
		$attrs = array_merge($defaults, $custom_attrs);
	} else {
		$attrs = null;
	}

	$width 			= array_key_exists('sv_width', $attrs) && !empty($attrs['sv_width']) 	? $attrs['sv_width'] 	: 640; //fallback
	$height 		= array_key_exists('sv_height', $attrs) && !empty($attrs['sv_height']) 	? $attrs['sv_height']	: 360; //fallback

	if ( is_array($attrs) ) {
		$embedParams 	= SV_Utilities::getEmbedUrlParams($attrs);
	}

	if ( !empty($mediaId) ) {
		if ( is_array($embedParams) ) {
			$src 		= SV_Utilities::getEmbedSrc( $mediaId, $embedParams );
		} else {
			$src 		= SV_Utilities::getEmbedSrc( $mediaId, array() );
		}
	} else {
		$src 		= null;
	}

    $html  = '<iframe allowfullscreen webkitallowfullscreen mozallowfullscreen ';
    $html .= ' width="'	.$width. '" height="'.$height.'" ';
	$html .= ' src="'.$src.'" scrolling="no" frameborder="0" ></iframe>';

	return $html;
}

add_shortcode( 'samba-player', 'samba_player_short_code' );
