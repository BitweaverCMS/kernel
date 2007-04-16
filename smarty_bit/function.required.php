<?php
function smarty_function_required( $pParams, &$gBitSmarty ) {
	require_once $gBitSmarty->_get_plugin_filepath('function','biticon');
	$biticon = array(
		'ipackage' => 'icons',
		'iname'    => 'emblem-important',
		'iexplain' => 'Required',
	);
	$ret = smarty_function_biticon( $biticon, $gBitSmarty );

	if( !empty( $pParams['legend'] )) {
		$ret = "<p>$ret ".tra( "Items marked with this symbol are required." )."</p>";
	}
	return $ret;
}
?>
