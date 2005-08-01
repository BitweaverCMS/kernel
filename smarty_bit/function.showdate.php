<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_showdate
 */
function smarty_function_showdate($params, &$gBitSmarty)
{
    
    extract($params);
    // Param = zone

    if (empty($mode)) {
        $gBitSmarty->trigger_error("assign: missing 'mode' parameter");
        return;
    }
    print(date($mode));
}

/* vim: set expandtab: */

?>
