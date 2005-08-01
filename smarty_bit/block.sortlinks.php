<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/** 
 * Smarty plugin 
 * ------------------------------------------------------------- 
 * File: block.repeat.php 
 * Type: block 
 * Name: repeat 
 * Purpose: repeat a template block a given number of times 
 * Parameters: count [required] - number of times to repeat 
 * assign [optional] - variable to collect output 
 * Author: Scott Matthewman <scott@matthewman.net> 
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
