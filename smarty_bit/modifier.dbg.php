<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/** \file
 * $Header$
 *
 * @author zaufi <zaufi@sendmail.ru>
 */

/**
 * \brief Smarty modifier plugin to add string to debug console log w/o modify output
 * Usage format {$gBitSmarty_var|dbg}
 */
function smarty_modifier_dbg($string, $label = '')
{
	global $debugger, $gBitSystem;
	if( $gBitSystem->isPackageActive( 'debug' ) ) {
		require_once( DEBUG_PKG_PATH.'debugger.php' );
		//
		$debugger->msg('Smarty log'.((strlen($label) > 0) ? ': '.$label : '').': '.$string);
		return $string;
	}
}

?>
