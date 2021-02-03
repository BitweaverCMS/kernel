<?php
/**
 * @package kernel
 * @author spider <spider@steelsun.com>
 *
 * Copyright (c) 2011 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 */


// Initialization
require_once( '../../kernel/includes/setup_inc.php' );

include_once( KERNEL_PKG_PATH.'notification_lib.php' );

// Check for admin permission
$gBitSystem->verifyPermission( 'p_admin' );

global $gSiteMapHash;

foreach( $gBitSystem->mPackages as $packageName => $package ) {
	if( file_exists( $package['path'].'sitemap.php' ) ) {
		$gSiteMapHash[$packageName]['loc'] = constant( strtoupper( $package['name'] ).'_PKG_URI' ).'sitemap.php';
		$gSiteMapHash[$packageName]['lastmod'] = date( "Y-m-d", strtotime( "midnight yesterday" ) );
	}
}

$gBitSmarty->assignByRef( 'gSiteMapHash', $gSiteMapHash );

// Display the template
$gBitSystem->display( 'bitpackage:kernel/admin_sitemaps.tpl', NULL, array( 'display_mode' => 'admin' ));

