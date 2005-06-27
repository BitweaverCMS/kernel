<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_displaycomment
 */
function smarty_function_displaycomment($params) {
	global $smarty;
	if (!empty($params['comment'])) {
		$comment = $params['comment'];
		$smarty->assign('comment', $comment);
		$smarty->display('bitpackage:liberty/display_comment.tpl');
		foreach ($comment['children'] as $childComment) {
			smarty_function_displaycomment(array('comment'=>$childComment));
		}
	}
}

?>
