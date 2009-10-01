<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_packages_inc.php,v 1.16 2009/10/01 13:45:42 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

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
$gBitInstaller->verifyInstalledPackages();
$gBitSmarty->assign( 'requirements', $gBitInstaller->calculateRequirements( TRUE ) );
$gBitSmarty->assign( 'requirementsMap', $gBitInstaller->drawRequirementsGraph( TRUE, 'cmapx' ));

$upgradable = array();
foreach( $gBitSystem->mPackages as $name => &$pkg ) {
	if( $gBitSystem->isPackageInstalled( $name ) && !empty( $pkg['info']['upgrade'] )) {
		// If no tables then just do a quiet 'auto-upgrade' of version number
		if( !isset( $pkg['tables'] ) || empty( $pkg['tables'] ) ) {
			$gBitSystem->storeVersion( $name, $pkg['info']['upgrade'] );
			$gBitSystem->registerPackageVersion( $name, $pkg['info']['upgrade'] );
			$pkg['info']['version'] = $pkg['info']['upgrade'];
			unset( $pkg['info']['upgrade'] );
		} else { // add to a list of displayed packages that need upgrading
			// only display relevant information to keep things tight.
			$upgradable[$name]['info']['version'] = $pkg['info']['version'];
			$upgradable[$name]['info']['upgrade'] = $pkg['info']['upgrade'];
		}
	}
}
$gBitSmarty->assign( 'upgradable', $upgradable );

// So packages will be listed in alphabetical order
ksort( $gBitSystem->mPackages );
?>
