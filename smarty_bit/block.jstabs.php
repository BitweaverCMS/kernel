<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {jstabs} block plugin
 *
 * Type:		block
 * Name:		jstabs
 * Input:		
 * Abstract:	Used to enclose a set of tabs
 */
function smarty_block_jstabs($params, $content, &$smarty) {
	$ret  = '<div class="tabpane">'.$content.'</div>';
	$ret .= "<script type=\"text/javascript\">//<![CDATA[\nsetupAllTabs();\n//]]></script>";
	return $ret;
}
?>
