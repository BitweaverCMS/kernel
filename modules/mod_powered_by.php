<?php
/**
 * $Header$
 *
 * Copyright ( c ) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * @package kernel
 * @subpackage modules
 */

$flag = '';
if ( isset($moduleParams['module_params']['powered_by_DB_Logo']) ) {
	global $gBitDbType;
	$flag = $gBitDbType;
}
$gBitSmarty->assign( 'gDbType', $flag );
if ( isset($moduleParams['module_params']['large']) ) {
	$gBitSmarty->assign( 'size', 'large' );
} else {
	$gBitSmarty->assign( 'size', '' );
}
if ( isset( $moduleParams['module_params']['htmlpurifier'] ) ) {
	$gBitSmarty->assign( 'htmlpurify', $moduleParams['module_params']['htmlpurifier'] );
} else {
	$gBitSmarty->assign( 'htmlpurifier', 'y' );
}
?>
