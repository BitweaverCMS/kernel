<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @link http://www.bitweaver.org/wiki/function_biticon function_biticon
 */

/**
 * get_first_match 
 * 
 * @param string $pDir Directory in which we want to search for the icon
 * @param array $pFilename Icon name without the extension
 * @access public
 * @return Icon name with extension on success, FALSE on failure
 */
function get_first_match( $pDir, $pFilename ) {
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
function output_icon( $pParams, $pFile ) {
	global $gBitSystem, $gSniffer;
	$iexplain = isset( $pParams["iexplain"] ) ? tra( $pParams["iexplain"] ) : 'please set iexplain';

	// text browsers don't need to see forced icons - usually part of menus or javascript stuff
	if( !empty( $pParams['iforce'] ) && $pParams['iforce'] == 'icon' && ( $gSniffer->_browser_info['browser'] == 'lx' || $gSniffer->_browser_info['browser'] == 'li' ) ) {
		return '';
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

		if( FALSE !== ( $matchFile = get_first_match( THEMES_PKG_PATH."icon_styles/$icon_style/".$pParams['ipath']."/", $pParams['iname'] ) ) ) {
			return output_icon( $pParams, THEMES_PKG_URL."icon_styles/$icon_style/".$pParams['ipath']."/".$matchFile );
		}

		if( FALSE !== ( $matchFile = get_first_match( THEMES_PKG_PATH."icon_styles/".DEFAULT_ICON_STYLE."/".$pParams['ipath']."/", $pParams['iname'] ) ) ) {
			return output_icon( $pParams, THEMES_PKG_URL."icon_styles/".DEFAULT_ICON_STYLE."/".$pParams['ipath']."/".$matchFile );
		}

		// if that didn't work, we'll try liberty
		$pParams['ipath'] = '';
		$pParams['ipackage'] = 'liberty';
	}

	// first check themes/force
	if( FALSE !== ( $matchFile = get_first_match( THEMES_PKG_PATH."force/icons/".$pParams['ipackage'].'/'.$pParams['ipath'],$pParams['iname'] ) ) ) {
		return output_icon( $pParams, BIT_ROOT_URL."themes/force/icons/".$pParams['ipackage'].'/'.$pParams['ipath'].$matchFile );
	}

	//if we have site styles, look there
	if( FALSE !== ( $matchFile = get_first_match( $gBitSystem->getStylePath().'/icons/'.$pParams['ipackage']."/".$pParams['ipath'],$pParams['iname'] ) ) ) {
		return output_icon( $pParams, $gBitSystem->getStyleUrl().'/icons/'.$pParams['ipackage']."/".$pParams['ipath'].$matchFile );
	}

	//Well, then lets look in the package location
	if( FALSE !== ( $matchFile = get_first_match( $gBitSystem->mPackages[$pParams['ipackage']]['path']."icons/".$pParams['ipath'],$pParams['iname'] ) ) ) {
		return output_icon( $pParams, $gBitSystem->mPackages[$pParams['ipackage']]['url']."icons/".$pParams['ipath'].$matchFile );
	}

	//Well, then lets look in the default location
	if( FALSE !== ( $matchFile = get_first_match( THEMES_PKG_PATH."styles/default/icons/".$pParams['ipackage']."/".$pParams['ipath'],$pParams['iname'] ) ) ) {
		return output_icon( $pParams, THEMES_PKG_URL."styles/default/icons/".$pParams['ipackage']."/".$pParams['ipath'].$matchFile );
	}

	//Still didn't find it! Well lets output something (return FALSE if only the url is requested)
	if( isset( $pParams['url'] ) ) {
		return FALSE;
	} else {
		return output_icon($pParams, "broken.".$pParams['ipackage']."/".$pParams['ipath'].$pParams['iname']);
	}
}
?>
