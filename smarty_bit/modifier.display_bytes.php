<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     display_bytes
 * Purpose:  show an integer in a human readable Byte size with optional resolution
 * Example:  {$someFile|filesize|display_bytes:2}
 * -------------------------------------------------------------
 */
function smarty_modifier_display_bytes( $pSize, $pDecimalPlaces = 1 ) {
	$i = 0;
	$iec = array( "B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB" );
	while( ( $pSize / 1024 ) > 1 ) {
		$pSize = $pSize / 1024;
		$i++;
	}
	return round( $pSize, $pDecimalPlaces )." ".$iec[$i];
}
?>
