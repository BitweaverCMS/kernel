<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
* smarty_function_adsense
*/
function smarty_function_adsense( $params, &$gBitSmarty ) {
	global $gBitSystem, $gLibertySystem;
	if( $gBitSystem->isFeatureActive( 'liberty_plugin_status_dataadsense' ) ) {
		echo data_adsense( '', $params );
	} else {
		echo "You need to activate the adsense liberty plugin to use this.";
	}
}
?>
