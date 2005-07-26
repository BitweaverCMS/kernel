<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_menu_options_inc.php,v 1.1.1.1.2.1 2005/07/26 15:50:08 drewslater Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH.'menu_lib.php' );

if (!$gBitUser->isAdmin()) {
	$gBitSmarty->assign('msg', tra("You dont have permission to use this feature"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["menu_id"])) {
	$gBitSmarty->assign('msg', tra("No menu indicated"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

$maxPos = $menulib->get_max_option($_REQUEST["menu_id"]);

$gBitSmarty->assign('menu_id', $_REQUEST["menu_id"]);
$menu_info = $menulib->get_menu($_REQUEST["menu_id"]);
$gBitSmarty->assign('menu_info', $menu_info);

if (!isset($_REQUEST["option_id"])) {
	$_REQUEST["option_id"] = 0;
}

$gBitSmarty->assign('option_id', $_REQUEST["option_id"]);

if ($_REQUEST["option_id"]) {
	$info = $menulib->get_menu_option($_REQUEST["option_id"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["url"] = '';
	$info["section"] = '';
	$info["perm"] = '';
	$info["groupname"] = '';
	$info["type"] = 'o';
	$info["position"] = $maxPos + 1;
}

$gBitSmarty->assign('name', $info["name"]);
$gBitSmarty->assign('url', $info["url"]);
$gBitSmarty->assign('section', $info["section"]);
$gBitSmarty->assign('perm', $info["perm"]);
$gBitSmarty->assign('groupname', $info["groupname"]);
$gBitSmarty->assign('type', $info["type"]);
$gBitSmarty->assign('position', $info["position"]);

if (isset($_REQUEST["remove"])) {
	
	$menulib->remove_menu_option($_REQUEST["remove"]);

	$maxPos = $menulib->get_max_option($_REQUEST["menu_id"]);
	$gBitSmarty->assign('position', $maxPos + 1);
}

if (isset($_REQUEST["save"])) {
	
	$menulib->replace_menu_option($_REQUEST["menu_id"], $_REQUEST["option_id"], $_REQUEST["name"], $_REQUEST["url"],
		$_REQUEST["type"], $_REQUEST["position"], $_REQUEST["section"], $_REQUEST["perm"], $_REQUEST["groupname"]);

	$gBitSmarty->assign('position', $_REQUEST["position"] + 1);
	$gBitSmarty->assign('name', '');
	$gBitSmarty->assign('option_id', 0);
	$gBitSmarty->assign('url', '');
	$gBitSmarty->assign('section', '');
	$gBitSmarty->assign('perm', '');
	$gBitSmarty->assign('groupname', '');
	$gBitSmarty->assign('type', 'o');
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'position_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign('find', $find);

$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
$allchannels = $menulib->list_menu_options($_REQUEST["menu_id"], 0, -1, $sort_mode, $find);
$channels = $menulib->list_menu_options($_REQUEST["menu_id"], $offset, $maxRecords, $sort_mode, $find, true);
$cant_pages = ceil($channels["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
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

$gBitSmarty->assign_by_ref('channels', $channels["data"]);
$gBitSmarty->assign_by_ref('allchannels', $allchannels["data"]);


?>
