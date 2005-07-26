<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
* smarty_function_banner
*/
function smarty_function_banner($params, &$gBitSmarty)
{
    global $gBitSystem;
    include_once( BANNERS_PKG_PATH.'banner_lib.php' );
    if(!isset($bannerlib)) {
      $bannerlib = new BannerLib();
    }

    extract($params);
    // Param = zone

    if (empty($zone)) {
        $gBitSmarty->trigger_error("assign: missing 'zone' parameter");
        return;
    }
    $banner = $bannerlib->select_banner($zone);
    print($banner);
}

/* vim: set expandtab: */

?>
