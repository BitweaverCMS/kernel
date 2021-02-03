<?php
/**
 * @version $Header$
 * @package kernel
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/includes/setup_inc.php' );

if( isset( $_REQUEST['upload_id'] ) && $gBitThemes->isAjaxRequest() ) {
	echo $gBitSmarty->fetch( 'bitpackage:kernel/upload_slot_inc.tpl' );
}
?>
