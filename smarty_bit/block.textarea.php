<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @link http://www.bitweaver.org/wiki/block_textarea block_textarea
 */

/** 
 * Smarty plugin 
 * ------------------------------------------------------------- 
 * File: block.textarea.php 
 * Type: block 
 * Name: textarea 
 * ------------------------------------------------------------- 
 */ 
function smarty_block_textarea( $pParams, $pContent, &$gBitSmarty ) {
	global $gBitSystem, $gContent;
	$attributes = '';
	$style = '';
	if (empty($pParams['rows'])) {
		$pParams['rows'] = (empty($_COOKIE['rows']) ? $gBitSystem->getConfig('liberty_textarea_height', 20) : $_COOKIE['rows']);
	}
	if (empty($pParams['cols'])) {
		$pParams['cols'] = (empty($_COOKIE['cols']) ? $gBitSystem->getConfig('liberty_textarea_width', 35) : $_COOKIE['rows']);
	}
	if (empty($pParams['id'])) {
		$pParams['id'] = LIBERTY_TEXT_AREA;
	}
	foreach ($pParams as $_key=>$_value) {
		switch ($_key) {
		case 'name':
		case 'id':
		case 'help':
		case 'noformat':
		case 'label':
			$gBitSmarty->assign("textarea_".$_key, $_value);
			break;
		case 'style':
			$style .= $_key;
			break;
		case 'gContent':
			// Trick out gContent
			$oldContent = $gContent;
			$gContent = $_value;
			$gBitSmarty->assign('gContent', $_value);
			break;
		default:
			$attributes .= $_key.'="'.$_value.'"';
			break;
		}
	}
	// We control hieght here when bnspell is on so as to be able to not
	// lose the rest of the style on the textarea.
	if ($gBitSystem->isPackageActive('bnspell')) {
		$style .= (empty($style) ? '' : ';').'height:'.$pParams['rows'].'em;';
	}
	$gBitSmarty->assign('textarea_attributes', $attributes);
	$gBitSmarty->assign('textarea_data', $pContent);
	if (!empty($style)) {
		$gBitSmarty->assign('textarea_style', 'style="'.$style.'"');
	}
	
	$ret = $gBitSmarty->fetch("bitpackage:liberty/edit_textarea.tpl");

	// Restore gContent
	if (isset($oldContent)) {
		$gContent = $oldContent;
		$gBitSmarty->assign('gContent', $oldContent);
	}

	return $ret;
}
?>
