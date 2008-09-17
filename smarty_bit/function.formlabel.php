<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {formlabel} function plugin
 *
 * Type:     function
 * Name:     formlabel
 * Input:
 *           - label       (required) - words that are displayed
 *		  - mandatory (optional) - add a class formmandatory in the div
 */
function smarty_function_formlabel( $params,&$gBitSmarty ) {
	$atts = '';
	foreach($params as $key => $val) {
		switch( $key ) {
			case 'label':
				$name = $val;
				break;
			case 'mandatory':
				$mandatory = true;
			default:
				if( $val ) {
					$atts .= ' '.$key.'="'.$val.'"';
				}
				break;
		}			
	}
	$html = '<div class="formlabel';
	if (isset($mandatory) && $mandatory) {
		$html .= ' formmandatory';
	}
	$html .= '">';
	if( $atts != '' ) {
		$html .= '<label'.$atts.'>';
	}
	if( empty( $params['no_translate'] ) ) {
		$html .= tra( $name );
	} else {
		$html .= $name;
	}
	if( $atts != '' ) {
		$html .= '</label>';
	}
	$html .= '</div>';
	return $html;
}
?>
