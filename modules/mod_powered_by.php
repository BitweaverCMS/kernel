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

// original:
// $flag = '';
// if ( isset($module_params['powered_by_DB_Logo']) ) {
//	global $gBitDbType;
//	$flag = $gBitDbType;
// }
// $gBitSmarty->assign( 'gDbType', $flag );
// new:

global $gBitDbType, $gBitDbSystem;

$gBitSmarty->assign( 'gDbType', $gBitDbType );
$gBitSmarty->assign( 'gDbSystem', $gBitDbSystem );

?>
