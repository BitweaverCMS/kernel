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
	$pClass = isset( $pParams['class'] ) ? ' '.$pParams['class'] : '';

	$tClass	= isset( $pParams['class'] ) ? ' '.$pParams['class'] : '';
	$tClick	= isset( $pParams['onclick'] ) ? ' onclick="'.$pParams['onclick'].'"' : '';
	$tTitle	= tra( isset( $pParams['title'] ) ? $pParams['title'] : 'No Title' );

	$ret  = '<div class="tabpage' . $pClass . '">';
	$ret .= '<h4 class="tab' . $tClass	. '"' . $tClick . '>' . $tTitle . '</h4>';
	$ret .= $pContent;
	$ret .= '</div>';

	return $ret;
}
?>
