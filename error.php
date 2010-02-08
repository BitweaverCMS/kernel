<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/error.php,v 1.6 2010/02/08 21:27:23 wjames5 Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

// Display the template
$gBitSmarty->assign( 'msg', strip_tags( $_REQUEST["error"] ));
$gBitSystem->display( 'error.tpl' , tra( 'Error' ), array( 'display_mode' => 'display' ));

?>
