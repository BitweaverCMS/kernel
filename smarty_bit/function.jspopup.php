<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_jspopup
 */
function smarty_function_jspopup( $params, &$gBitSmarty ) {
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

	if( !empty( $params['ibiticon'] ) ) {
		require_once $gBitSmarty->_get_plugin_filepath( 'function','biticon' );
		$tmp = explode( '/', $params['ibiticon'] );
		$ibiticon = array(
			'ipackage' => $tmp[0],
			'iname' => $tmp[1],
			'iexplain' => $params['title'],
		);

		if( !empty( $params['iforce'] ) ) {
			$ibiticon['iforce'] = $params['iforce'];
		}
		$icon = smarty_function_biticon( $ibiticon, $gBitSmarty );
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
		return( '<a '.$guts.'>'.( !empty( $icon ) ? $icon : tra( $params['title'] ) ).'</a>' );
	}
}
?>
