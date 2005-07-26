<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/list_cache.php,v 1.1.1.1.2.1 2005/07/26 15:50:08 drewslater Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

if (!$gBitUser->isAdmin()) {
	$gBitSmarty->assign('msg', tra("You dont have permission to use this feature"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

/*
if($feature_listPages != 'y') {
  $gBitSmarty->assign('msg',tra("This feature is disabled"));
  $gBitSystem->display( 'error.tpl' );
  die;  
}
*/
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
// use the maxRecords php variable to set the limit
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
$listpages = $gBitSystem->list_cache($offset, $maxRecords, $sort_mode, $find);

// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($listpages["cant"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);


// Display the template
$gBitSystem->display( 'bitpackage:kernel/list_cache.tpl');

?>
