<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_rss
 */
function smarty_function_captcha($params, &$gBitSmarty) {
	global $gLibertySystem;
	$ret = '<img src="'.USERS_PKG_URL.'captcha_image.php" alt="'.tra('Random Image').'"/>...'; 
	print $ret;
}
?>
