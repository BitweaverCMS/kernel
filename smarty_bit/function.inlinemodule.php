<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Usage: add to the body of any .tpl file
 * Example: {inlinemodule file="bitpackage:wiki/mod_last_modif_pages.tpl" rows="50"}
 */
function smarty_function_inlinemodule($params, &$gBitSmarty) {
	global $module_rows, $module_title, $module_params;

	$module_rows   = ( !empty( $params['rows'] ) && is_numeric( trim( $params['rows'] ) ) ? $params['rows'] : NULL );
	$module_title  = ( !empty( $params['title'] ) ? $params['title'] : NULL );
	$module_params = ( !empty( $params ) ? $params : NULL );
	print $gBitSmarty->fetch( $params['file'] );
}
?>
