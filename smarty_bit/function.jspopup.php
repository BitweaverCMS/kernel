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
	global $gBitThemes;

	$ret = '';
	if( empty( $pParams['href'] ) ) {
		return( 'assign: missing "href" parameter' );
	}

	if( empty( $pParams['title'] ) ) {
		return( 'assign: missing "title" parameter' );
	} else {
		$title = empty( $pParams['notra'] ) ? $pParams['title'] : tra( $pParams['title'] );
	}

	if( empty( $pParams['text'] )){
		$text = $title;
	} else {
		$text = empty( $pParams['notra'] ) ? $pParams['text'] : tra( $pParams['text'] );
		// remove it from the hash since later the params are looped over for to formulate the a tag
		unset( $pParams['text'] );
	}


	$optionHash = array( 'type', 'width', 'height', 'gutsonly', 'img' );
	foreach( $pParams as $param => $val ) {
		if( !in_array( $param, $optionHash ) ) {
			if( $param == 'title' ) {
				$guts .= ' '.$param.'="'.tra( 'This will open a new window: ' ).$title.'"';
			}elseif( $param == 'href' && $gBitThemes->isJavascriptEnabled()){
				$guts .= ' '.$param.'="javascript:void(0)"';
			} else {
				$guts .= ' '.$param.'="'.$val.'"';
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
		$img_size = NULL;
		$file = str_replace( '//', '/', BIT_BASE_PATH.( str_replace( BIT_ROOT_URI, '', urldecode( $pParams['img'] ))));
		if( is_file( $file )) {
			if( $imgSizeHash = @getimagesize( $file )) {
				$img_size = $imgSizeHash[3];
			}
		}
		$img = '<img src="'.$pParams['img'].'" alt="'.$title.'" title="'.$title.'" '.$img_size.' />';
	}

	if( !empty( $pParams['type'] ) && $pParams['type'] == 'fullscreen' ) {
		$js = 'popUpWin(\''.$pParams['href'].'\',\'fullScreen\');';
	} else {
		$js = 'popUpWin(\''.$pParams['href'].'\',\'standard\','.( !empty( $pParams['width'] ) ? $pParams['width'] : 600 ).','.( !empty( $pParams['height'] ) ? $pParams['height'] : 400 ).');';
	}

	// deprecated slated for removal - onkeypress causes focus problems with browsers - if this is an ada issue get in touch -wjames5.
	// $guts .= ' onkeypress="'.$js.'" onclick="'.$js.'return false;"';
	$guts .= ' onclick="'.$js.'return false;"';

	if( !empty( $pParams['gutsonly'] ) ) {
		return $guts;
	} else {
		return( '<a '.$guts.'>'.( !empty( $img ) ? $img : $text ).'</a>' );
	}
}
