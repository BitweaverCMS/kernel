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
	global $gBitSmarty;

	if (!empty($params['comment'])) {
		$comment = $params['comment'];
		$gBitSmarty->assign('comment', $comment);
		$gBitSmarty->display('bitpackage:liberty/display_comment.tpl');
if (0) {
if (!empty($comment['children']) && count($comment['children']) >= 1) {
		foreach ($comment['children'] as $childComment) {
if (count($childComment) >= 1) {
			smarty_function_displaycomment(array('comment'=>$childComment));
		}
		}
		}
}
	}
}

?>
