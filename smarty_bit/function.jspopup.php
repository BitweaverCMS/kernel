<?php


function smarty_function_jspopup($params, &$smarty)
{
    extract($params);
    // Param = zone
    if(empty($href)) {
        $smarty->trigger_error("assign: missing href parameter");
        return;
    }
    $attrs = array();
    $attrs[] = "alwaysRaised=yes";
    $attrs[] = (!isset($scrollbars)) ? "scrollbars=no" : "scrollbars=".$scrollbars."";
    $attrs[] = (!isset($menubar)) ? "menubar=no" : "menubar=".$menubar."";
    $attrs[] = (!isset($resizable)) ? "resizable=yes" : "resizable=".$resizable."";
    if (isset($height)) $attrs[] = "height=".$height;
    if (isset($width)) $attrs[] = "width=".$width;
    print "href='#' onClick='javascript:window.open(\"$href\",\"\",\"" . join(",", $attrs) . "\");' ";
}

/* vim: set expandtab: */

?>
