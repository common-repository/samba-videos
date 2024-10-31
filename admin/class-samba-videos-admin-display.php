<?php

/**
 * The display-specific functionality of the plugin.
 *
 * @link       https://github.com/jefmoura
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 */

/**
 * The display-specific functionality of the plugin.
 *
 * Displays the information from settings screen
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/includes
 * @author     Jeferson Moura <jeferson.moura@sambatech.com.br>
 */
class Samba_Videos_Admin_Display {

	public function sv_display_section() {

	}

	public function sv_display_general_input($args) {

		foreach ($args as $key => $value) {
			${$key} = $value;
		}

		$options = SV_Utilities::get_option($option_group);

		//echo(print_r($args));
		//echo(print_r($options));
		//echo("  --------   ".$id." = ".$options[$id]);

	    //if ( !empty($options) && is_array($options) ) {
	    	if ( isset($options[$id]) ) {
		    	$options[$id] = stripslashes($options[$id]);  
		    	$options[$id] = esc_attr($options[$id]);
			} else {
				$options[$id] = '';
			}
		//} else {
			//wp_die( '<pre>' . __('There is an internal error in Samba Videos plugin. Deactivate it and contact our support!', 'samba-videos') . '</pre>' );
		//}



    	switch ( $type ) {
   			case 'text':
	            $html = "<input class='regular-text $class' type='text' id='$id' name='" . $option_group . "[$id]' value='".$options[$id]."' />";
	            $html .= ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
            	echo $html;
       		break;
       	  	case 'url':
	            $html = "<input class='regular-text $class' type='text' id='$id' name='" . $option_group . "[$id]' value='".esc_url($options[$id])."' />";
	            $html .= ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
            	echo $html;
       		break;
       	  	case 'checkbox':
	            $html  = "<label label_for='$label_for'>";
	            $html .= "<input class='$class' type='checkbox' $checked id='$id' name='" . $option_group . "[$id]' value='1' ";

            	if ( $options[$id] == true ){
              		$html .= " checked ";
            	}

        	    $html .= " />";

    	        $html .= ($desc != '') ? "<span class='description'>$desc</span>" : "";
	            $html .= "</label>"; 
	            echo $html;
       		break;
       		case 'select':

          		$html = "<select class='$class' id='$id' name='" . $option_group . "[$id]' >";

          		if ( isset($select_values) ){

          			foreach ($select_values as $key => $value) {
        	  			$html .= "<option value='$value' ";
    	      			if ($options[$id] == $value){
	          				$html .= " selected ";
	          			}
          				$html .= " >".$key."</option>";
          			}
          		}

          		$html .= "</select>";
          		$html .= ($desc != '') ? "<br /><span class='description'>$desc</span>" : ""; 
        	  	echo $html;
       	  	break;
   	      	case 'fields':

		        if ( isset($fields) && count($fields) ){

		           	$html .= "<table class='switch-box' ><tbody>";
					$html .= "<tr>";
	           		foreach ($fields as $key => $value) {
	           			$html .= "<td>";
	           			$html .= "<label for='".$value['label_for']."'>".$value['label']."</label><br/>";
	           			echo $html;
	           			$this->sv_display_general_input($value);
	       	    		$html = "</td>";
	            	}
		           	$html .= "</tr>";
		          	$html .= "</tbody></table>";
	           	}

	          	echo $html;
        	break;
         	case 'range':
    	  		$html = "<input class='$class' id='$id' type='range' name='" . $option_group . "[$id]' min='0' max='$max' value='$options[$id]' >";
	       		$html .= "<label for='$id'>$options[$id]</label>";
	           	$html .= ($desc != '') ? "<br /><span class='description'>$desc</span>" : ""; 
	           	echo $html;
        	break;
	       
	    }
	}
}
