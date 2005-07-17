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
function smarty_function_formfeedback( $params,&$smarty ) {
	if( !empty( $params['hash'] ) ) {
		$hash = &$params['hash'];
	} else {
		// maybe params were passed in separately
		$hash = &$params;
	}
	$feedback = '';
	foreach( $hash as $key => $val ) {
		if( $val ) {
			require_once $smarty->_get_plugin_filepath('function','biticon');
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
					$feedback .= '<li class="'.$key.'">'.smarty_function_biticon( $biticon,$smarty ).' '.$valText.'</li>';
				}
				$feedback .= '</ul>';
			} else {
				$feedback .= '<div class="'.$key.'">'.$val.'</div>';
			}
		}
	}

	$html = '';
	if( !empty( $feedback ) ) {
		$html = '<div class="formfeedback">';
		$html .= $feedback;
		$html .= '</div>';
	}
	return $html;
}
?>
