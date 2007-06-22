<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_required
 */
function smarty_function_required( $pParams, &$gBitSmarty ) {
	require_once $gBitSmarty->_get_plugin_filepath('function','biticon');
	$biticon = array(
		'ipackage' => 'icons',
		'iname'    => 'emblem-important',
		'iexplain' => 'Required',
	);
	$ret = smarty_function_biticon( $biticon, $gBitSmarty );

	if( !empty( $pParams['legend'] )) {
		$ret = "<p>$ret ".tra( "Elements marked with this symbol are required." )."</p>";
	}
	return $ret;
}
?>
