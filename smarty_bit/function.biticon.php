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
	if( is_dir( $pDir ) ) {
		global $gSniffer;

		// if this is MSIE, we try png last.
		if( $gSniffer->_browser_info['browser'] == 'ie' ) {
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
	global $gBitSystem;

	if( !isset( $pParams['ipath'] ) ) {
		$pParams['ipath'] = '';
	}

	if( isset( $pParams['ipackage'] ) ) {
		$pParams['ipackage'] = strtolower( $pParams['ipackage'] );
	}

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
			$pParams['ipath'] = 'small';
		}

		if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."icon_styles/$icon_style/".$pParams['ipath']."/", $pParams['iname'] ) ) ) {
			return biticon_output( $pParams, THEMES_PKG_URL."icon_styles/$icon_style/".$pParams['ipath']."/".$matchFile );
		}

		if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."icon_styles/".DEFAULT_ICON_STYLE."/".$pParams['ipath']."/", $pParams['iname'] ) ) ) {
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
	if( FALSE !== ( $matchFile = biticon_first_match( $gBitSystem->getStylePath().'/icons/'.$pParams['ipackage']."/".$pParams['ipath'],$pParams['iname'] ) ) ) {
		return biticon_output( $pParams, $gBitSystem->getStyleUrl().'/icons/'.$pParams['ipackage']."/".$pParams['ipath'].$matchFile );
	}

	//Well, then lets look in the package location
	if( FALSE !== ( $matchFile = biticon_first_match( $gBitSystem->mPackages[$pParams['ipackage']]['path']."icons/".$pParams['ipath'],$pParams['iname'] ) ) ) {
		return biticon_output( $pParams, constant( strtoupper( $pParams['ipackage'] ).'_PKG_URL' )."icons/".$pParams['ipath'].$matchFile );
	}

	//Well, then lets look in the default location
	if( FALSE !== ( $matchFile = biticon_first_match( THEMES_PKG_PATH."styles/default/icons/".$pParams['ipackage']."/".$pParams['ipath'],$pParams['iname'] ) ) ) {
		return biticon_output( $pParams, THEMES_PKG_URL."styles/default/icons/".$pParams['ipackage']."/".$pParams['ipath'].$matchFile );
	}

	//Still didn't find it! Well lets output something (return FALSE if only the url is requested)
	if( isset( $pParams['url'] ) ) {
		return FALSE;
	} else {
		return biticon_output($pParams, "broken.".$pParams['ipackage']."/".$pParams['ipath'].$pParams['iname']);
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
	global $gSniffer;
	$tempdir = TEMP_PKG_PATH.'themes/biticon/';
	if( !is_dir( $tempdir )) {
		mkdir_p( $tempdir );
	}

	if( !empty( $pParams['ipackage'] ) && $pParams['ipackage'] == 'icons' ) {
		if( !strstr( $pParams['iname'], '/' ) ) {
			$pParams['ipath'] = 'small';
		}
	}

	// create a hash filename based on the parameters given
	$hashstring = '';
	$ihash = array( 'ipath', 'iname', 'iexplain', 'ipackage', 'url' );
	foreach( $pParams as $param => $value ) {
		if( in_array( $param, $ihash )) {
			$hashstring .= strtolower( $value );
		}
	}

	// finally we append browser since we have browser-specific stuff in biticon
	// we also append bitversion to invalidate cache in case somethang has changed since the last release
	return $tempdir.md5( $hashstring ).'_'.BIT_MAJOR_VERSION.BIT_MINOR_VERSION.BIT_SUB_VERSION."_".$gSniffer->_browser_info['browser'];
}
?>
