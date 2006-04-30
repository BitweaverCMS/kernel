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
 * Name:     countryflag
 * Purpose:  get countryflag for a given user
 * -------------------------------------------------------------
 */
function smarty_modifier_countryflag($user)
{
  global $gBitSystem;
  $flag = $gBitSystem->getConfig('users_country','Other',$user);
  return "<img alt='flag' src='".IMG_PKG_URL."flags/".$flag.".gif' />";
}

?>
