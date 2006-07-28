<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     displayUrl
 * Purpose:  give bqck the displal Url
 * -------------------------------------------------------------
 */
function smarty_modifier_displayUrl($string, $lib='BitPage') {
	return BitPage::getDisplayUrl($string);  

}

?>
