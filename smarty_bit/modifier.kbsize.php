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
 * Name:     kbsize
 * Purpose:  returns size in Mb, Kb or bytes.
 * -------------------------------------------------------------
 */
function smarty_modifier_kbsize( $pSize ) {
	$i = 0;
	$iec = array( "b", "Kb", "Mb", "Gb", "Tb", "Pb", "Eb", "Zb", "Yb" );
	while( ( $pSize / 1024 ) > 1 ) {
		$pSize = $pSize / 1024;
		$i++;
	}
	return substr( $pSize, 0, strpos( $pSize, '.' ) + 2 )." ".$iec[$i];
}
?>
