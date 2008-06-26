<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/index.php,v 1.21 2008/06/26 09:56:44 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
global $gForceAdodb;
$gForceAdodb = TRUE;
require_once( '../../bit_setup_inc.php' );
require_once( KERNEL_PKG_PATH.'simple_form_functions_lib.php' );

// only admins may use this page
$gBitSystem->verifyPermission( 'p_admin' );

//make an alias in case anyone decides to verifyInstalledPackages
$gBitInstaller = &$gBitSystem;

if( !empty( $_REQUEST["page"] )) {
	$page = $_REQUEST["page"];

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

		$adminPage =  constant( strtoupper( $package ).'_PKG_PATH' ).'/admin/admin_'.$file.'_inc.php';
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
		$lowerPackage = strtolower( $package );
		$tpl = "bitpackage:$lowerPackage/menu_{$lowerPackage}_admin.tpl";
		if(( $gBitSystem->isPackageActive( $package ) || $lowerPackage == 'kernel' ) && @$gBitSmarty->template_exists( $tpl )) {
			$adminTemplates[$package] = $tpl;
		}
	}

	$gBitSystem->setBrowserTitle( 'Administration' );
	$gBitSmarty->assign_by_ref( 'adminTemplates', $adminTemplates );
}

if( !empty( $_REQUEST['version_check'] ) ) {
	$gBitSmarty->assign( 'version_info', $gBitSystem->checkBitVersion() );
}

// Display the template
$gBitSystem->display( 'bitpackage:kernel/admin.tpl' , NULL, array( 'display_mode' => 'admin' ));
?>
