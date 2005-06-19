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
function smarty_function_pagination( $params, &$smarty ) {
	$pgnUrl = isset( $params['url'] ) ? $params['url'] : $_SERVER['PHP_SELF'];
	unset( $params['url'] );
    $smarty->assign( 'pgnUrl', $pgnUrl );

	$pgnVars = '';
	foreach( $params as $form_param => $form_val ) {
		$pgnVars .= "&amp;".$form_param."=".$form_val;
		$pgnHidden[$form_param] = $form_val;
	}
    $smarty->assign( 'pgnVars', $pgnVars );

    $smarty->display('bitpackage:kernel/pagination.tpl');
}
?>
