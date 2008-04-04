<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_adsense.php,v 1.2 2008/04/04 20:01:22 jetskijoe Exp $
 *
 * Copyright ( c ) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
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