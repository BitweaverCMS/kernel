<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {form} block plugin
 *
 * Type:     block
 * Name:     form
 * Input:
 *           - legend      (optional) - text that appears in the legend
 */
function smarty_block_legend($params, $content, &$gBitSmarty) {
	if( $content ) {
		$ret = '<fieldset><legend>'.$params['legend'].'</legend>';
		$ret .= $content;
		$ret .= '<div class="clear"></div></fieldset>';
		return $ret;
	}
}
?>
