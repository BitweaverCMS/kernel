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
require_once $gBitSmarty->_get_plugin_filepath('modifier','bit_date_format');

/**
 * smarty_modifier_bit_short_datetime
 */
function smarty_modifier_bit_short_datetime($string, $time='')
{
	global $gBitSystem;
	if ( !empty( $time ) && date( 'Ymd' ) == date( 'Ymd', $string ) ) {
		return smarty_modifier_bit_date_format($string, $gBitSystem->get_short_time_format(), null, "%Y-%m-%d %H:%M");
} else {
		return smarty_modifier_bit_date_format($string, $gBitSystem->get_short_datetime_format(), null, "%Y-%m-%d %H:%M");
	}
}
/* vim: set expandtab: */

?>
