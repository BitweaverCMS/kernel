<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {navbar} block plugin
 *
 * Type:	block
 * Name:	navbar
 * Input:	set of links that are used for navigation purposes
 */
function smarty_block_navbar($params, $content, &$gBitSmarty) {
	$links = smarty_block_navbar_get_links( $content );
	$gBitSmarty->assign( 'links',$links );
	return $gBitSmarty->fetch( 'bitpackage:kernel/navbar.tpl' );
}
function smarty_block_navbar_get_links( $content ) {
	$links = array();
	if( preg_match_all( "/<a.*?href=\".*?\">.*?<\/a>/i",$content,$res ) ) {
		$res = $res[0];
		$links = array_unique( $res );
	}
	return $links;
}
?>