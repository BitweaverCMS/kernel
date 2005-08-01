<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_menu
 */
function smarty_function_menu($params, &$gBitSmarty)
{
    global $menulib;
    extract($params);
    // Param = zone

    if (empty($id)) {
        $gBitSmarty->trigger_error("assign: missing id");
        return;
    }
    $menu_info = $menulib->get_menu($id);
    $channels = $menulib->list_menu_options($id,0,-1,'position_asc','');
    $gBitSmarty->assign('menu_info',$menu_info);
    $gBitSmarty->assign('channels',$channels["data"]);
    
    $gBitSmarty->display('bitpackage:users/user_menu.tpl');
}

/* vim: set expandtab: */

?>
