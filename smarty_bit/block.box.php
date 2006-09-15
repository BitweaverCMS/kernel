<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {box} block plugin
 *
 * Type:	block
 * Name:	box
 * Input:
 *			- title		(optional)	box title
 *			- class		(optional)	overrides the default class 'box'
 *			- biticon values	(optional)	see function.biticon.php for details
 *			- idiv		(optional)	name of class of div that surrounds icon (if not set, no div is created)
 * @uses smarty_function_escape_special_chars()
 * @todo somehow make the variable that is contained within $iselect global --> this will allow importing of outside variables not set in $_REQUEST
 */
function smarty_block_box($params, $content, &$gBitSmarty) {
	$atts = '';
	foreach( $params as $key => $val ) {
		switch( $key ) {
			case 'title':
				$gBitSmarty->assign( $key, tra( $val ) );
				break;
			case 'class':
			case 'iclass':
			case 'ipackage':
			case 'iname':
			case 'iexplain':
			case 'idiv':
				$gBitSmarty->assign( $key,$val );
				break;
			default:
				$atts .= $key.'="'.$val.'" ';
				break;
		}
	}
	$gBitSmarty->assign( 'content',$content );
	$gBitSmarty->assign( 'atts',$atts );
	return $gBitSmarty->fetch( 'bitpackage:kernel/box.tpl' );
}
?>
