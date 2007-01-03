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
function smarty_modifier_display_bytes( $pBytes, $pDecimalPlaces=1 )
{
	if( $pBytes >= 1073741824 ) {
		$ret = round( ($pBytes / 1073741824), $pDecimalPlaces ).' GB';
	} elseif( $pBytes >= 1048576 ) {
		$ret = round( ($pBytes / 1048576), $pDecimalPlaces ).' MB';
	} elseif( $pBytes >= 1024 ) {
		$ret = round( ($pBytes / 1024), $pDecimalPlaces ).' KB';
	} else {
		$ret = $pBytes.' '.tra( 'Bytes' );
	}
	return $ret;	
}

?>
