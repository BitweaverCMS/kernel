<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

require_once $smarty->_get_plugin_filepath('modifier','bit_date_format');
function smarty_modifier_bit_long_date($string)
{
	global $gBitSystem;
	return smarty_modifier_bit_date_format($string, $gBitSystem->get_long_date_format(), null, "%A %d ".tra('of')." %B, %Y");
}

/* vim: set expandtab: */

?>
