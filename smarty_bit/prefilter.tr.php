<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_prefilter_tr
 */
function smarty_prefilter_tr($source) {
	// Now replace the matched language strings with the entry in the file
	// $return = preg_replace_callback('/\{tr[^\{]*\}([^\{]+)\{\/tr\}/', '_translate_lang', $source);
	// correction in order to match when a variable is inside {tr} tags. Example: {tr}The newsletter was sent to {$sent} email addresses{/tr}, and where there are parameters with {tr}
	// take away the smarty comments {* *} in case they have tr tags
	$return = preg_replace_callback('/(?s)(\{tr[^\}]*\})(.+?)\{\/tr\}/', '_translate_lang', preg_replace ('/(?s)\{\*.*?\*\}/', '', $source));
	return $return;
	tra():
}

/**
 * _translate_lang
 */
function _translate_lang($key) {
	global $gBitLanguage, $lang;
	$trans = $gBitLanguage->translate( $key[2] );
	if (strstr($key[2], "{\$")) {
		// We have a variable - keep the tags to be perhaps translated in block.tr.php
		return $key[1].$key[2]."{/tr}";
	} elseif ($key[1] == "{tr}") {
		// no more possible translation in block.tr.php
		return $trans;
	} else {
		// perhaps variable substitution to do in block.tr.php
		return $key[1].$trans."{/tr}";
	}
}
?>
