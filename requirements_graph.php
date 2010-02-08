<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/requirements_graph.php,v 1.3 2010/02/08 21:27:23 wjames5 Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * Setup
 */
require_once( '../kernel/setup_inc.php' );
$gBitSystem->verifyPermission( 'p_admin' );
global $gBitInstaller;
$gBitInstaller = &$gBitSystem;
$gBitInstaller->verifyInstalledPackages();
$gBitInstaller->drawRequirementsGraph( !empty( $_REQUEST['install_version'] ), ( !empty( $_REQUEST['format'] ) ? $_REQUEST['format'] : 'png' ), ( !empty( $_REQUEST['command'] ) ? $_REQUEST['command'] : 'dot' ));
?>
