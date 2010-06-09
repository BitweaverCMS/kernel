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

if( !empty( $module_params ) ) {
	$gBitSmarty->assign( 'modParams', $module_params );
}

$gBitSmarty->assign('adSenseActive', !empty($module_params['client']) &&
	$gBitSystem->isFeatureActive( 'liberty_plugin_status_dataadsense' ));
?>