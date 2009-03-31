<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * this generates a relatively unique ids for the 
 * ajax attachment portions of a form, which  must be uniquely identified 
 * when there are multiple forms enabling attachment uploads on one page.
 */
function smarty_function_form_id(){
	if( !isset( $_SESSION['form_id_index'] ) ){
		$ret = $_SESSION['form_id_index'] = 1;
	}else{
		$ret = ( $_SESSION['form_id_index']++ );
	}
	return $ret;
}
?>
