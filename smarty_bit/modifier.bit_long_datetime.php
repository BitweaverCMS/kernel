<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * required setup
 */
require_once $smarty->_get_plugin_filepath('modifier','bit_date_format');

/**
 * smarty_modifier_bit_long_datetime
 */
function smarty_modifier_bit_long_datetime($string)
{
	global $gBitSystem;
	return smarty_modifier_bit_date_format($string, $gBitSystem->get_long_datetime_format(), null, "%A %d ".tra('of')." %B, %Y[%H:%M:%S %Z]");
}

/* vim: set expandtab: */

?>
