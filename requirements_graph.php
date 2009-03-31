<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/requirements_graph.php,v 1.2 2009/03/31 06:30:03 lsces Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * Setup
 */
require_once( '../bit_setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );
global $gBitInstaller;
$gBitInstaller = &$gBitSystem;
$gBitInstaller->verifyInstalledPackages();
$gBitInstaller->drawRequirementsGraph( !empty( $_REQUEST['install_version'] ), ( !empty( $_REQUEST['format'] ) ? $_REQUEST['format'] : 'png' ), ( !empty( $_REQUEST['command'] ) ? $_REQUEST['command'] : 'dot' ));
?>
