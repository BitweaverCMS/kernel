<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_packages_inc.php,v 1.1.1.1.2.5 2005/10/22 14:42:20 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Process Packages form
$fPackage = &$_REQUEST['fPackage'];   // emulate register_globals

ksort($gBitSystem->mPackages);	// So packages will be listed in alphabetical order

// make a copy of mPackages - expensive, but this is low use code
$gBitSmarty->assign_by_ref('pkgArray' , $pkgArray);
if( !empty( $_REQUEST['features'] ) ) {
	$pkgArray = $gBitSystem->mPackages;
	foreach( array_keys( $pkgArray ) as $pkgKey ) {
		$pkg = $pkgArray[$pkgKey];
		if( isset( $pkg['name'] ) ) {
			$pkgName = strtolower( $pkg['name'] );
			if( isset( $_REQUEST['fPackage'][$pkgName] ) ) {
				$gBitSystem->storePreference( 'package_'.$pkgName, 'y' );
				unset( $pkgArray[$pkgKey] );
			}
		}
	}

	foreach( array_keys( $pkgArray ) as $pkgKey ) {
		$pkg = $pkgArray[$pkgKey];
		if( isset( $pkg['name'] ) ) {
			$pkgName = strtolower( $pkg['name'] );
			if( empty($pkg['required']) ) {
				$gBitSystem->storePreference( 'package_'.$pkgName, 'n' );
			}
		}
	}
}

global $gBitInstaller;
$gBitInstaller = &$gBitSystem;
$gBitSystem->verifyInstalledPackages();

// get all the services joined together for the listing
foreach( $gBitSystem->mPackages as $pkgName => $servicePkg ) {
	if( $serviceType = $gLibertySystem->getService( $pkgName ) ) {
		$serviceList[$serviceType][$pkgName] = $gBitSystem->mPackages[$pkgName];
	}
}
if (isset($serviceList)) {
	$gBitSmarty->assign( 'serviceList', $serviceList );
}

$gBitSystem->setHelpInfo('Features','Settings','Help with the features settings');
?>
