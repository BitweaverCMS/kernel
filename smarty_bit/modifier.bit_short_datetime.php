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
function smarty_modifier_bit_short_datetime($string)
{
	global $gBitSystem;
	return smarty_modifier_bit_date_format($string, $gBitSystem->get_short_datetime_format(), null, "%a %d ".tra('of')." %b, %Y[%H:%M %Z]");
}

/* vim: set expandtab: */

?>
