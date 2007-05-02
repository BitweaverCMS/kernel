<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_attachhelp( $pParams, &$gBitSmarty ) {
	global $gBitSystem;
	if( !empty( $pParams['hash'] ) && is_array( $pParams['hash'] )) {
		$pParams = array_merge( $pParams, $pParams['hash'] );
	}

	if( empty( $pParams['attachment_id'] )) {
		return tra( 'You need to provide an attachment_id' );
	}

	$gBitSmarty->assign( 'attachment', $pParams );
	return $gBitSmarty->fetch( 'bitpackage:liberty/attachhelp.tpl' );
}
?>
