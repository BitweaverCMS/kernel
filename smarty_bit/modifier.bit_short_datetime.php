<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * required setup
 */
global $gBitSmarty;
require_once $gBitSmarty->_get_plugin_filepath( 'modifier', 'bit_date_format' );

/**
 * smarty_modifier_bit_short_datetime
 */
function smarty_modifier_bit_short_datetime( $pString, $time='' ) {
	global $gBitSystem;
	if( !empty( $time ) && date( 'Ymd' ) == date( 'Ymd', $pString )) {
		return smarty_modifier_bit_date_format( $pString, $gBitSystem->get_short_time_format(), '%H:%M %Z' );
	} else {
		return smarty_modifier_bit_date_format( $pString, $gBitSystem->get_short_datetime_format(), '%a %d of %b, %Y (%H:%M %Z)' );
	}
}
?>
