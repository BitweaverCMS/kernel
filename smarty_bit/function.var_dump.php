<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/** \file
 * $Header: /cvsroot/bitweaver/_bit_kernel/smarty_bit/function.var_dump.php,v 1.1.1.1.2.2 2005/07/26 15:50:10 drewslater Exp $
 *
 * \author zaufi <zaufi@sendmail.ru>
 */


/**
 * \brief Smarty plugin to add variable dump to debug console log
 * Usage format {var_dump var=var_name_2_dump}
 */
function smarty_function_var_dump($params, &$gBitSmarty)
{
  global $debugger;
  require_once( DEBUG_PKG_PATH.'debugger.php' );
  //
  $v = $params['var'];
  if (strlen($v) != 0)
  {
    $tmp = $gBitSmarty->get_template_vars();
    if (is_array($tmp) && isset($tmp[$v]))
      $debugger->msg("Smarty var_dump(".$v.') = '.print_r($tmp[$v], true));
    else
      $debugger->msg("Smarty var_dump(".$v."): Variable not found");
  }
  else
    $debugger->msg("Smarty var_dump: Parameter 'var' not specified");
  return '<!-- var_dump('.$v.') -->';
}

?>
