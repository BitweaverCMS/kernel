<?php
// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_server_inc.php,v 1.3 2006/02/06 00:07:32 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Handle Update
$processForm = set_tab();

if( $processForm ) {
	
	$pref_toggles = array(
		"site_closed",
		"use_load_threshold",
		"use_proxy",
		"session_db"
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	$pref_simple_values = array(
		"feature_server_name",
		"sender_email",
		"proxy_host",
		"proxy_port",
		"session_lifetime",
		"load_threshold",
		"site_busy_msg",
		"site_closed_msg"
	);

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem);
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

	// Special handling for temp_dir, which has a default value
	if (isset($_REQUEST["temp_dir"])) {
		$gBitSystem->storePreference("temp_dir", $_REQUEST["temp_dir"]);

		$gBitSmarty->assign_by_ref("temp_dir", $_REQUEST["temp_dir"]);
	} else {
		$tdir = BitSystem::tempdir();

		$gBitSystem->storePreference("temp_dir", $tdir);
		$gBitSmarty->assign("temp_dir", $tdir);
	}

	// Special handling for centralissed_upload_dir, which has a default value
	$centralDir = ( isset( $_REQUEST["centralized_upload_dir"] ) ? $_REQUEST["centralized_upload_dir"] : NULL );
	$gBitSystem->storePreference( "centralized_upload_dir", $centralDir );
	$gBitSmarty->assign_by_ref( "centralized_upload_dir", $centralDir );

}


?>
