<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/upload_slot_inc.php,v 1.2 2007/11/18 12:00:19 lsces Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

if( isset( $_REQUEST['upload_id'] ) && $gBitThemes->isAjaxRequest() ) {
	echo $gBitSmarty->fetch( 'bitpackage:kernel/upload_slot_inc.tpl' );
}
?>
