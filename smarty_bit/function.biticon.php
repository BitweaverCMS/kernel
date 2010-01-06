<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @link http://www.bitweaver.org/wiki/function_biticon function_biticon
 */

/**
 * biticon_first_match 
 * 
 * @param string $pDir Directory in which we want to search for the icon
 * @param array $pFilename Icon name without the extension
 * @access public
 * @return Icon name with extension on success, FALSE on failure
 */
function biticon_first_match( $pDir, $pFilename ) {
	if( is_dir( $pDir )) {
		global $gSniffer;

		// if this is MSIE < 7, we try png last.
		if( $gSniffer->_browser_info['browser'] == 'ie' && $gSniffer->_browser_info['maj_ver'] < 7 ) {
			$extensions = array( 'gif', 'jpg', 'png' );
		} else {
			$extensions = array( 'png', 'gif', 'jpg' );
		}

		foreach( $extensions as $ext ) {
			if( is_file( $pDir.$pFilename.'.'.$ext ) ) {
				return $pFilename.'.'.$ext;
			}
		}
	}
	return FALSE;
}

/**
 * Turn collected information into an html image
 * 
 * @param boolean $pParams['url'] set to TRUE if you only want the url and nothing else
 * @param string $pParams['iexplain'] Explanation of what the icon represents
 * @param string $pParams['iforce'] takes following optins: icon, icon_text, text - will override system settings
 * @param string $pFile Path to icon file
 * @param string iforce  override site-wide setting how to display icons (can be set to 'icon', 'text' or 'icon_text')
 * @access public
 * @return Full <img> on success
 */
function biticon_output( $pParams, $pFile ) {
	global $gBitSystem;
	$iexplain = isset( $pParams["iexplain"] ) ? tra( $pParams["iexplain"] ) : 'please set iexplain';

	if( empty( $pParams['iforce'] )) {
		$pParams['iforce'] = NULL;
	}

	if( isset( $pParams["url"] )) {
		$outstr = $pFile;
	} else {
		if(( $gBitSystem->getConfig( 'site_biticon_display_style' ) == 'text' || $pParams['iforce'] == 'text' ) && $pParams['iforce'] != 'icon' ) {
			$outstr =  $iexplain;
		} else {
			$outstr='<img src="'.$pFile.'"';
			if( isset( $pParams["iexplain"] ) ) {
				$outstr .= ' alt="'.tra( $pParams["iexplain"] ).'" title="'.tra( $pParams["iexplain"] ).'"';
			} else {
				$outstr .= ' alt=""';
			}

			$ommit = array( 'ilocation', 'ipackage', 'ipath', 'iname', 'iexplain', 'iforce', 'istyle', 'iclass' );
			foreach( $pParams as $name => $val ) {
				if( !in_array( $name, $ommit ) ) {
					$outstr .= ' '.$name.'="'.$val.'"';
				}
			}

			if( !isset( $pParams["iclass"] ) ) {
				$outstr .= ' class="icon"';
			} else {
				$outstr .=  ' class="'.$pParams["iclass"].'"';
			}

			if( isset( $pParams["onclick"] ) ) {
				$outstr .=  ' onclick="'.$pParams["onclick"].'"';
			}

			// insert image width and height
			list( $width, $height, $type, $attr ) = @getimagesize( BIT_ROOT_PATH.$pFile );
			if( !empty( $width ) && !empty( $height ) ) {
				$outstr .= ' width="'.$width.'" height="'.$height.'"';
			}

			$outstr .= " />";

			if( $gBitSystem->getConfig( 'site_biticon_display_style' ) == 'icon_text' && $pParams['iforce'] != 'icon' || $pParams['iforce'] == 'icon_text' ) {
				$outstr .= '&nbsp;'.$iexplain;
			}
		}
	}

	if( !preg_match( "#^broken\.#", $pFile )) {
		if( !biticon_write_cache( $pParams, $outstr )) {
			echo tra( 'There was a problem writing the icon cache file' );
		}
	}

	return $outstr;
}

/**
 * smarty_function_biticon 
 * 
 * @param array $pParams['ipath'] subdirectory within icon directory
 * @param array $pParams['iname'] name of the icon without extension
 * @param array $pParams['ipackage'] package the icon should be searched for - if it's part of an icon theme, this should be set to 'icons'
 * @param array $gBitSmarty Referenced object
 * @access public
 * @return final <img>
 */
function smarty_function_biticon( $pParams, &$gBitSmarty, $pCheckSmall = FALSE ) {
	global $gBitSystem, $gBitThemes, $gSniffer;

	// this is needed in case everything goes horribly wrong
	$copyParams = $pParams;

	// ensure that ipath has a leading and trailing slash
	if( !empty( $pParams['ipath'] )) {
		$pParams['ipath'] = str_replace( "//", "/", "/".$pParams['ipath']."/" );
	} else {
		$pParams['ipath'] = '/';
	}

	// try to separate iname from ipath if we've been given some sloppy naming
	if( strstr( $pParams['iname'], '/' )) {
		$pParams['iname'] = $pParams['ipath'].$pParams['iname'];
		$boom = explode( '/', $pParams['iname'] );
		$pParams['iname'] = array_pop( $boom );
		$pParams['ipath'] = str_replace( "//", "/", "/".implode( $boom, '/' )."/" );
	}

	// if we don't have an ipath yet, we will set it here
	if( $pParams['ipath'] == '/' ) {
		// iforce is generally only set in menus - we might need a parameter to identify menus more accurately
		if( !empty( $pParams['ilocation'] )) {
			if( $pParams['ilocation'] == 'menu' ) {
				$pParams['ipath'] .= 'small/';
				$pParams['iforce'] = 'icon_text';
			} elseif( $pParams['ilocation'] == 'quicktag' ) {
				$pParams['ipath'] .= 'small/';
				$pParams['iforce'] = 'icon';
				$pParams['iclass'] = 'quicktag icon';
			}
		} else {
			$pParams['ipath'] .= $gBitSystem->getConfig( 'site_icon_size', 'small' ).'/';
		}
	}

	// this only happens when we haven't found the original icon we've been looking for
	if( $pCheckSmall ) {
		$pParams['ipath'] = preg_replace( "!/.*?/$!", "/small/", $pParams['ipath'] );
	}

	// we have one special case: pkg_icons don't have a size variant
	if( strstr( $pParams['iname'], 'pkg_' ) && !strstr( $pParams['ipath'], 'small' )) {
		$pParams['ipath'] = preg_replace( "!/.*?/$!", "/", $pParams['ipath'] );
	}

	// make sure ipackage is set correctly
	if( !empty( $pParams['ipackage'] )) {
		$pParams['ipackage'] = strtolower( $pParams['ipackage'] );
	} else {
		$pParams['ipackage'] = 'icons';
	}

	// if the user is using a text-browser we force text instead of icons
	if( $gSniffer->_browser_info['browser'] == 'lx' || $gSniffer->_browser_info['browser'] == 'li' ) {
		$pParams['iforce'] = 'text';
	}

	// get out of here as quickly as possible if we've already cached the icon information before
	if(( $ret = biticon_read_cache( $pParams )) && !( defined( 'TEMPLATE_DEBUG' ) && TEMPLATE_DEBUG == TRUE )) {
		return $ret;
	}

	// first deal with most common scenario: icon themes
	if( $pParams['ipackage'] == 'icons' ) {
		// get the current icon style
		// istyle is a private parameter!!! - only used on theme manager page for icon preview!!!
		// violators will be poked with soft cushions by the Cardinal himself!!!
		$icon_style = !empty( $pParams['istyle'] ) ? $pParams['istyle'] : $gBitSystem->getConfig( 'site_icon_style', DEFAULT_ICON_STYLE );

		if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."icon_styles/$icon_style".$pParams['ipath'], $pParams['iname'] ))) {
			return biticon_output( $pParams, THEMES_PKG_URL."icon_styles/$icon_style".$pParams['ipath'].$matchFile );
		}

		if( $icon_style != DEFAULT_ICON_STYLE && FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."icon_styles/".DEFAULT_ICON_STYLE.$pParams['ipath'], $pParams['iname'] ))) {
			return biticon_output( $pParams, THEMES_PKG_URL."icon_styles/".DEFAULT_ICON_STYLE.$pParams['ipath'].$matchFile );
		}

		// if that didn't work, we'll try liberty
		$pParams['ipath'] = '/'.$gBitSystem->getConfig( 'site_icon_size', 'small' ).'/';
		$pParams['ipackage'] = 'liberty';
	}

	// since package icons reside in <pkg>/icons/ we don't need the small/ subdir
	if( strstr( "/small/", $pParams['ipath'] )) {
		$pParams['ipath'] = str_replace( "small/", "", $pParams['ipath'] );
		$small = TRUE;
	}

	// first check themes/force
	if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."force/icons/".$pParams['ipackage'].$pParams['ipath'], $pParams['iname'] ))) {
		return biticon_output( $pParams, BIT_ROOT_URL."themes/force/icons/".$pParams['ipackage'].$pParams['ipath'].$matchFile );
	}

	//if we have site styles, look there
	if( FALSE !== ( $matchFile = biticon_first_match( $gBitThemes->getStylePath().'/icons/'.$pParams['ipackage'].$pParams['ipath'], $pParams['iname'] ))) {
		return biticon_output( $pParams, $gBitThemes->getStyleUrl().'/icons/'.$pParams['ipackage'].$pParams['ipath'].$matchFile );
	}

	//Well, then lets look in the package location
	if( FALSE !== ( $matchFile = biticon_first_match( constant( strtoupper( $pParams['ipackage'] ).'_PKG_PATH' )."icons".$pParams['ipath'], $pParams['iname'] ))) {
		return biticon_output( $pParams, constant( strtoupper( $pParams['ipackage'] ).'_PKG_URL' )."icons".$pParams['ipath'].$matchFile );
	}

	// Still didn't find it! Well lets output something (return FALSE if only the url is requested)
	if( isset( $pParams['url'] )) {
		return FALSE;
	} else {
		// if we were looking for the large icon, we'll try the whole kaboodle again, looking for the small icon
		if( empty( $small ) && !$pCheckSmall ) {
			return smarty_function_biticon( $copyParams, $gBitSmarty, TRUE );
		} else {
			return biticon_output( $pParams, "broken.".$pParams['ipackage']."/".$pParams['ipath'].$pParams['iname'] );
		}
	}
}

/**
 * biticon_cache 
 * 
 * @param array $pParams 
 * @access public
 * @return cached icon string on sucess, FALSE on failure
 */
function biticon_read_cache( $pParams ) {
	$ret = FALSE;
	$cacheFile = biticon_get_cache_file( $pParams );
	if( is_readable( $cacheFile )) {
		if( $h = fopen( $cacheFile, 'r' )) {
			$ret = fread( $h, filesize( $cacheFile ));
			fclose( $h );
		}
	}

	return $ret;
}

/**
 * biticon_write_cache 
 * 
 * @param array $pParams 
 * @access public
 * @return TRUE on success, FALSE on failure
 */
function biticon_write_cache( $pParams, $pCacheString ) {
	$ret = FALSE;
	if( $cacheFile = biticon_get_cache_file( $pParams )) {
		if( $h = fopen( $cacheFile, 'w' )) {
			$ret = fwrite( $h, $pCacheString );
			fclose( $h );
		}
	}

	return( $ret != 0 );
}

/**
 * will get the path to the cache files based on the stuff in $pParams
 * 
 * @param array $pParams 
 * @access public
 * @return full path to cachefile
 */
function biticon_get_cache_file( $pParams ) {
	global $gBitThemes, $gBitSystem;

	// create a hash filename based on the parameters given
	$hashstring = '';
	$ihash = array( 'iforce', 'ipath', 'iname', 'iexplain', 'ipackage', 'url', 'istyle', 'id', 'style', 'onclick' );
	foreach( $pParams as $param => $value ) {
		if( in_array( $param, $ihash )) {
			$hashstring .= strtolower( $value );
		}
	}

	// return path to cache file
	return $gBitThemes->getIconCachePath().md5( $hashstring.$gBitSystem->getConfig( 'site_biticon_display_style', 'icon' ));
}
?>
