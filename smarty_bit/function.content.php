<?php
function smarty_function_content($params, &$smarty)
{
    global $gBitSystem;
    global $dcslib;
    include_once( DCS_PKG_PATH.'dcs_lib.php' );
    extract($params);
    // Param = zone

    if (empty($id)) {
        $smarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
    $data = $dcslib->get_actual_content($id);
    print($data);
}

?>
