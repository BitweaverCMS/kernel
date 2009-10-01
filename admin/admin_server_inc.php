<?php
// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_server_inc.php,v 1.18 2009/10/01 14:17:01 wjames5 Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Handle Update
$processForm = set_tab();

if( $processForm ) {

	$pref_toggles = array(
		"site_closed",
		"site_use_load_threshold",
		"site_use_proxy",
		"site_store_session_db"
	);

	foreach( $pref_toggles as $item ) {
		simple_set_toggle( $item, KERNEL_PKG_NAME );
	}

	$pref_simple_values = array(
		"kernel_server_name",
		"site_sender_email",
		"site_proxy_host",
		"site_proxy_port",
		"site_session_lifetime",
		"site_load_threshold",
		"site_busy_msg",
		"site_closed_msg",
		"storage_host"
	);

	foreach( $pref_simple_values as $item ) {
		simple_set_value( $item, KERNEL_PKG_NAME );
	}

	$pref_byref_values = array(
		"site_title",
		"site_slogan",
		"site_description",
		"site_notice",
		"site_error_title",
	);

	foreach( $pref_byref_values as $item ) {
		$_REQUEST['site_description'] = substr( $_REQUEST['site_description'], 0, 180 );
		byref_set_value( $item );
	}

	if( !empty( $_REQUEST['site_keywords'] )) {
		$_REQUEST['site_keywords'] = substr( $_REQUEST['site_keywords'], 0, 900 );
		$keywords = str_split( $_REQUEST['site_keywords'], 250 );

		// we need to make sure we remove all settings for site_keywords first in case the new value is considerably shorter than the previous one
		$gBitSystem->storeConfig( 'site_keywords_1', NULL );
		$gBitSystem->storeConfig( 'site_keywords_2', NULL );
		$gBitSystem->storeConfig( 'site_keywords_3', NULL );

		foreach( $keywords as $key => $chunk ) {
			$gBitSystem->storeConfig( "site_keywords".( !empty( $key ) ? '_'.$key : '' ), $chunk, KERNEL_PKG_NAME );
		}

		// join keywords back together
		$gBitSystem->setConfig( 'site_keywords',
			$gBitSystem->getConfig( 'site_keywords' ).
			$gBitSystem->getConfig( 'site_keywords_1' ).
			$gBitSystem->getConfig( 'site_keywords_2' ).
			$gBitSystem->getConfig( 'site_keywords_3' )
		);
	}

	// Special handling for site_temp_dir, which has a default value
	if( isset( $_REQUEST["site_temp_dir"] ) && $_REQUEST["site_temp_dir"] != TEMP_PKG_PATH ) {
		$gBitSystem->storeConfig( "site_temp_dir", $_REQUEST["site_temp_dir"], KERNEL_PKG_NAME );
		$gBitSmarty->assign_by_ref( "site_temp_dir", $_REQUEST["site_temp_dir"] );
	}

	// Special handling for centralissed_upload_dir, which has a default value
	$centralDir = ( !empty( $_REQUEST["site_upload_dir"] ) ? $_REQUEST["site_upload_dir"] : NULL );
	$gBitSystem->storeConfig( "site_upload_dir", $centralDir , KERNEL_PKG_NAME );
	$gBitSmarty->assign_by_ref( "site_upload_dir", $centralDir );
}
?>
