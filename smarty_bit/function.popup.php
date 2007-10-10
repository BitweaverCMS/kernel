<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {popup} function plugin
 *
 * Type:	 function<br>
 * Name:	 popup<br>
 * Purpose:  make text pop up in windows via overlib
 * @link http://smarty.php.net/manual/en/language.function.popup.php {popup}
 *		  (Smarty online manual)
 * @param array
 * @param Smarty
 * @return string
 */
require_once(LIBERTY_PKG_PATH."LibertyContent.php");

function smarty_function_popup($params, &$gBitSmarty) {
	$append = '';
	foreach ($params as $_key=>$_value) {
		switch ($_key) {
			case 'target':
			case 'text':
			case 'trigger':
			case 'content_id':
				$$_key = (string)$_value;
				break;

			case 'caption':
			case 'closetext':
			case 'status':
				$append .= ',' . strtoupper($_key) . ",'" . str_replace("'","\'",$_value) . "'";
				break;

			case 'fgcolor':
			case 'bgcolor':
			case 'textcolor':
			case 'capcolor':
			case 'closecolor':
			case 'textfont':
			case 'captionfont':
			case 'closefont':
			case 'textsize':
			case 'captionsize':
			case 'closesize':
			case 'width':
			case 'height':
			case 'border':
			case 'offsetx':
			case 'offsety':
			case 'fgbackground':
			case 'bgbackground':
			case 'inarray':
			case 'caparray':
			case 'capicon':
			case 'snapx':
			case 'snapy':
			case 'fixx':
			case 'fixy':
			case 'background':
			case 'padx':
			case 'pady':
			case 'frame':
			case 'timeout':
			case 'delay':
			case 'function':
				$append .= ',' . strtoupper($_key) . ",'$_value'";
				break;

			case 'sticky':
			case 'left':
			case 'right':
			case 'center':
			case 'above':
			case 'below':
			case 'noclose':
			case 'autostatus':
			case 'autostatuscap':
			case 'fullhtml':
			case 'hauto':
			case 'vauto':
			case 'mouseoff':
				if ($_value) $append .= ',' . strtoupper($_key);
				break;
			case 'closeclick':
				if ($_value) $append .= ',' . strtoupper($_key);
				if ($_value) $target_append = $_key.'=1';
				break;

			default:
				$gBitSmarty->trigger_error("[popup] unknown parameter $_key", E_USER_WARNING);
		}
	}

	if (empty($target) && empty($text) && empty($content_id) && !isset($inarray) && empty($function)) {
		$gBitSmarty->trigger_error("overlib: attribute 'target' or 'text' or 'inarray' or 'function' required");
		return false;
	}

	if ((!empty($target) && !empty($text)) || (!empty($target) && !empty($content_id)) || (!empty($text) && !empty($content_id)) ) {
		$gBitSmarty->trigger_error("overlib: You may only specify one of 'target', 'text' and 'content_id'..");
	}

	if (empty($trigger)) { $trigger = "onmouseover"; }

	if (!empty($content_id)) {
		$target = LibertyContent::getPreviewUrl($content_id);
	}

	if (!empty($target) && !empty($target_append)) {
	  if (strstr($target, "?")) {
	    $target .= "&".$target_append;
	  }
	  else {
	    $target .= "?".$target_append;
	  }
	}

	if (!empty($text)) {
		$retval = $trigger . '="return overlib(\''.preg_replace(array("!'!","![\r\n]!"),array("\'",'\r'),$text).'\'';
		$retval .= $append . ');" onmouseout="nd();"';
	}
	else {
		$retval = $trigger . '="of=function(t){overlib(t'.$append.');};BitAjax.getAndCall(this,of,\''.$target.'\');" onmouseout="nd();"';
	}

	return $retval;
}
?>
