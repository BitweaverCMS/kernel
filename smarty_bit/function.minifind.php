<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {minifind} function plugin
 *
 * Type:     function
 * Name:     minifind
 * Input:    all parameters that are passed in will be added as <input type="hidden" name=$name value=$value>
 * Output:   a small form that allows you to search your table using $_REQUEST['find'] as search value
 */
function smarty_function_minifind($params, &$gBitSmarty) {
	$gBitSmarty->assign( 'hidden',$params );
    $gBitSmarty->display( 'bitpackage:kernel/minifind.tpl' );
}
?>
