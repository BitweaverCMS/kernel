<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_rss
 */
function smarty_function_rss($params, &$gBitSmarty) {
  global $gLibertySystem;
  include_once( LIBERTY_PKG_PATH.'plugins/data.rss.php' );
  $feed = rss_parse_data("", $params);
  print $feed;
}
?>
