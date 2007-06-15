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

if( !isset( $moduleParams['module_params']['no_dblogo'] )) {
	global $gBitDbType;
	$gBitSmarty->assign( 'gBitDbType', $gBitDbType );
}

if ( isset($moduleParams['module_params']['large']) ) {
	$gBitSmarty->assign( 'size', 'large' );
}
?>
