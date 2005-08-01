<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_helplink
 */
function smarty_function_helplink($params, &$gBitSmarty)
{
    extract($params);
    // Param = zone
    if(empty($page)) {
        $gBitSmarty->trigger_error("assign: missing page parameter");
        return;
    }
    print("<a title='help' href='#' onClick='javascript:window.open(\"".WIKI_PKG_URL."index_p.php?page=$page\",\"\",\"menubar=no,scrollbars=yes,resizable=yes,height=600,width=500\");'><img border='0' src='img/icons/help.gif' alt='".tra("help")."' /></a>");
}

/* vim: set expandtab: */

?>
