<?php

/*
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
  $flag = $gBitSystem->get_user_preference($user,'country','Other');
  return "<img alt='flag' src='".IMG_PKG_URL."flags/".$flag.".gif' />";
}

?>
