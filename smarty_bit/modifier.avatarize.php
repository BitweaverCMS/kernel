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
function smarty_modifier_avatarize($user)
{
  global $gBitSystem;
  $avatar = $gBitSystem->get_user_avatar($user);
  if($gBitUser->userNameExists($user)&&$gBitSystem->get_user_preference($user,'user_information','public')=='public') {
  	$avatar = '<a title="'.$user.'" href="'.USERS_PKG_URL.'index.php?home='.$user.'">'.$avatar.'</a>';
  } 
  return $avatar;	
}

?>
