<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {forminput} block plugin
 *
 * Type:     block
 * Name:     forminput
 */
function smarty_block_forminput($params, $content, &$smarty) {
	if( $content ) {
		$ret = '<div class="forminput">'.$content.'</div>';
		return $ret;
	}
}
?>
