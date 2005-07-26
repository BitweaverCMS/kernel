<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/** \file
 * $Header: /cvsroot/bitweaver/_bit_kernel/smarty_bit/modifier.dbg.php,v 1.1.1.1.2.3 2005/07/26 15:50:10 drewslater Exp $
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
