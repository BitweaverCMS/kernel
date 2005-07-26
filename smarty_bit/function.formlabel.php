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
 */
function smarty_function_formlabel( $params,&$gBitSmarty ) {
	$atts = '';
	foreach($params as $key => $val) {
		switch( $key ) {
			case 'label':
				$name = $val;
				break;
			default:
				if( $val ) {
					$atts .= ' '.$key.'="'.$val.'"';
				}
				break;
		}			
	}
	$html = '<div class="formlabel">';
	if( $atts != '' ) {
		$html .= '<label'.$atts.'>';
	}
	$html .= tra( $name );
	if( $atts != '' ) {
		$html .= '</label>';
	}
	$html .= '</div>';
	return $html;
}
?>
