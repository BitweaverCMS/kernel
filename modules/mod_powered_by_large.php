<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_kernel/modules/Attic/mod_powered_by_large.php,v 1.1 2007/06/08 22:36:53 lsces Exp $
 *
 * Copyright ( c ) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * @package kernel
 * @subpackage modules
 */

$flag = '';
//if ( isset($module_params['powered_by_DB_Logo']) ) {
	global $gBitDbType;
	$flag = $gBitDbType;
//}
$gBitSmarty->assign( 'gDbType', $flag );
?>
