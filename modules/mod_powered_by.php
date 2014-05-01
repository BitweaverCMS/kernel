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

global $gBitDbSystem;
$_template->tpl_vars['gBitDbSystem'] = new Smarty_variable( $gBitDbSystem );

if( !isset( $moduleParams['module_params']['no_dblogo'] )) {
	global $gBitDbType;
	$_template->tpl_vars['gBitDbType'] = new Smarty_variable( $gBitDbType );
}

if( isset( $moduleParams['module_params']['large'] ) || $gBitSystem->getConfig( 'site_icon_size', 'small' ) == 'large' ) {
	$_template->tpl_vars['size'] = new Smarty_variable( '/large' );
}
?>
