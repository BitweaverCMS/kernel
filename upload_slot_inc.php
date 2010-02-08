<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/upload_slot_inc.php,v 1.3 2010/02/08 21:27:23 wjames5 Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

if( isset( $_REQUEST['upload_id'] ) && $gBitThemes->isAjaxRequest() ) {
	echo $gBitSmarty->fetch( 'bitpackage:kernel/upload_slot_inc.tpl' );
}
?>
