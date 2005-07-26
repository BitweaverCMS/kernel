<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {nexus} function plugin
 *
 * Type:	function
 * Name:	nexus
 * Input:	- id	(required) - id of the menu that should be displayed
 */
function smarty_function_nexus( $params, &$gBitSmarty ) {
	extract($params);

	if( empty( $id ) ) {
		$gBitSmarty->trigger_error("assign: missing id");
		return;
	}

	require_once( NEXUS_PKG_PATH.'Nexus.php' );
	$tmpNexus = new Nexus( $id );
	$nexusMenu = $tmpNexus->mInfo;

	$gBitSmarty->assign( 'nexusMenu', $nexusMenu );
	$gBitSmarty->assign( 'nexusId', $id );
	$gBitSmarty->display('bitpackage:nexus/nexus_module.tpl');
}
?>