<?php
/** \file
 * $Header: /cvsroot/bitweaver/_bit_kernel/smarty_bit/function.var_dump.php,v 1.1 2005/06/19 04:52:53 bitweaver Exp $
 *
 * \author zaufi <zaufi@sendmail.ru>
 */


/**
 * \brief Smarty plugin to add variable dump to debug console log
 * Usage format {var_dump var=var_name_2_dump}
 */
function smarty_function_var_dump($params, &$smarty)
{
  global $debugger;
  require_once( DEBUG_PKG_PATH.'debugger.php' );
  //
  $v = $params['var'];
  if (strlen($v) != 0)
  {
    $tmp = $smarty->get_template_vars();
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
