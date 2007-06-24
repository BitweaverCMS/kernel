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
function biticon_first_match( $pDir, $pFilename, $size = true ) {
	if( is_dir( $pDir ) ) {
		global $gSniffer, $gBitSystem;

		// if this is MSIE < 7, we try png last.
		if( !$gBitSystem->isFeatureActive( 'themes_use_msie_png_hack' ) && $gSniffer->_browser_info['browser'] == 'ie' && $gSniffer->_browser_info['maj_ver'] < 7 ) {
			$extensions = array( 'gif', 'jpg', 'png' );
		} else {
			$extensions = array( 'png', 'gif', 'jpg' );
		}
		
		// Add other icon sizes but extending the icon name with the size
		// Default is small icons
		$icon_size = $gBitSystem->getConfig( 'site_icon_size', 'small' );
		if ( $size && $icon_size != 'small' ) {
			$pFilename .= '_'.$icon_size;
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
	global $gBitSystem, $gSniffer;
	$iexplain = isset( $pParams["iexplain"] ) ? tra( $pParams["iexplain"] ) : 'please set iexplain';

	// text browsers don't need to see forced icons - usually part of menus or javascript stuff
	if( !empty( $pParams['iforce'] ) && $pParams['iforce'] == 'icon' && ( $gSniffer->_browser_info['browser'] == 'lx' || $gSniffer->_browser_info['browser'] == 'li' ) ) {
		return '';
	} elseif( empty( $pParams['iforce'] ) ) {
		$pParams['iforce'] = NULL;
	}

	if( isset( $pParams["url"] ) ) {
		$outstr = $pFile;
	} else {
		if( $gBitSystem->getConfig( 'site_biticon_display_style' ) == 'text' && $pParams['iforce'] != 'icon' ) {
			$outstr = $iexplain;
		} else {
			$outstr='<img src="'.$pFile.'"';
			if( isset( $pParams["iexplain"] ) ) {
				$outstr .= ' alt="'.tra( $pParams["iexplain"] ).'" title="'.tra( $pParams["iexplain"] ).'"';
			} else {
				$outstr .= ' alt=""';
			}

			$ommit = array( 'ipackage', 'ipath', 'iname', 'iexplain', 'iforce', 'istyle', 'iclass' );
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

			// insert image width and height
			list( $width, $height, $type, $attr ) = @getimagesize( BIT_ROOT_PATH.$pFile );
			if( !empty( $width ) && !empty( $height ) ) {
				$outstr .= ' width="'.$width.'" height="'.$height.'"';
			}

			$outstr .= " />";
		}

		if( $gBitSystem->getConfig( 'site_biticon_display_style' ) == 'icon_text' && $pParams['iforce'] != 'icon' || $pParams['iforce'] == 'icon_text' ) {
			$outstr .= '&nbsp;'.$iexplain;
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
function smarty_function_biticon( $pParams, &$gBitSmarty ) {
	global $gBitSystem, $gBitThemes;

	if( !isset( $pParams['ipath'] ) ) {
		$pParams['ipath'] = '';
	}

	$size = true;
	// use '_medium' or '_large' for extra scaled icons
	if( isset( $pParams['ipackage'] ) ) {
		$pParams['ipackage'] = strtolower( $pParams['ipackage'] );
		// drop extension for bitweaver liberty icons
		if ( ($pParams['ipackage'] == 'liberty' && $pParams['ipath'] != '') || $pParams['ipackage'] == 'smileys' || $pParams['ipackage'] == 'quicktags' || preg_match( '/pkg_/i', $pParams['iname'] ) ) $size = false;
	} else {
		$pParams['ipackage'] = 'icons';
	}

	// Make sure we have an icon to get
	if( isset( $pParams['iname'] ) ){
		// get out of here as quickly as possible if we've already cached the icon information before
		if(( $ret = biticon_get_cached( $pParams )) && !( defined( 'TEMPLATE_DEBUG') && TEMPLATE_DEBUG == TRUE )) {
			return $ret;
		}

		// Icon styles are treated differently
		// we need to think about how we want to override these icon themes
		if( $pParams['ipackage'] == 'icons' ) {
			// get the current icon style
			// istyle is a private parameter!!! - only used on theme manager page for icon preview!!!
			// violators will be poked with soft cushions by the Cardinal himself!!!
			$icon_style = !empty( $pParams['istyle'] ) ? $pParams['istyle'] : $gBitSystem->getConfig( 'site_icon_style', DEFAULT_ICON_STYLE );

			if( !empty( $pParams['ipath'] ) ) {
			} elseif( !strstr( $pParams['iname'], '/' ) ) {
				$pParams['ipath'] = $gBitSystem->getConfig( 'site_icon_size', 'small' );
			}

			if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."icon_styles/$icon_style/".$pParams['ipath']."/", $pParams['iname'], false ) ) ) {
				return biticon_output( $pParams, THEMES_PKG_URL."icon_styles/$icon_style/".$pParams['ipath']."/".$matchFile );
			}

			if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."icon_styles/".DEFAULT_ICON_STYLE."/".$pParams['ipath']."/", $pParams['iname'], false ) ) ) {
				return biticon_output( $pParams, THEMES_PKG_URL."icon_styles/".DEFAULT_ICON_STYLE."/".$pParams['ipath']."/".$matchFile );
			}

			// if that didn't work, we'll try liberty
			$pParams['ipath'] = '';
			$pParams['ipackage'] = 'liberty';
		}

		// first check themes/force
		if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."force/icons/".$pParams['ipackage'].'/'.$pParams['ipath'],$pParams['iname'] ) ) ) {
			return biticon_output( $pParams, BIT_ROOT_URL."themes/force/icons/".$pParams['ipackage'].'/'.$pParams['ipath'].$matchFile );
		}

		//if we have site styles, look there
		if( FALSE !== ( $matchFile = biticon_first_match( $gBitThemes->getStylePath().'/icons/'.$pParams['ipackage']."/".$pParams['ipath'],$pParams['iname'] ) ) ) {
			return biticon_output( $pParams, $gBitThemes->getStyleUrl().'/icons/'.$pParams['ipackage']."/".$pParams['ipath'].$matchFile );
		}

		//Well, then lets look in the package location
		if( FALSE !== ( $matchFile = biticon_first_match( $gBitSystem->mPackages[$pParams['ipackage']]['path']."icons/".$pParams['ipath'],$pParams['iname'], $size ) ) ) {
			return biticon_output( $pParams, constant( strtoupper( $pParams['ipackage'] ).'_PKG_URL' )."icons/".$pParams['ipath'].$matchFile );
		}

		//Well, then lets look in the default location
		if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."styles/default/icons/".$pParams['ipackage']."/".$pParams['ipath'],$pParams['iname'] ) ) ) {
			return biticon_output( $pParams, THEMES_PKG_URL."styles/default/icons/".$pParams['ipackage']."/".$pParams['ipath'].$matchFile );
		}
	}

	//Still didn't find it! Well lets output something (return FALSE if only the url is requested)
	if( isset( $pParams['url'] ) ) {
		return FALSE;
	} else {
		return biticon_output($pParams, "broken.".$pParams['ipackage']."/".$pParams['ipath'].
							  (empty($pParams['iname']) ? 'missing-iname' : $pParams['iname']));
	}
}

/**
 * biticon_cache
 *
 * @param array $pParams
 * @access public
 * @return cached icon string on sucess, FALSE on failure
 */
function biticon_get_cached( $pParams ) {
	$ret = FALSE;
//	$cacheFile = biticon_get_cache_file( $pParams );
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
	global $gSniffer, $gBitThemes, $gBitSystem, $gBitLanguage;
	$cachedir = $gBitThemes->getIconCachePath();

	if( !empty( $pParams['ipackage'] ) && $pParams['ipackage'] == 'icons' ) {
		if( !strstr( $pParams['iname'], '/' ) ) {
			$pParams['ipath'] = $gBitSystem->getConfig( 'site_icon_size', 'small' );
		}
	}

	// create a hash filename based on the parameters given
	$hashstring = '';
	$ihash = array( 'iforce', 'ipath', 'iname', 'iexplain', 'ipackage', 'url', 'istyle' );
	foreach( $pParams as $param => $value ) {
		if( in_array( $param, $ihash )) {
			$hashstring .= strtolower( $value );
		}
	}

	$hashstring .= $gBitSystem->getConfig( 'site_biticon_display_style' );
	// needed to correctly cache icon size changes for non-theme set icons
	$hashstring .= $gBitSystem->getConfig( 'site_icon_size', 'small' );

	// finally we append browser with its major version since we have browser-specific stuff in biticon
	// we also append bitversion to invalidate cache in case something has changed since the last release
	return $cachedir.md5( $hashstring ).'_'.$gBitLanguage->getLanguage()."_".BIT_MAJOR_VERSION.BIT_MINOR_VERSION.BIT_SUB_VERSION."_".$gSniffer->_browser_info['browser'].$gSniffer->_browser_info['maj_ver'];
}
?>
