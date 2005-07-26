<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
// $Header: /cvsroot/bitweaver/_bit_kernel/smarty_bit/block.bitmodule.php,v 1.1.1.1.2.2 2005/07/26 15:50:09 drewslater Exp $
/**
 * \brief Smarty {bitmodule}{/bitmodule} block handler
 *
 * To make a module it is enough to place smth like following
 * into corresponding mod-name.tpl file:
 * \code
 *  {bitmodule name="module_name" title="Module title"}
 *    <!-- module Smarty/HTML code here -->
 *  {/bitmodule}
 * \endcode
 *
 * This block may (can) use 2 Smarty templates:
 *  1) module.tpl = usual template to generate module look-n-feel
 *  2) module-error.tpl = to generate diagnostic error message about
 *     incorrect {bitmodule} parameters

\Note
error was used only in case the name was not there.
I fixed that error case. -- mose
 
 */
function smarty_block_bitmodule($params, $content, &$gBitSmarty) {
	extract($params);
	if (!isset($content))   return "";
	if (!isset($title))     $title = substr($content,0,12)."...";
	if (!isset($name))      $name  = ereg_replace("[^-_a-zA-Z0-9]","",$title);
	$gBitSmarty->assign('module_title', $title);
	$gBitSmarty->assign('module_name', $name);
	$gBitSmarty->assign_by_ref('module_content', $content);
	if ($_COOKIE[$name] == 'c') {
		$toggle_state = 'none';
	} else {
		$toggle_state = 'block';
	}
	$gBitSmarty->assign('toggle_state', $toggle_state);
	return $gBitSmarty->fetch('bitpackage:kernel/module.tpl');
}
?>
