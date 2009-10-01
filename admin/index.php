<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/index.php,v 1.25 2009/10/01 13:45:43 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Initialization
global $gForceAdodb;
$gForceAdodb = TRUE;
require_once( '../../bit_setup_inc.php' );
require_once( KERNEL_PKG_PATH.'simple_form_functions_lib.php' );

//make an alias in case anyone decides to verifyInstalledPackages
$gBitInstaller = &$gBitSystem;

if( !empty( $_REQUEST["page"] )) {
	$page = $_REQUEST["page"];

	// only admins may use this page
	$gBitSystem->verifyPermission( 'p_'.$page.'_admin' );

	if( preg_match( '/\.php/', $page )) {
		$adminPage = $page;
	} else {
		$file = $page; // Default file name
		switch( $page ) {
			// handle a few special cases for page requests
			case 'features':
			case 'packages':
			case 'general':
			case 'server':
				$package = 'kernel';
				break;
			case 'layout':
			case 'layout_overview':
			case 'columns':
			case 'modules':
			case 'custom_modules':
				$package = 'themes';
				break;
			case 'menus':
			case 'menu_options':
				$package = 'tidbits';
				break;
			case 'login':
			case 'userfiles':
				$package = 'users';
				break;
			default:
				$package = $page;
				break;
		}

		$adminPage = constant( strtoupper( $package ).'_PKG_PATH' ).'/admin/admin_'.$file.'_inc.php';
		// gBitThemes->loadLayout uses this to determine the currently active package
		$gBitSystem->mActivePackage = $package;
	}
	$gBitSmarty->assign( 'package', $package );
	$gBitSmarty->assign( 'file', $file );
	$gBitSmarty->assign( 'page', $page );
	$gBitSystem->setBrowserTitle( preg_replace( '/_/', ' ', $page )." Settings" );

	include_once ( $adminPage );

	// Spiderr - a bit hackish, but need to force preferences refresh
	$gBitSystem->loadConfig();
} else {
	$adminTemplates = array();
	// deal with package sorting for a unified layout
	$packages = array_keys( $gBitSystem->mPackages );
	asort( $packages );
	$packages = array_unique( array_merge( array( 'kernel', 'liberty', 'users', 'themes' ), $packages ));
	foreach( $packages as $package ) {
		if( $gBitUser->hasPermission( 'p_'.$package.'_admin' ) ) {
			$lowerPackage = strtolower( $package );
			$tpl = "bitpackage:$lowerPackage/menu_{$lowerPackage}_admin.tpl";
			if(( $gBitSystem->isPackageActive( $package ) || $lowerPackage == 'kernel' ) && @$gBitSmarty->template_exists( $tpl )) {
				$adminTemplates[$package] = $tpl;
			}
		}
	}

	if( !empty( $adminTemplates ) ) {
		$gBitSystem->setBrowserTitle( 'Administration' );
		$gBitSmarty->assign_by_ref( 'adminTemplates', $adminTemplates );
	} else {
		$gBitSystem->verifyPermission( 'p_admin' );
	}
}


if( !empty( $_REQUEST['version_check'] ) && $gBitUser->isAdmin() ) {
	$gBitSmarty->assign( 'version_info', $gBitSystem->checkBitVersion() );
}

// Display the template
$gBitSystem->display( 'bitpackage:kernel/admin.tpl' , NULL, array( 'display_mode' => 'admin' ));
?>
