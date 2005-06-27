<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_base
 */
require_once( KERNEL_PKG_PATH.'BitBase.php' );

/**
 * smarty_function_poll
 */
function smarty_function_poll($params, &$smarty) {
    global $polllib, $gBitSystem;

    extract($params);
    // Param = zone
	include_once( POLLS_PKG_PATH.'poll_lib.php' );
	include_once( LIBERTY_PKG_PATH.'LibertyComment.php' );

    if (empty($id)) {
      $id = $polllib->get_random_active_poll();
    }
    if($id) {
      $menu_info = $polllib->get_poll($id);
      $channels = $polllib->list_poll_options($id,0,-1,'option_id_asc','');
			if ($gBitSystem->getPreference('feature_poll_comments') == 'y') {
				$comments = new LibertyComment();
				$comments_count = $comments->count_comments("poll:".$menu_info["poll_id"]);
			}
			$smarty->assign('comments', $comments_count);
      $smarty->assign('ownurl',POLLS_PKG_URL.'results.php?poll_id='.$id);
      $smarty->assign('menu_info',$menu_info);
      $smarty->assign('channels',$channels["data"]);
      $smarty->display('bitpackage:polls/poll.tpl');
    }
}

/* vim: set expandtab: */

?>
