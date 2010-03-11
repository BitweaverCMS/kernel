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
function smarty_function_formfeedback( $params, &$gBitSmarty ) {
	detoxify( $params );
	if( !empty( $params['hash'] ) ) {
		$hash = &$params['hash'];
	} else {
		// maybe params were passed in separately
		$hash = &$params;
	}
	$feedback = '';
	$i = 0;
	$color = isset( $hash['color'] )?$hash['color']:"000000";
	foreach( $hash as $key => $val ) {
		if( $val ) {
			require_once $gBitSmarty->_get_plugin_filepath( 'function', 'biticon' );

			$keys = array( 'warning', 'success', 'error', 'important' );
			if( in_array( $key, $keys )) {
				if( $key === 'important' ) {
					$iname = 'emblem-important';
				} elseif( $key === 'success' ) {
					$iname = 'dialog-ok';
				} elseif( $key === 'warning' ) {
					$iname = 'dialog-warning';
				} elseif( $key === 'error' ) {
					$iname = 'dialog-error';
				}

				$biticon = array(
					'ipackage' => 'icons',
					'iname'    => $iname,
					'iexplain' => ucfirst( $key ),
					'iforce'   => 'icon',
				);
				if( !is_array( $val ) ) {
					$val = array( $val );
				}

				foreach( $val as $valText ) {
					$feedback .= '<p id="fat'.rand( 0, 10000 ).'" class="fade-'.$color.' '.$key.'">'.smarty_function_biticon( $biticon, $gBitSmarty ).' '.$valText.'</p>';
				}

			} else {
				/* unfortunately this plugin was written a little strictly and so it expects all params to be display text
				 * to allow setting of a background color we have to exclude that param when rendering out the html
				 * otherwise we'll render the color as text. -wjames5
				 */ 
				if ( $key != 'color' ){
					$feedback .= '<p class="'.$key.'">'.$val.'</p>';
				}
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
