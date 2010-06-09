<?php
/**
 * $Header$
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
