<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_menu_options_inc.php,v 1.1 2005/06/19 04:52:54 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH.'menu_lib.php' );

if (!$gBitUser->isAdmin()) {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["menu_id"])) {
	$smarty->assign('msg', tra("No menu indicated"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

$maxPos = $menulib->get_max_option($_REQUEST["menu_id"]);

$smarty->assign('menu_id', $_REQUEST["menu_id"]);
$menu_info = $menulib->get_menu($_REQUEST["menu_id"]);
$smarty->assign('menu_info', $menu_info);

if (!isset($_REQUEST["option_id"])) {
	$_REQUEST["option_id"] = 0;
}

$smarty->assign('option_id', $_REQUEST["option_id"]);

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

$smarty->assign('name', $info["name"]);
$smarty->assign('url', $info["url"]);
$smarty->assign('section', $info["section"]);
$smarty->assign('perm', $info["perm"]);
$smarty->assign('groupname', $info["groupname"]);
$smarty->assign('type', $info["type"]);
$smarty->assign('position', $info["position"]);

if (isset($_REQUEST["remove"])) {
	
	$menulib->remove_menu_option($_REQUEST["remove"]);

	$maxPos = $menulib->get_max_option($_REQUEST["menu_id"]);
	$smarty->assign('position', $maxPos + 1);
}

if (isset($_REQUEST["save"])) {
	
	$menulib->replace_menu_option($_REQUEST["menu_id"], $_REQUEST["option_id"], $_REQUEST["name"], $_REQUEST["url"],
		$_REQUEST["type"], $_REQUEST["position"], $_REQUEST["section"], $_REQUEST["perm"], $_REQUEST["groupname"]);

	$smarty->assign('position', $_REQUEST["position"] + 1);
	$smarty->assign('name', '');
	$smarty->assign('option_id', 0);
	$smarty->assign('url', '');
	$smarty->assign('section', '');
	$smarty->assign('perm', '');
	$smarty->assign('groupname', '');
	$smarty->assign('type', 'o');
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

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$allchannels = $menulib->list_menu_options($_REQUEST["menu_id"], 0, -1, $sort_mode, $find);
$channels = $menulib->list_menu_options($_REQUEST["menu_id"], $offset, $maxRecords, $sort_mode, $find, true);
$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign_by_ref('allchannels', $allchannels["data"]);


?>
