<?php
require_once( '../bit_setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );
global $gBitInstaller;
$gBitInstaller = &$gBitSystem;
$gBitInstaller->verifyInstalledPackages();
$gBitInstaller->drawDependencyGraph( !empty( $_REQUEST['install_version'] ), ( !empty( $_REQUEST['format'] ) ? $_REQUEST['format'] : 'png' ), ( !empty( $_REQUEST['command'] ) ? $_REQUEST['command'] : 'dot' ));
?>
