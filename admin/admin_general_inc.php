<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_general_inc.php,v 1.1.1.1.2.2 2005/08/14 01:38:09 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once( THEMES_PKG_PATH.'theme_control_lib.php' );

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
	'feature_menusfolderstyle' => array(
		'label' => 'Display menus as folders',
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
		"feature_menusfolderstyle",
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	$pref_simple_values = array(
		"maxRecords",
		"urlIndex",
	);

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem);
	}

	// Special handling for tied fields: bitIndex and urlIndex
	if (!empty($_REQUEST["urlIndex"]) && $_REQUEST["bitIndex"] == 'custom_home') {
		$_REQUEST["bitIndex"] = $_REQUEST["urlIndex"];
	}

	$pref_byref_values = array(
		"long_date_format",
		"long_time_format",
		"short_date_format",
		"short_time_format",
		"bitIndex"
	);

	foreach ($pref_byref_values as $britem) {
		byref_set_value ($britem);
	}
}

?>
