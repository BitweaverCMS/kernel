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
function smarty_function_nexus( $params, &$smarty ) {
	extract($params);

	if( empty( $id ) ) {
		$smarty->trigger_error("assign: missing id");
		return;
	}

	require_once( NEXUS_PKG_PATH.'Nexus.php' );
	$tmpNexus = new Nexus( $id );
	$nexusMenu = $tmpNexus->mInfo;

	$smarty->assign( 'nexusMenu', $nexusMenu );
	$smarty->assign( 'nexusId', $id );
	$smarty->display('bitpackage:nexus/nexus_module.tpl');
}
?>