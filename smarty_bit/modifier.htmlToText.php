<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty htmlToText modifier plugin
 *
 * Type:	modifier<br>
 * Name:	html2text<br>
 * Purpose: transform a html to a text version 
 * @param string
 */
function smarty_modifier_htmlToText( $string ) {
	return preg_replace('/<.*>/U', '', $string);
}
?>
