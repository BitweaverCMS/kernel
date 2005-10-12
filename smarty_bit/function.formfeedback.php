<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {formfeedback} function plugin
 *
 * Type:     function
 * Name:     formfeedback
 * Input:
 *           - warning, error or success are defined css styles, but you can feed it anything
 */
function smarty_function_formfeedback( $params,&$gBitSmarty ) {
	if( !empty( $params['hash'] ) ) {
		$hash = &$params['hash'];
	} else {
		// maybe params were passed in separately
		$hash = &$params;
	}
	$feedback = '';
	$i = 0;
	foreach( $hash as $key => $val ) {
		if( $val ) {
			require_once $gBitSmarty->_get_plugin_filepath('function','biticon');
			if( $key === 'warning' || $key === 'success' || $key === 'error' ) {
				$biticon = array(
					'ipackage' => 'liberty',
					'iname' => $key,
					'iexplain' => $key,
					'iforce' => 'icon',
				);
				if( !is_array( $val ) ) {
					$val = array( $val );
				}
				$feedback .= '<ul>';
				foreach( $val as $valText ) {
					$feedback .= '<li id="fat'.$i++.'" class="fade-000000 '.$key.'">'.smarty_function_biticon( $biticon,$gBitSmarty ).' '.$valText.'</li>';
				}
				$feedback .= '</ul>';
			} else {
				$feedback .= '<div class="'.$key.'">'.$val.'</div>';
			}
		}
	}

	$html = '';
	if( !empty( $feedback ) ) {
		$html = '<div class="clear formfeedback">';
		$html .= $feedback;
		$html .= '</div>';
	}
	return $html;
}
?>
