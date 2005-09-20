<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_jspopup
 */
function smarty_function_jspopup($params, &$gBitSmarty) {
	$ret = '';
	if( empty( $params['href'] ) ) {
		$gBitSmarty->trigger_error( 'assign: missing "href" parameter' );
	}

	if( empty( $params['title'] ) ) {
		$gBitSmarty->trigger_error( 'assign: missing "title" parameter' );
	}

	$optionHash = array( 'type', 'width', 'height', 'gutsonly' );
	foreach( $params as $param => $val ) {
		if( !in_array( $param, $optionHash ) ) {
			if( $param != 'title' ) {
				$guts .= ' '.$param.'="'.$val.'"';
			} else {
				$guts .= ' '.$param.'="'.tra( 'This will open a new window: ' ).tra( $params['title'] ).'"';
			}
		}
	}

	if( !empty( $params['type'] ) && $params['type'] == 'fullscreen' ) {
		$js = 'popUpWin(this.href,\'fullScreen\');';
	} else {
		$js = 'popUpWin(this.href,\'standard\','.( !empty( $params['width'] ) ? $params['width'] : 600 ).','.( !empty( $params['height'] ) ? $params['height'] : 400 ).');';
	}

	$guts .= ' onkeypress="'.$js.'" onclick="'.$js.'return false;"';

	if( !empty( $params['gutsonly'] ) ) {
		return $guts;
	} else {
		return( '<a '.$guts.'>'.tra( $params['title'] ).'</a>' );
	}
}
?>
