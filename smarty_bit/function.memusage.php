<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_memusage
 */
function smarty_function_memusage($params, &$gBitSmarty)
{
    if (function_exists('memory_get_usage')) {
      $memusage=memory_get_usage();
    } else {
      $memusage=0;
    }
    
    if ($memusage>0) {
      $memunit="B";
      if ($memusage>1024) {
        $memusage=$memusage/1024;
        $memunit="kB";
      }
      if ($memusage>1024) {
        $memusage=$memusage/1024;
        $memunit="MB";
      }
      if ($memusage>1024) {
        $memusage=$memusage/1024;
        $memunit="GB";
      }
      print(number_format($memusage,2).$memunit);
    }
}

/* vim: set expandtab: */

?>
