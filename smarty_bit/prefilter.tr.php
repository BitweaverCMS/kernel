<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_prefilter_tr 
 * 
 * @param array $source 
 * @access public
 * @return full source with partially treated {tr} sections
 */
function smarty_prefilter_tr( $source ) {
	// Now replace the matched language strings with the entry in the file
	// $return = preg_replace_callback( '#\{tr[^\{]*\}([^\{]+)\{/tr\}#', '_translate_lang', $source );
	//
	// correction: in order to match when a variable is inside {tr} tags.
	// Example: {tr}The newsletter was sent to {$sent} email addresses{/tr},
	// and where there are parameters with {tr} take away the smarty comments 
	// {* *} in case they have tr tags
	return( preg_replace_callback( '#(?s)(\{tr[^\}]*\})(.+?)\{/tr\}#', '_translate_lang', preg_replace ( '#(?s)\{\*.*?\*\}#', '', $source ) ) );
}

/**
 * _translate_lang 
 * 
 * @param array $pKey Hash passed in by smarty_prefilter_tr() above
 * @param array $pKey[0] starting {tr} tag
 * @param array $pKey[1] string that needs to be translated
 * @param array $pKey[2] flosing {/tr} tag
 * @access protected
 * @return translated string
 */
function _translate_lang( $pKey ) {
	global $gBitLanguage, $lang;
	$trans = $gBitLanguage->translate( $pKey[2] );

	// this is the original codeblock:
//	if (strstr($pKey[2], "{\$")) {
//		// We have a variable - keep the tags to be perhaps translated in block.tr.php
//		return $pKey[1].$pKey[2]."{/tr}";
//	} elseif ($pKey[1] == "{tr}") {
//		// no more possible translation in block.tr.php
//		return $trans;
//	} else {
//		// perhaps variable substitution to do in block.tr.php
//		return $pKey[1].$trans."{/tr}";
//	}
	// Explanation why this doesn't work:
	//  - case 1
	//    returning stuff like {tr}waiting for {$number} ratings{/tr}
	//    will get translated by block.tr.php but only after the {$number} has 
	//    been interpreted and we will end up with master strings like:
	//    waiting for 4 ratings
	//    waiting for 5 ratings
	//    waiting for 6 ratings
	//
	//    if we were to return:
	//    return $pKey[1].$trans."{/tr}";
	//    we would end up with a worse situation where translated strings would 
	//    be added to the master string database
	//
	//  - case 2
	//    this should be fine since it has been translated and the {tr} blocks 
	//    have been removed i.e. block.tr.php won't be called anymore
	//
	//  - case 3
	//    this will leave everything as is. this will work as well, but only if 
	//    there is no {$var} in the string - see case 1
	// --- xing


	// the following has case 1 removed from the above situation. not sure if 
	// this caters for all situations, but seems to work for now
	if( $pKey[1] == "{tr}" ) {
		// no parameters set for block.tr.php
		return $trans;
	} else {
		// perhaps there are parameters set for block.tr.php
		return $pKey[1].$pKey[2]."{/tr}";
	}
}
?>
