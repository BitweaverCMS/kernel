<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/error.php,v 1.5 2008/10/17 13:33:57 squareing Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

// Display the template
$gBitSmarty->assign( 'msg', strip_tags( $_REQUEST["error"] ));
$gBitSystem->display( 'error.tpl' , tra( 'Error' ), array( 'display_mode' => 'display' ));

?>
