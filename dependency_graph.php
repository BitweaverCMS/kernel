<?php
require_once( '../bit_setup_inc.php' );
global $gBitInstaller;
$gBitInstaller = &$gBitSystem;
$gBitInstaller->verifyInstalledPackages();
$gBitInstaller->drawDependencyGraph(( !empty( $_REQUEST['format'] ) ? $_REQUEST['format'] : 'png' ), ( !empty( $_REQUEST['command'] ) ? $_REQUEST['command'] : 'dot' ));
?>
