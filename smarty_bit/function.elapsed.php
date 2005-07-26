<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_elapsed
 */
function smarty_function_elapsed($params, &$gBitSmarty)
{
    global $gBitSystem;
    
    $ela = number_format($gBitSystem->mTimer->elapsed(),2);
    print($ela);
}

/* vim: set expandtab: */

?>
