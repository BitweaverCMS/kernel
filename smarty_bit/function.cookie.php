<?php
function smarty_function_cookie($params, &$smarty)
{
    global $taglinelib;
    include_once( KERNEL_PKG_PATH.'tagline_lib.php' );
    extract($params);
    // Param = zone
  
  	if( $taglinelib ) {
	    $data = $taglinelib->pick_cookie();
   		print($data);
	}
}

/* vim: set expandtab: */

?>
