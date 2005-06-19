<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_menus_inc.php,v 1.1 2005/06/19 04:52:54 bitweaver Exp $

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
	$_REQUEST["menu_id"] = 0;
}

$smarty->assign('menu_id', $_REQUEST["menu_id"]);

if ($_REQUEST["menu_id"]) {
	$info = $menulib->get_menu($_REQUEST["menu_id"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["description"] = '';
	$info["type"] = 'd';
}

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('type', $info["type"]);

if (isset($_REQUEST["remove"])) {
	
	$menulib->remove_menu($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	
	$menulib->replace_menu($_REQUEST["menu_id"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["type"]);

	$smarty->assign('name', '');
	$smarty->assign('description', '');
	$smarty->assign('type', '');
	$_REQUEST["menu_id"] = 0;
	$smarty->assign('menu_id', 0);
}

$formMenuFeatures = array(
	'feature_menusfolderstyle' => array(
		'label' => 'Display menus as folders',
		'note' => 'Show a folder icon in front of collapsable menus, to indicate that they can be opended and shut.',
	),
);
$smarty->assign( 'formMenuFeatures',$formMenuFeatures );
// process form
if (isset($_REQUEST["menu_features"])) {
	
	foreach( $formMenuFeatures as $item => $data ) {
		simple_set_toggle( $item );
	}
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'name_desc';
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
$channels = $menulib->list_menus($offset, $maxRecords, $sort_mode, $find);

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



?>
