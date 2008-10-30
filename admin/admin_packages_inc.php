<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_packages_inc.php,v 1.11 2008/10/30 23:02:12 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Process Packages form
$fPackage = &$_REQUEST['fPackage'];   // emulate register_globals

# rescan to include all packages, installed and not installed
$gBitSystem->scanPackages(
	'bit_setup_inc.php', TRUE, 'all', TRUE, TRUE
);

// make a copy of mPackages - expensive, but this is low use code

if( !empty( $_REQUEST['features'] ) ) {
	$pkgArray = $gBitSystem->mPackages;
	foreach( array_keys( $pkgArray ) as $pkgKey ) {
		$pkg = $pkgArray[$pkgKey];
		if( !empty( $pkg['name'] )) {
			$pkgName = strtolower( $pkg['name'] );
			// can only change already installed packages that are not required
			if( $gBitSystem->isPackageInstalled( $pkgName ) && empty( $pkg['required'] )) {
				if( isset( $_REQUEST['fPackage'][$pkgName] )) {
					// mark installed and active
					$gBitSystem->storeConfig( 'package_'.$pkgName, 'y', $pkgName );
					unset( $pkgArray[$pkgKey] );
				} else {
					// mark installed but not active
					$gBitSystem->storeConfig( 'package_'.$pkgName, 'i', $pkgName );
					unset( $pkgArray[$pkgKey] );
				}
			}
		}
	}
}

global $gBitInstaller;
$gBitInstaller = &$gBitSystem;
$gBitSystem->verifyInstalledPackages();
$gBitSmarty->assign( 'dependencies', $gBitInstaller->calculateDependencies( TRUE ) );
$gBitSmarty->assign( 'dependencymap', $gBitSystem->drawDependencyGraph( TRUE, 'cmapx' ));

// So packages will be listed in alphabetical order
ksort( $gBitSystem->mPackages );
?>
