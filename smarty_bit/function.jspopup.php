<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_jspopup
 */

/**
 * smarty_function_jspopup 
 * 
 * @param array $pParams hash of options 
 * @param srting $pParams[href] link the popup should open
 * @param srting $pParams[title] title of the link
 * @param srting $pParams[img] source of an image that is to be displayed instead of the title
 * @param srting $pParams[href]
 * @param srting $pParams[href]
 * @param array $gBitSmarty 
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function smarty_function_jspopup( $pParams, &$gBitSmarty ) {
	$ret = '';
	if( empty( $pParams['href'] ) ) {
		$gBitSmarty->trigger_error( 'assign: missing "href" parameter' );
	}

	if( empty( $pParams['title'] ) ) {
		$gBitSmarty->trigger_error( 'assign: missing "title" parameter' );
	} else {
		$title = empty( $pParams['notra'] ) ? $pParams['title'] : tra( $pParams['title'] );
	}

	$optionHash = array( 'type', 'width', 'height', 'gutsonly', 'img' );
	foreach( $pParams as $param => $val ) {
		if( !in_array( $param, $optionHash ) ) {
			if( $param != 'title' ) {
				$guts .= ' '.$param.'="'.$val.'"';
			} else {
				$guts .= ' '.$param.'="'.tra( 'This will open a new window: ' ).$title.'"';
			}
		}
	}

	if( !empty( $pParams['ibiticon'] ) ) {
		require_once $gBitSmarty->_get_plugin_filepath( 'function','biticon' );

		$tmp = explode( '/', $pParams['ibiticon'] );
		$ibiticon = array(
			'ipackage' => $tmp[0],
			'iname'    => $tmp[1],
			'iexplain' => $title,
		);

		if( !empty( $pParams['iforce'] ) ) {
			$ibiticon['iforce'] = $pParams['iforce'];
		}
		$img = smarty_function_biticon( $ibiticon, $gBitSmarty );
	}

	if( !empty( $pParams['img'] )) {
		$img = '<img src="'.$pParams['img'].'" alt="'.$title.'" title="'.$title.'" />';
	}

	if( !empty( $pParams['type'] ) && $pParams['type'] == 'fullscreen' ) {
		$js = 'popUpWin(this.href,\'fullScreen\');';
	} else {
		$js = 'popUpWin(this.href,\'standard\','.( !empty( $pParams['width'] ) ? $pParams['width'] : 600 ).','.( !empty( $pParams['height'] ) ? $pParams['height'] : 400 ).');';
	}

	$guts .= ' onkeypress="'.$js.'" onclick="'.$js.'return false;"';

	if( !empty( $pParams['gutsonly'] ) ) {
		return $guts;
	} else {
		return( '<a '.$guts.'>'.( !empty( $img ) ? $img : $title ).'</a>' );
	}
}
?>
