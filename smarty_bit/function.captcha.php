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
	$size = !empty( $params['size'] ) ? $params['size'] : '5';
	$ret = '
		<div class="captcha">
			<img src="'.USERS_PKG_URL.'captcha_image.php" alt="'.tra('Random Image').'"/><input type="text" name="captcha" size="'.$size.'"/>
		</div>
	'; 
	print $ret;
}
?>
