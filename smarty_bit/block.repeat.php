<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File: block.repeat.php
 * Type: block
 * Name: repeat
 * Purpose: repeat a template block a given number of times
 * Parameters: count [required] - number of times to repeat
 * assign [optional] - variable to collect output
 * Author: Scott Matthewman <scott@matthewman.net>
 * -------------------------------------------------------------
 */
function smarty_block_repeat( $params, $content, &$gBitSmarty ) {
	if( !empty( $content ) ) {
		$intCount = intval( $params['count'] );
		if( $intCount < 0 ) {
			$gBitSmarty->trigger_error( "block: negative 'count' parameter" );
			return;
		}

		$strRepeat = str_repeat( $content, $intCount );
		if( !empty( $params['assign'] ) ) {
			$gBitSmarty->assign($params['assign'], $strRepeat );
		} else {
			echo $strRepeat;
		}
	}
}
?>
