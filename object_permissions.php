<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/object_permissions.php,v 1.2 2005/06/28 07:45:45 spiderr Exp $
 * @package kernel
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
/**
 * required setup
 */
include_once( '../bit_setup_inc.php' );

if (!$gBitUser->isAdmin()) {
	$smarty->assign('msg', tra("Permission denied you cannot assign permissions for this page"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["referer"])) {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$_REQUEST["referer"] = $_SERVER['HTTP_REFERER'];
	}
}

if (isset($_REQUEST["referer"])) {
	$smarty->assign('referer', $_REQUEST["referer"]);
}

if (!isset(
	$_REQUEST["objectName"]) || !isset($_REQUEST["object_type"]) || !isset($_REQUEST["object_id"]) || !isset($_REQUEST["permType"])) {
	$smarty->assign('msg', tra("Not enough information to display this page"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if ($_REQUEST["object_id"] < 1) {
	$smarty->assign('msg', tra("Fatal error"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

$_REQUEST["object_id"] = urldecode($_REQUEST["object_id"]);
$_REQUEST["object_type"] = urldecode($_REQUEST["object_type"]);
$_REQUEST["permType"] = urldecode($_REQUEST["permType"]);

$smarty->assign('objectName', $_REQUEST["objectName"]);
$smarty->assign('object_id', $_REQUEST["object_id"]);
$smarty->assign('object_type', $_REQUEST["object_type"]);
$smarty->assign('permType', $_REQUEST["permType"]);

// Process the form to assign a new permission to this page
if (isset($_REQUEST["assign"])) {
	
	$gBitUser->assign_object_permission($_REQUEST["group"], $_REQUEST["object_id"], $_REQUEST["object_type"], $_REQUEST["perm"]);
}

// Process the form to remove a permission from the page
if (isset($_REQUEST["action"])) {
	
	if ($_REQUEST["action"] == 'remove') {
		$gBitUser->remove_object_permission($_REQUEST["group"], $_REQUEST["object_id"], $_REQUEST["object_type"], $_REQUEST["perm"]);
	}
}

// Now we have to get the individual page permissions if any
$page_perms = $gBitUser->get_object_permissions($_REQUEST["object_id"], $_REQUEST["object_type"]);
$smarty->assign_by_ref('page_perms', $page_perms);

// Get a list of groups
$groups = $gBitUser->getAllUserGroups();
$smarty->assign_by_ref('groups', $groups["data"]);

// Get a list of permissions
$smarty->assign_by_ref('perms', array_keys( $gBitUser->mPerms ) );



$gBitSystem->display( 'bitpackage:kernel/object_permissions.tpl');

?>
