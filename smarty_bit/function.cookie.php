<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_cookie
 */
function smarty_function_cookie($params, &$gBitSmarty) {
    global $taglinelib;
    include_once( TIDBITS_PKG_PATH.'BitFortuneCookies.php' );
	print( $taglinelib->pick_cookie() );
}
?>
