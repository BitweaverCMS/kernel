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
	global $gLibertySystem, $gBitUser;
	if( !$gBitUser->hasPermission( 'p_users_bypass_captcha' ) ) {
		$size = !empty( $params['size'] ) ? $params['size'] : '5';
		$getString = 'size='.$size;
		if( @BitBase::verifyId( $params['width'] ) ) {
			$getString .= '&width='.$params['width'];
		}
		if( @BitBase::verifyId( $params['height'] ) ) {
			$getString .= '&height='.$params['height'];
		}
		$ret = '
			<div class="captcha">
				<img src="'.USERS_PKG_URL.'captcha_image.php?'.$getString.'" alt="'.tra('Random Image').'" align="top"/><input type="text" name="captcha" size="'.$size.'"/>
				<br/><em class="small">'.tra( 'Spam Protection: Please copy the code into the box.' ).'</em>
			</div>
		'; 
		print $ret;
	}
}
?>
