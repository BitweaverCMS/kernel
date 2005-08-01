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
 * smarty_modifier_bit_long_time
 */
function smarty_modifier_bit_long_time($string)
{
	global $gBitSystem;
	return smarty_modifier_bit_date_format($string, $gBitSystem->get_long_time_format(), null, tra("%H:%M:%S %Z"));
}

/* vim: set expandtab: */

?>
