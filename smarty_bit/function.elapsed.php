<?php
function smarty_function_elapsed($params, &$smarty)
{
    global $gBitSystem;
    
    $ela = number_format($gBitSystem->mTimer->elapsed(),2);
    print($ela);
}

/* vim: set expandtab: */

?>
