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

$flag = $gBitSystem->getConfig( 'powered_by_DB_Logo' );
if ( $flag == 'y' ) {
	global $gBitDbType;
	$flag = $gBitDbType;
}
$gBitSmarty->assign( 'gDbType', $flag );
?>
