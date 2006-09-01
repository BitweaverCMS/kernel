<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and 
 *           appending the $etc string.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param string
 * @return string
 */
function smarty_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false, $divId='') {
	if ($length == 0)
		return '';

	if (strlen($string) > $length) {
		$length -= strlen($etc);
		if ($break_words) // break the word
			$fragment = mb_substr($string, 0, $length);
		else {
			$fragment = mb_substr($string, 0, $length + 1);
			$fragment = preg_replace('/\s+(\S+)?$/', '', $fragment);
		}
		if (!empty($divId)) {
			$etc = "<span  style='display:inline;' id='dyn_".$divId."_display'><a class='truncate' onclick='javascript:toggle_dynamic_var(\"$divId\");' title='".tra('Click to see the full text')."'>$etc</a></span>";
			$etc .= "<span style='display:none;' id='dyn_".$divId."_edit'>".substr($string, strlen($fragment))."</span>";
		}
		return $fragment.$etc;
	} else
		return $string;
}

/* vim: set expandtab: */

?>
