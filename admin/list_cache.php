<?php

// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Initialization
require_once( '../../kernel/setup_inc.php' );

$gBitSystem->verifyPermission( 'p_admin' );

if (isset($_REQUEST["remove"])) {
	$gBitSystem->remove_cache($_REQUEST["remove"]);
}

if (isset($_REQUEST["refresh"])) {
	$gBitSystem->refresh_cache($_REQUEST["refresh"]);
}

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'url_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the max_records php variable to set the limit
// if sortMode is not set then use last_modified_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$gBitSmarty->assign_by_ref('offset', $offset);

if (!isset($_REQUEST["find"])) {
	$find = '';
} else {
	$find = $_REQUEST["find"];
}

$gBitSmarty->assign('find', $find);

// Get a list of last changes to the Wiki database
$listpages = $gBitSystem->list_cache($offset, $max_records, $sort_mode, $find);

// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $max_records);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $max_records));

if ($listpages["cant"] > ($offset + $max_records)) {
	$gBitSmarty->assign('next_offset', $offset + $max_records);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $max_records);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);


// Display the template
$gBitSystem->display( 'bitpackage:kernel/list_cache.tpl', NULL, array( 'display_mode' => 'list' ));

?>
