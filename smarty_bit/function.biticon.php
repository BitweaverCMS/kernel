<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
* get_first_match
*/
function get_first_match($dir,$filename)
{
  if(!is_dir($dir)) {
     //intf("Could not find dir: %s<br />",$dir);
     return false;
  }
  $dh=opendir($dir);
  $pattern = strtolower($filename).'.';
  while(false !== ($curFile = readdir($dh)))
  {
  	//print(' ');
  	//print_r($pattern);
  	//print(' ');
  	//print_r(strtolower($curFile));
  	//print(' ');
  	//print_r(strpos(strtolower($curFile),$pattern));
  	//print('<>');
  	if((strpos(strtolower($curFile),$pattern)===0) && is_file($dir.$curFile)) {
  		return $curFile;
  	}
  	//else
  	//{
  	//	printf(" %s != %s @ %s<br />",$curFile, $pattern, $dir.$curFile);
  	//}
  }
  return false;
}

/**
* output_icon
*/
function output_icon($params, $file) {
	global $gBitSystem;

	if( isset( $params["url"] ) ) {
		$outstr = $file;
	} else {
		if( $gBitSystem->getPreference( 'biticon_display' ) == 'text' && $params['iforce'] != 'icon' ) {
			$outstr = isset( $params["iexplain"] ) ? tra($params["iexplain"]) : 'please set iexplain';
		} else {
			$outstr="<img src=\"".$file."\"";
			if(isset($params["iexplain"])) {
				$outstr=$outstr." alt=\"".tra($params["iexplain"])."\" title=\"".tra($params["iexplain"])."\"";
			} else {
				$outstr=$outstr." alt=\"\"";
			}

			foreach ($params as $name => $val) {
				if($name != "ipackage" && $name != "ipath" && $name != "iname" && $name != "iexplain" && $name != "iforce") {
					$outstr = $outstr." ".$name."=\"".$val."\"";
				}
			}

			if(!isset($params["class"])) {
				$outstr=$outstr." class=\"icon\"";
			}

			$outstr = $outstr." />";
		}
		if( $gBitSystem->getPreference( 'biticon_display' ) == 'icon_text' && $params['iforce'] != 'icon' ) {
			$outstr .= '&nbsp;'.isset( $params["iexplain"] ) ? tra($params["iexplain"]) : 'please set iexplain';
		}
	}
	return $outstr;
}

/**
* smarty_function_biticon
*/
function smarty_function_biticon($params, &$gBitSmarty) {
	global $gBitSystem, $icon_style;

	if (!isset($params['ipath']))
		$params['ipath'] = '';

	if(0) {
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
	if (isset($icon_style)) {
		if (false !== ($matchFile = get_first_match(BIT_THEME_PATH."styles/$icon_style/icons/".$params['ipackage']."/".$params['ipath'],$params['iname']))) {
			return output_icon($params, BIT_THEME_URL."styles/$icon_style/icons/".$params['ipackage']."/".$params['ipath'].$matchFile);
		}
	}

	// first check themes/force
	if (false !== ($matchFile = get_first_match(BIT_THEME_PATH."force/icons/".$params['ipackage'].'/'.$params['ipath'],$params['iname']))) {
		$ret = output_icon($params, BIT_ROOT_URL."themes/force/icons/".$params['ipackage'].'/'.$params['ipath'].$matchFile);
		return $ret;
	}

	//if we have site styles, look there
	if (false !== ($matchFile = get_first_match( $gBitSystem->getStylePath().'/icons/'.$params['ipackage']."/".$params['ipath'],$params['iname']))) {
		return output_icon($params, $gBitSystem->getStyleUrl().'/icons/'.$params['ipackage']."/".$params['ipath'].$matchFile);
	}

	//Well, then lets look in the package location
	if (false !== ($matchFile = get_first_match($gBitSystem->mPackages[$params['ipackage']]['path']."icons/".$params['ipath'],$params['iname']))) {
		return output_icon($params, $gBitSystem->mPackages[$params['ipackage']]['url']."icons/".$params['ipath'].$matchFile);
	}

	//Well, then lets look in the default location
	if (false !== ($matchFile = get_first_match(BIT_THEME_PATH."styles/default/icons/".$params['ipackage']."/".$params['ipath'],$params['iname']))) {
		return output_icon($params, BIT_THEME_URL."styles/default/icons/".$params['ipackage']."/".$params['ipath'].$matchFile);
	}

	//Still didn't find it! Well lets output something (return false if only the url is requested)
	if( isset( $params['url'] ) ) {
		return false;
	} else {
		return output_icon($params, "broken.".$params['ipackage']."/".$params['ipath'].$params['iname']);
	}
}
?>
