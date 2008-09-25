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
 * Input:    all parameters (except legend) that are passed in will be added as <input type="hidden" name=$name value=$value>. The 'legend' parameter will be used as the form legend, a string is expected.
 * Output:   a small form that allows you to search your table using $_REQUEST['find'] as search value
 */
function smarty_function_minifind($params, &$gBitSmarty) {
	
	if(isset($params['legend'])) {
		$legend = $params['legend'];
		unset($params['legend']);
	} else {
		$legend = 'find in entries';
	}

	if( !empty( $params['prompt'] ) ) {
		$gBitSmarty->assign( 'prompt', tra( $params['prompt'] ) );
	}
	
	$gBitSmarty->assign( 'legend',$legend );
	$gBitSmarty->assign( 'hidden',$params );
	$gBitSmarty->display( 'bitpackage:kernel/minifind.tpl' );
}
?>
