<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {forminput} block plugin
 *
 * Type:     block
 * Name:     forminput
 */
function smarty_block_forminput($params, $content, &$gBitSmarty) {
	// defaults
	$class = "";
	$id = "";

	extract( $params );

	if( !empty( $class ) ){
		$class = ' '.trim( $class );
	}

	if( !empty( $id ) ){
		$id = 'id="'.trim( $id ).'"';
	}

	if( $content ) {
		$ret = '<div class="forminput'.$class.'" '.$id.' >'.$content.'</div>';
		return $ret;
	}
}
?>
