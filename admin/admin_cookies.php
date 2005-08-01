<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_cookies.php,v 1.2 2005/08/01 18:40:33 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH.'tagline_lib.php' );

if (!$gBitUser->hasPermission( 'bit_p_edit_cookies' )) {
	$gBitSmarty->assign('msg', tra("You dont have permission to use this feature"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["cookie_id"])) {
	$_REQUEST["cookie_id"] = 0;
}

$gBitSmarty->assign('cookie_id', $_REQUEST["cookie_id"]);

if ($_REQUEST["cookie_id"]) {
	$info = $taglinelib->get_cookie($_REQUEST["cookie_id"]);
} else {
	$info = array();

	$info["cookie"] = '';
}

$gBitSmarty->assign('cookie', $info["cookie"]);

if (isset($_REQUEST["remove"])) {
	
	$taglinelib->remove_cookie($_REQUEST["remove"]);
}

if (isset($_REQUEST["removeall"])) {
	
	$taglinelib->remove_all_cookies();
}

if (isset($_REQUEST["upload"])) {
	
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "r");

		while (!feof($fp)) {
			$data = fgets($fp, 255);

			if (!empty($data)) {
				$data = str_replace("\n", "", $data);

				$taglinelib->replace_cookie(0, $data);
			}
		}

		fclose ($fp);
		$size = $_FILES['userfile1']['size'];
		$name = $_FILES['userfile1']['name'];
		$type = $_FILES['userfile1']['type'];
	} else {
		$gBitSmarty->assign('msg', tra("Upload failed"));

		$gBitSystem->display( 'error.tpl' );
		die;
	}
}

if (isset($_REQUEST["save"])) {
	
	$taglinelib->replace_cookie($_REQUEST["cookie_id"], $_REQUEST["cookie"]);

	$gBitSmarty->assign("cookie_id", '0');
	$gBitSmarty->assign('cookie', '');
}

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'cookie_id_desc';
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
$channels = $taglinelib->list_cookies($offset, $maxRecords, $sort_mode, $find);

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


// Display the template
$gBitSystem->display( 'bitpackage:kernel/admin_cookies.tpl');

?>
