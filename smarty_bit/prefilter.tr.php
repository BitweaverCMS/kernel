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
//  $return = preg_replace_callback('/\{tr[^\{]*\}([^\{]+)\{\/tr\}/', '_translate_lang', $source);
// correction in order to match when a variable is inside {tr} tags. Example: {tr}The newsletter was sent to {$sent} email addresses{/tr}, and where there are parameters with {tr}
// take away the smarty comments {* *} in case they have tr tags
  $return = preg_replace_callback('/(?s)(\{tr[^\}]*\})(.+?)\{\/tr\}/', '_translate_lang', preg_replace ('/(?s)\{\*.*?\*\}/', '', $source));
  return $return;
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
			return $trans;// no more possible translation in block.tr.php
		} else {
			return $key[1].$trans."{/tr}";// perhaps variable substitution to do in block.tr.php
		}
}

/*
function _translate_lang($key) {
	global $gBitLanguage, $gBitSystem;
	if( $gBitSystem->isFeatureActive( 'lang_use_db' ) ) {
		global $gBitSystem;
		$content = $key[2];
		$tran = $gBitLanguage->translate( $content );
		if ($key[1] == "{tr}") {
			return $tran;// no more possible translation in block.tr.php
		} else {
			return $key[1].$tran."{/tr}";// perhaps variable substituion to do in block.tr.php
		}
	} else {
		global $lang;
		include_once(LANGUAGES_PKG_PATH."lang/$gBitLanguage->mLanguage/language.php");
		if(isset($lang[$key[2]])) {
			if ($key[1] == "{tr}") {
				return $lang[$key[2]];// no more possible translation in block.tr.php
			} else {
				return $key[1].$lang[$key[2]]."{/tr}";// perhaps variable substitution to do in block.tr.php
			}
		}// not found in language.php
		elseif (strstr($key[2], "{\$")) {
			return $key[1].$key[2]."{/tr}";// keep the tags to be perhaps translated in block.tr.php
		}
		else {
			return $key[2];
		}
	}
}*/
?>
