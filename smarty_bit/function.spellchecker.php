<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {spellchecker} function plugin
 *
 * Type:	function
 * Name:	spellchecker
 */
function smarty_function_spellchecker( $params, &$gBitSmarty ) {
	global $gBitSystem;
	if( $gBitSystem->isPackageActive( 'bnspell' ) ) {
		echo 'title="spellcheck_icons" accesskey="'.BNSPELL_PKG_URL.'spell_checker.php" style="height:'.( !empty( $_COOKIE['rows'] ) ? $_COOKIE['rows'] : '20' ).'em"';
	}
}
?>
