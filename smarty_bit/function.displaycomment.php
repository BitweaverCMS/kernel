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
		if (empty($params['template'])) {
			$gBitSmarty->display('bitpackage:liberty/display_comment.tpl');
		} else {
			$gBitSmarty->display($params['template']);
		}
	}
}

?>
