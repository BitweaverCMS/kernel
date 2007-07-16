<?php
require_once( '../bit_setup_inc.php' );

if( isset( $_REQUEST['upload_id'] ) && $gBitThemes->isAjaxRequest() ) {
	echo $gBitSmarty->fetch( 'bitpackage:kernel/upload_slot_inc.tpl' );
}
?>
