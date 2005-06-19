<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_dsn.php,v 1.1 2005/06/19 04:52:54 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH.'admin_lib.php' );

if (!$gBitUser->isAdmin()) {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["dsn_id"])) {
	$_REQUEST["dsn_id"] = 0;
}

$smarty->assign('dsn_id', $_REQUEST["dsn_id"]);

if ($_REQUEST["dsn_id"]) {
	$info = $adminlib->get_dsn($_REQUEST["dsn_id"]);
} else {
	$info = array();

	$info["dsn"] = '';
	$info['name'] = '';
}

$smarty->assign('info', $info);

if (isset($_REQUEST["remove"])) {
	
	$adminlib->remove_dsn($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	
	$adminlib->replace_dsn($_REQUEST["dsn_id"], $_REQUEST["dsn"], $_REQUEST['name']);

	$info = array();
	$info["dsn"] = '';
	$info['name'] = '';
	$smarty->assign('info', $info);
	$smarty->assign('name', '');
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'dsn_id_desc';
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
$channels = $adminlib->list_dsn($offset, $maxRecords, $sort_mode, $find);

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


// Display the template
$gBitSystem->display( 'bitpackage:kernel/admin_dsn.tpl');

?>
