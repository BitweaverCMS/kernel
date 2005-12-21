<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/index.php,v 1.3.2.2 2005/12/21 20:09:08 mej Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
require_once( KERNEL_PKG_PATH.'admin_lib.php' );

if( isset( $_REQUEST["page"] ) ) {
	$page = $_REQUEST["page"];
	$pageName = preg_replace('/_/', ' ', $page);
	$gBitSystem->setBrowserTitle( "$pageName Settings" );
} else {
	$gBitSystem->setBrowserTitle( 'Administration' );
}

$gBitSystem->verifyPermission( 'bit_p_admin' );

require_once( KERNEL_PKG_PATH.'simple_form_functions_lib.php' );

$gBitInstaller = &$gBitSystem;
$gBitSystem->verifyInstalledPackages();
//vd($gBitSystem->mPackages);
$home_blog = $gBitSystem->getPreference("home_blog", 0);
$gBitSmarty->assign('home_blog', $home_blog);

$home_forum = $gBitSystem->getPreference("home_forum", 0);
$gBitSmarty->assign('home_forum', $home_forum);

$home_gallery = $gBitSystem->getPreference("home_gallery", 0);
$gBitSmarty->assign('home_gallery', $home_gallery);
if( isset( $page ) ) {
	$gBitSmarty->assign('page', $page);
}
if( isset( $pageName ) ) {
	$gBitSmarty->assign( 'pageName', $pageName );
}

$home_file_gallery = $gBitSystem->getPreference("home_file_gallery", 0);
$gBitSmarty->assign('home_file_gallery', $home_file_gallery);

if (isset($page)) {
	if( preg_match('/\.php/', $page ) ) {
		$adminPage = $page;
	} else {
		$file = $page; // Default file name
		switch( $page ) {
			// handle a few special cases for page requests
			case 'features':
			case 'packages':
			case 'general':
			case 'server':
			case 'layout':
			case 'modules':
			case 'menus':
			case 'menu_options':
			case 'custom_modules':
				$package = 'kernel';
				break;
			case 'login':
			case 'userfiles':
				$package = 'users';
				break;
			default:
				$package =$page;
				break;
		}

		$adminPage =  constant( strtoupper( $package ).'_PKG_PATH' ).'/admin/admin_'.$file.'_inc.php';
	}
	$gBitSmarty->assign('package', $package );
	$gBitSmarty->assign('file', $file );
	include_once ( $adminPage );
	// Spiderr - a bit hackish, but need to force preferences refresh
	$gBitSystem->loadPreferences();
} else {
//vd( $gBitSystem->mPackages );
	$adminTemplates = array();
	foreach( array_keys( $gBitSystem->mPackages ) as $package ) {
		$lowerPackage = strtolower( $package );
		$upperPackage = strtoupper( $package );
		$tpl = "bitpackage:$lowerPackage/menu_".$lowerPackage."_admin.tpl";
		if( ($gBitSystem->isPackageActive( $package ) || $lowerPackage == 'kernel') && @$gBitSmarty->template_exists( $tpl ) ) {
			$adminTemplates[$package] = $tpl;
		}
	}
	$gBitSmarty->assign_by_ref( 'kernelTemplate', $adminTemplates["kernel"] );
	$gBitSmarty->assign_by_ref( 'adminTemplates', $adminTemplates );
}

$admin_panels = $gBitSystem->mAppMenu;
$gBitSmarty->assign('admin_panels', $admin_panels);

if( !empty( $_REQUEST['version_check'] ) ) {
	$gBitSmarty->assign( 'version_info', $gBitSystem->checkBitVersion() );
}

// Display the template
$gBitSystem->display( 'bitpackage:kernel/admin.tpl' );

?>
