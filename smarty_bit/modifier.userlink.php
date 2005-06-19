<?php

function smarty_modifier_userlink($user,$class='username') {
   return '<a class="'.$class.'" href="'.USERS_PKG_URL.'index.php?home='.$user.'">'.$user.'</a>';
}

/* vim: set expandtab: */

?>
