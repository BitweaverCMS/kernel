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
 * smarty_modifier_bit_short_date
 */
function smarty_modifier_bit_short_date($string)
{
	global $gBitSystem;
	return smarty_modifier_bit_date_format($string, $gBitSystem->get_short_date_format(), null, "%a %d ".tra('of')." %b, %Y");
}

/* vim: set expandtab: */

?>
