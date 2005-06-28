<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_menu
 */
function smarty_function_menu($params, &$smarty)
{
    global $menulib;
    extract($params);
    // Param = zone

    if (empty($id)) {
        $smarty->trigger_error("assign: missing id");
        return;
    }
    $menu_info = $menulib->get_menu($id);
    $channels = $menulib->list_menu_options($id,0,-1,'position_asc','');
    $smarty->assign('menu_info',$menu_info);
    $smarty->assign('channels',$channels["data"]);
    
    $smarty->display('bitpackage:users/user_menu.tpl');
}

/* vim: set expandtab: */

?>
