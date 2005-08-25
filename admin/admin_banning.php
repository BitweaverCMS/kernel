<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_banning.php,v 1.1.1.1.2.2 2005/08/25 21:21:21 lsces Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../../bit_setup_inc.php' );

include_once (KERNEL_PKG_PATH.'ban_lib.php');

if ($gBitSystem->getPreference('feature_banning') != 'y') {
	$gBitSmarty->assign('msg', tra("This feature is disabled").": feature_banning");

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!$gBitUser->hasPermission( 'bit_p_admin_banning' )) {
	$gBitSmarty->assign('msg', tra("Permission denied"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (isset($_REQUEST['ban_id'])) {
	$info = $banlib->get_rule($_REQUEST['ban_id']);
} else {
	$_REQUEST['ban_id'] = 0;

	$info['sections'] = array();
	$info['title'] = '';
	$info['mode'] = 'user';
	$info['ip1'] = 255;
	$info['ip2'] = 255;
	$info['ip3'] = 255;
	$info['ip4'] = 255;
	$info['use_dates'] = 'n';
	$info['date_from'] = $gBitSystem->getUTCTime();
	$info['date_to'] = $info['date_from'] + 7 * 24 * 3600;
	$info['message'] = '';
}

$gBitSmarty->assign('ban_id', $_REQUEST['ban_id']);
$gBitSmarty->assign_by_ref('info', $info);

if (isset($_REQUEST['remove'])) {
	
	$banlib->remove_rule($_REQUEST['remove']);
}

if (isset($_REQUEST['del']) && isset($_REQUEST['delsec'])) {
	
	foreach (array_keys($_REQUEST['delsec'])as $sec) {
		$banlib->remove_rule($sec);
	}
}

if (isset($_REQUEST['save'])) {
	
	$_REQUEST['use_dates'] = isset($_REQUEST['use_dates']) ? 'y' : 'n';

	$_REQUEST['date_from'] = mktime(0, 0, 0, $_REQUEST['date_fromMonth'], $_REQUEST['date_fromDay'], $_REQUEST['date_fromYear']);
	$_REQUEST['date_to'] = mktime(0, 0, 0, $_REQUEST['date_toMonth'], $_REQUEST['date_toDay'], $_REQUEST['date_toYear']);
	$sections = array_keys($_REQUEST['section']);
	$banlib->replace_rule($_REQUEST['ban_id'], $_REQUEST['mode'], $_REQUEST['title'], $_REQUEST['ip1'], $_REQUEST['ip2'],
		$_REQUEST['ip3'], $_REQUEST['ip4'], $_REQUEST['user'], $_REQUEST['date_from'], $_REQUEST['date_to'], $_REQUEST['use_dates'],
		$_REQUEST['message'], $sections);

	$info['sections'] = array();
	$info['title'] = '';
	$info['mode'] = 'user';
	$info['ip1'] = 255;
	$info['ip2'] = 255;
	$info['ip3'] = 255;
	$info['ip4'] = 255;
	$info['use_dates'] = 'n';
	$info['date_from'] = $gBitSystem->getUTCTime();
	$info['date_to'] = $info['date_from'] + 7 * 24 * 3600;
	$info['message'] = '';
	$gBitSmarty->assign_by_ref('info', $info);
}

$where = '';
$wheres = array();
/*
if(isset($_REQUEST['filter'])) {
  if($_REQUEST['filter_name']) {
   $wheres[]=" name='".$_REQUEST['filter_name']."'";
  }
  if($_REQUEST['filter_active']) {
   $wheres[]=" is_active='".$_REQUEST['filter_active']."'";
  }
  $where = implode('and',$wheres);
}
*/
if (isset($_REQUEST['where'])) {
	$where = $_REQUEST['where'];
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'created_desc';
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
$gBitSmarty->assign('where', $where);
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
$items = $banlib->list_rules($offset, $maxRecords, $sort_mode, $find, $where);
$gBitSmarty->assign('cant', $items['cant']);

$cant_pages = ceil($items["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$gBitSmarty->assign_by_ref('items', $items["data"]);

$sections = array(
	'wiki',
	'galleries',
	'file_galleries',
	'cms',
	'blogs',
	'forums',
	'chat',
	'categories',
	'games',
	'faqs',
	'html_pages',
	'quizzes',
	'surveys',
	'webmail',
	'trackers',
	'featured_links',
	'directory',
	'user_messages',
	'newsreader',
	'mybitweaver',
	'workflow',
	'charts'
);

$gBitSmarty->assign('sections', $sections);


$gBitSystem->display( 'bitpackage:kernel/admin_banning.tpl');

?>
