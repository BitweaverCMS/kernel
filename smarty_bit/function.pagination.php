<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {pagination} function plugin
 *
 * Type:     function
 * Name:     pagination
 * Input:
 *           - <attribute>=<value>  (optional) - pass in any attributes and they will be added to the pagination string
 * Output:   url of the form: $PHP_SELF?attribute1=value1&attribute2=value2
 */
function smarty_function_pagination( $params, &$gBitSmarty ) {
	$pgnUrl = isset( $params['url'] ) ? $params['url'] : $_SERVER['PHP_SELF'];
	unset( $params['url'] );
    $gBitSmarty->assign( 'pgnUrl', $pgnUrl );

	$pgnVars = '';
	foreach( $params as $form_param => $form_val ) {
		$pgnVars .= "&amp;".$form_param."=".$form_val;
		$pgnHidden[$form_param] = $form_val;
	}
    $gBitSmarty->assign( 'pgnVars', $pgnVars );
	$gBitSmarty->assign( 'pgnHidden', $pgnHidden );
    $gBitSmarty->display('bitpackage:kernel/pagination.tpl');
}
?>
