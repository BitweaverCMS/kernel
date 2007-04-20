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
	$rows = !empty($params['rows']) ? $params['rows'] : '20';

	if( $gBitSystem->isPackageActive( 'bnspell' ) ) {
		echo 'title="spellcheck_icons" accesskey="'.BNSPELL_PKG_URL.'spell_checker.php"';
	}
}
?>
