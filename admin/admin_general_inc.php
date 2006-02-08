<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_general_inc.php,v 1.6 2006/02/08 21:51:14 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$formGeneralMisc = array(
	'direct_pagination' => array(
		'label' => 'Use direct pagination links',
	),
	'cachepages' => array(
		'label' => 'Use cache for external pages',
	),
	'cacheimages' => array(
		'label' => 'Use cache for external images',
	),
	'feature_obzip' => array(
		'label' => 'Use gzipped output',
	),
	'count_admin_pvs' => array(
		'label' => 'Count admin pageviews',
	),
);
$gBitSmarty->assign( 'formGeneralMisc',$formGeneralMisc );

// Handle Update
$processForm = set_tab();

if ($processForm) {
	$pref_toggles = array(
		"cacheimages",
		"cachepages",
		"count_admin_pvs",
		"direct_pagination",
		"feature_obzip",
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle, KERNEL_PKG_NAME);
	}

	$pref_simple_values = array(
		"site_menu_title",
		"max_records",
		"url_index",
	);

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem, KERNEL_PKG_NAME);
	}

	// Special handling for tied fields: bit_index and url_index
	if (!empty($_REQUEST["url_index"]) && $_REQUEST["bit_index"] == 'custom_home') {
		$_REQUEST["bit_index"] = $_REQUEST["url_index"];
	}

	$pref_byref_values = array(
		"long_date_format",
		"long_time_format",
		"short_date_format",
		"short_time_format",
		"bit_index"
	);

	foreach ($pref_byref_values as $britem) {
		byref_set_value ($britem);
	}
}

?>
