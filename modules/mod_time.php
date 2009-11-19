<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_time.php,v 1.7 2009/11/19 19:25:21 wjames5 Exp $
 *
 * Copyright ( c ) 2004 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @package kernel
 * @subpackage modules
 */
extract( $moduleParams );

if( !empty( $module_params ) ) {
	$gBitSmarty->assign( 'modParams', $module_params );
}
?>
