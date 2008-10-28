<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/admin/upgrades/2.0.0.php,v 1.1 2008/10/28 21:15:00 squareing Exp $
 */
global $gBitInstaller;

$infoHash = array(
	'package'      => KERNEL_PKG_NAME,
	'version'      => str_replace( '.php', '', basename( __FILE__ )),
	'description'  => "Set core package version number.",
);

$gBitInstaller->registerPackageUpgrade( $infoHash );
?>
