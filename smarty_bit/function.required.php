<?php
function smarty_function_required( $pParams, &$gBitSmarty ) {
	require_once $gBitSmarty->_get_plugin_filepath('function','biticon');
	$biticon = array(
		'ipackage' => 'icons',
		'iname'    => 'emblem-important',
		'iforce'   => 'icon',
		'iexplain' => 'Required',
	);
	echo smarty_function_biticon( $biticon, $gBitSmarty );
}
?>
