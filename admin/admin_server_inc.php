<?php
// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_server_inc.php,v 1.7 2006/04/19 13:48:37 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Handle Update
$processForm = set_tab();

if( $processForm ) {
	
	$pref_toggles = array(
		"site_closed",
		"site_use_load_threshold",
		"site_use_proxy",
		"site_store_session_db"
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle, KERNEL_PKG_NAME);
	}

	$pref_simple_values = array(
		"kernel_server_name",
		"site_sender_email",
		"site_proxy_host",
		"site_proxy_port",
		"site_session_lifetime",
		"site_load_threshold",
		"site_busy_msg",
		"site_closed_msg"
	);

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem, KERNEL_PKG_NAME);
	}

	$pref_byref_values = array(
		"site_title",
		"site_slogan",
		"site_description",
		"site_keywords",
	);

	foreach ($pref_byref_values as $britem) {
		byref_set_value ($britem);
	}

	// Special handling for site_temp_dir, which has a default value
	if (isset($_REQUEST["site_temp_dir"])) {
		$gBitSystem->storeConfig("site_temp_dir", $_REQUEST["site_temp_dir"], KERNEL_PKG_NAME );

		$gBitSmarty->assign_by_ref("site_temp_dir", $_REQUEST["site_temp_dir"]);
	} else {
		$tdir = BitSystem::tempdir();

		$gBitSystem->storeConfig("site_temp_dir", $tdir, KERNEL_PKG_NAME );
		$gBitSmarty->assign("site_temp_dir", $tdir);
	}

	// Special handling for centralissed_upload_dir, which has a default value
	$centralDir = ( isset( $_REQUEST["site_upload_dir"] ) ? $_REQUEST["site_upload_dir"] : NULL );
	$gBitSystem->storeConfig( "site_upload_dir", $centralDir , KERNEL_PKG_NAME );
	$gBitSmarty->assign_by_ref( "site_upload_dir", $centralDir );

}


?>
