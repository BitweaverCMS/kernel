<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_ed
 */
function smarty_function_ed($params, &$gBitSmarty)
{
    global $gBitSystem;
    extract($params);
    // Param = zone

    if (empty($id)) {
        $gBitSmarty->trigger_error("ed: missing 'id' parameter");
        return;
    }
    
    print($banner);
}

/* vim: set expandtab: */

?>
