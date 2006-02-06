<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/admin_notifications.php,v 1.3 2006/02/06 00:07:32 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH.'notification_lib.php' );

// Check for admin permission
$gBitSystem->verifyPermission( 'bit_p_admin' );

if (isset($_REQUEST["add"])) {
	
	if (isset($_REQUEST["email"]) && !empty($_REQUEST["email"]))
		$notificationlib->add_mail_event($_REQUEST["event"], '*', $_REQUEST["email"]);
}

if (isset($_REQUEST["removeevent"])) {
	
	$notificationlib->remove_mail_event($_REQUEST["removeevent"], $_REQUEST["object"], $_REQUEST["email"]);
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'event_asc';
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
$channels = $notificationlib->list_mail_events($offset, $max_records, $sort_mode, $find);

$cant_pages = ceil($channels["cant"] / $max_records);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $max_records));

if ($channels["cant"] > ($offset + $max_records)) {
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

$gBitSmarty->assign_by_ref('channels', $channels["data"]);

$gBitSmarty->assign_by_ref('events', $gBitSystem->mNotifyEvents);
$admin_mail=$admin_mail=$gBitUser->mInfo['email'];
$cuser_mail=$gBitUser->mInfo['email'];
$gBitSmarty->assign('admin_mail', $admin_mail);
$gBitSmarty->assign('cuser_mail', $cuser_mail);

// Display the template
$gBitSystem->display( 'bitpackage:kernel/admin_notifications.tpl');

?>
