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
function smarty_block_jstab($params, $content, &$smarty) {
	$ret  = '<div class="tabpage">';
	$ret .= '<h4 class="tab">'.tra( isset( $params['title'] ) ? $params['title'] : 'No Title' ).'</h4>';
	$ret .= $content;
	$ret .= '</div>';
	return $ret;
}
?>
