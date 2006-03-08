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
function smarty_function_poll($params, &$gBitSmarty) {
    global $gBitSystem;
    require_once( POLLS_PKG_PATH.'poll_lib.php' );
    if(!isset($polllib)) {
      $polllib = new PollLib();
    }

    extract($params);
    // Param = zone

    if (empty($id)) {
      $id = $polllib->get_random_active_poll();
    }
    if($id) {
      $poll_info = $polllib->get_poll($id);
      $polls = $polllib->list_poll_options($id,0,-1,'option_id_asc','');
			if ($gBitSystem->getPreference('feature_poll_comments') == 'y') {
                                include_once( LIBERTY_PKG_PATH.'LibertyComment.php' );
				$comments = new LibertyComment();
				$comments_count = $comments->count_comments("poll:".$poll_info["poll_id"]);
			}
			$gBitSmarty->assign('comments', $comments_count);
      $gBitSmarty->assign('poll_info',$poll_info);
      $gBitSmarty->assign('polls',$polls["data"]);
      $gBitSmarty->display('bitpackage:polls/poll.tpl');
    }
}

/* vim: set expandtab: */

?>
