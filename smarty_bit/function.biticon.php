<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @link http://www.bitweaver.org/wiki/function_biticon function_biticon
 */

/**
* get_first_match
*/
function get_first_match( $dir,$filename ) {
	if( !is_dir( $dir ) ) {
		return FALSE;
	}
	$dh=opendir( $dir );
	$pattern = strtolower( $filename ).'.';
	while( false !== ( $curFile = readdir( $dh ) ) ) {
		if( ( strpos( strtolower( $curFile ),$pattern )===0 ) && is_file( $dir.$curFile ) ) {
			return $curFile;
		}
	}
	return false;
}

/**
* output_icon
*/
function output_icon( $params, $file ) {
	global $gBitSystem, $gSniffer;
	$iexplain = isset( $params["iexplain"] ) ? tra( $params["iexplain"] ) : 'please set iexplain';

	// text browsers don't need to see forced icons - usually part of menus or javascript stuff
	if( !empty( $params['iforce'] ) && $params['iforce'] == 'icon' && ( $gSniffer->_browser_info['browser'] == 'lx' || $gSniffer->_browser_info['browser'] == 'li' ) ) {
		return '';
	}

	if( isset( $params["url"] ) ) {
		$outstr = $file;
	} else {
		if( $gBitSystem->getPreference( 'biticon_display' ) == 'text' && $params['iforce'] != 'icon' ) {
			$outstr = $iexplain;
		} else {
			$outstr='<img src="'.$file.'"';
			if( isset( $params["iexplain"] ) ) {
				$outstr .= ' alt="'.tra( $params["iexplain"] ).'" title="'.tra( $params["iexplain"] ).'"';
			} else {
				$outstr .= ' alt=""';
			}

			$ommit = array( 'ipackage', 'ipath', 'iname', 'iexplain', 'iforce' );
			foreach( $params as $name => $val ) {
				if( !in_array( $name, $ommit ) ) {
					$outstr .= ' '.$name.'="'.$val.'"';
				}
			}

			if( !isset( $params["class"] ) ) {
				$outstr .= ' class="icon"';
			}

			// insert image width and height
			list( $width, $height, $type, $attr ) = @getimagesize( BIT_ROOT_PATH.$file );
			if( !empty( $width ) && !empty( $height ) ) {
				$outstr .= ' width="'.$width.'" height="'.$height.'"';
			}

			$outstr .= " />";
		}

		if( $gBitSystem->getPreference( 'biticon_display' ) == 'icon_text' && $params['iforce'] != 'icon' || $params['iforce'] == 'icon_text' ) {
			$outstr .= '&nbsp;'.$iexplain;
		}
	}
	return $outstr;
}

/**
* smarty_function_biticon
*/
function smarty_function_biticon($params, &$gBitSmarty) {
	global $gBitSystem, $icon_style;

	if( !isset( $params['ipath'] ) ) {
		$params['ipath'] = '';
	}

	if( 0 ) {
		print_r($params);
		print("<br />");
		print_r($gBitSystem->getPreference('style'));
		print("<br />");
		print_r($icon_style);
		print("<br />");
	}

	if( isset( $params['ipackage'] ) ) {
		$params['ipackage'] = strtolower( $params['ipackage'] );
	}
	//if we have icon styles, first look there
	if( isset( $icon_style ) ) {
		if( false !== ( $matchFile = get_first_match( THEMES_PKG_PATH."styles/$icon_style/icons/".$params['ipackage']."/".$params['ipath'],$params['iname'] ) ) ) {
			return output_icon( $params, THEMES_PKG_URL."styles/$icon_style/icons/".$params['ipackage']."/".$params['ipath'].$matchFile );
		}
	}

	// first check themes/force
	if( false !== ( $matchFile = get_first_match( THEMES_PKG_PATH."force/icons/".$params['ipackage'].'/'.$params['ipath'],$params['iname'] ) ) ) {
		$ret = output_icon( $params, BIT_ROOT_URL."themes/force/icons/".$params['ipackage'].'/'.$params['ipath'].$matchFile );
		return $ret;
	}

	//if we have site styles, look there
	if( false !== ( $matchFile = get_first_match( $gBitSystem->getStylePath().'/icons/'.$params['ipackage']."/".$params['ipath'],$params['iname'] ) ) ) {
		return output_icon( $params, $gBitSystem->getStyleUrl().'/icons/'.$params['ipackage']."/".$params['ipath'].$matchFile );
	}

	//Well, then lets look in the package location
	if( false !== ( $matchFile = get_first_match( $gBitSystem->mPackages[$params['ipackage']]['path']."icons/".$params['ipath'],$params['iname'] ) ) ) {
		return output_icon( $params, $gBitSystem->mPackages[$params['ipackage']]['url']."icons/".$params['ipath'].$matchFile );
	}

	//Well, then lets look in the default location
	if( false !== ( $matchFile = get_first_match( THEMES_PKG_PATH."styles/default/icons/".$params['ipackage']."/".$params['ipath'],$params['iname'] ) ) ) {
		return output_icon( $params, THEMES_PKG_URL."styles/default/icons/".$params['ipackage']."/".$params['ipath'].$matchFile );
	}

	//Still didn't find it! Well lets output something (return false if only the url is requested)
	if( isset( $params['url'] ) ) {
		return false;
	} else {
		return output_icon($params, "broken.".$params['ipackage']."/".$params['ipath'].$params['iname']);
	}
}
?>
