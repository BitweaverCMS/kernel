<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {jstab} block plugin
  *
 * Type:		block
 * Name:		jstab
 * Input:
 * Abstract:	Used to enclose a set of tabs
 */
function smarty_block_jstab( $pParams, $pContent, &$gBitSmarty ) {
	// if this is modified, please adjust the preg_match_all() pattern in block.jstabs.php
	$ret  = '<div class="tabpage">';
	$ret .= '<h4 class="tab">'.tra( isset( $pParams['title'] ) ? $pParams['title'] : 'No Title' ).'</h4>';
	$ret .= $pContent;
	$ret .= '</div>';
	return $ret;
}
?>
