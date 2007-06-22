<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/** 
 * Smarty plugin 
 * ------------------------------------------------------------- 
 * File: block.sortlinks.php 
 * Type: block 
 * Name: sortlinks 
 * ------------------------------------------------------------- 
 */ 
function smarty_block_sortlinks($params, $content, &$gBitSmarty) 
{ 
if ($content) { 
  $links=spliti("\n",$content);
  $links2=array();
  foreach ($links as $value) {
    $splitted=preg_split("/[<>]/",$value,-1,PREG_SPLIT_NO_EMPTY);
    $links2[$splitted[2]]=$value;
  }

	if( isset( $params['order'] ) && $params['order']=='reverse' ) {
		krsort( $links2 );
	} else {
		ksort($links2);
	}

  foreach($links2 as $value) {
    echo $value;
  }
}
} 
?> 
