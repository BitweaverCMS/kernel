<?php
/**
 * Smarty {pagination} function plugin
 * @package Smarty
 * @subpackage plugins
 * @link http://www.bitweaver.org/wiki/function_pagination function_pagination
 */

/**
 * Smarty {pagination} function plugin
 *
 * Type:     function<br>
 * Name:     pagination<br>
 * Input:<br>
 *           - <attribute>=<value>  (optional) - pass in any attributes and they will be added to the pagination string<br>
 * Output:   url of the form: $PHP_SELF?attribute1=value1&attribute2=value2
 */
function smarty_function_pagination( $params, &$gBitSmarty ) {
    $pgnUrl = $gBitSmarty->get_template_vars('returnURL');
    if ( isset( $params['url'] ) ) {
     	  $pgnUrl = $params['url'];
        unset( $params['url'] );
    }
    if( empty( $pgnUrl ) ) {
        $pgnUrl = $_SERVER['PHP_SELF'];
	}

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
