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
 * Name:     capitalize
 * Purpose:  capitalize words in the string
 * -------------------------------------------------------------
 */
function smarty_modifier_iconify($string)
{
  // XINGICON what are we going to do with this function? is this needed?
  $string=substr($string,strlen($string)-3);
  if(file_exists(IMG_PKG_PATH."icn/$string".".gif")) {
    return "<img border='0' src='".IMG_PKG_URL."icn/${string}.gif' alt='icon' class='icon' />";
  } else {
    return "<img border='0' src='".IMG_PKG_URL."icn/else.gif' alt='icon' class='icon' />";
  }     
	
}

?>
