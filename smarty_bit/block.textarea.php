<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @link http://www.bitweaver.org/wiki/block_textarea block_textarea
 */

function smarty_block_textarea( $pParams, $pContent, &$gBitSmarty ) {
	global $gBitSystem;
	$attributes = '';
	$style = '';
	$rows = $gBitSystem->getConfig('liberty_textarea_height', 20);
	$cols = $gBitSystem->getConfig('liberty_textarea_width', 35);
	foreach ($pParams as $_key=>$_value) {
		switch ($_key) {
		case 'name':
		case 'id':
			$gBitSmarty->assign("textarea_".$_key, $_value);
			break;
		case 'cols':
		case 'rows':
			$$_key = $_value;
			break;
		case 'style':
			$style .= $_key;
			break;
		default:
			$attributes .= $_key.'="'.$_value.'"';
			break;
		}
	}
	$style .= (empty($style) ? '' : ';').'height:'.( !empty( $_COOKIE['rows'] ) ? $_COOKIE['rows'] : $rows ).'em;'.'width:'.(!empty($_COOKIE['cols']) ? $_COOKIE['cols'] : $cols).'em;';
	$gBitSmarty->assign('textarea_attributes', $attributes);
	$gBitSmarty->assign('textarea_data', $pContent);
	if (!empty($style)) {
		$gBitSmarty->assign('textarea_style', 'style="'.$style.'"');
	}
	$gBitSmarty->assign('textarea_cols', $cols);
	$gBitSmarty->assign('textarea_rows', $rows);


	return $gBitSmarty->fetch("bitpackage:liberty/edit_textarea.tpl");
}
?>
