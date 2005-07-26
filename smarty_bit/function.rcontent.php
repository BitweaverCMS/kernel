<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_rcontent
 */
function smarty_function_rcontent($params, &$gBitSmarty)
{
    global $gBitSystem;
    include_once( DCS_PKG_PATH.'dcs_lib.php' );
    extract($params);
    // Param = zone

    if (empty($id)) {
        $gBitSmarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
    $data = $dcslib->get_random_content($id);
    print($data);
}

/* vim: set expandtab: */

?>
