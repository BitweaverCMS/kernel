<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_custom_modules_inc.php,v 1.1.1.1.2.1 2005/07/26 15:50:08 drewslater Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../../bit_setup_inc.php' );

include_once( KERNEL_PKG_PATH.'menu_lib.php' );
include_once( KERNEL_PKG_PATH.'mod_lib.php' );

if( $gBitSystem->isPackageActive( 'dcs' ) ) {
	include_once( DCS_PKG_PATH.'dcs_lib.php' );
	if (!isset($dcslib)) {
		$dcslib = new DCSLib();
	}
	$contents = $dcslib->list_content(0, -1, 'content_id_desc', '');
	$gBitSmarty->assign('contents', $contents["data"]);
}
if( $gBitSystem->isPackageActive( 'banners' ) ) {
	include_once( BANNERS_PKG_PATH.'banner_lib.php' );
	if (!isset($bannerlib)) {
		$bannerlib = new BannerLib();
	}
	$banners = $bannerlib->list_zones();
	$gBitSmarty->assign('banners', $banners["data"]);
}
if( $gBitSystem->isPackageActive( 'rss' ) ) {
	include_once( RSS_PKG_PATH.'rss_lib.php' );
	if (!isset($rsslib)) {
		$rsslib = new RssLib();
	}
	$rsss = $rsslib->list_rss_modules(0, -1, 'name_desc', '');
	$gBitSmarty->assign('rsss', $rsss["data"]);

}
if( $gBitSystem->isPackageActive( 'polls' ) ) {
	include_once( POLLS_PKG_PATH.'poll_lib.php' );
	if (!isset($polllib)) {
		$polllib = new PollLib();
	}
	$polls = $polllib->list_active_polls(0, -1, 'publish_date_desc', '');
	$gBitSmarty->assign('polls', $polls["data"]);
}
if( $gBitSystem->isPackageActive( 'imagegals' ) ) {
	$galleries = $gBitSystem->list_galleries(0, -1, 'last_modified_desc', $user, '');
	$gBitSmarty->assign('galleries', $galleries["data"]);
}
$menus = $menulib->list_menus(0, -1, 'menu_id_desc', '');
$gBitSmarty->assign('menus', $menus["data"]);

$gBitSmarty->assign('wysiwyg', 'n');

if (!$gBitUser->isAdmin()) {
	$gBitSmarty->assign('msg', tra("You dont have permission to use this feature"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

// Values for the user_module edit/create form
$gBitSmarty->assign('um_name', '');
$gBitSmarty->assign('um_title', '');
$gBitSmarty->assign('um_data', '');

$gBitSmarty->assign('assign_name', '');
//$gBitSmarty->assign('assign_title','');
$gBitSmarty->assign('assign_position', '');
$gBitSmarty->assign('assign_order', '');
$gBitSmarty->assign('assign_cache', 0);
$gBitSmarty->assign('assign_rows', 10);
$gBitSmarty->assign('assign_params', '');

if (isset($_REQUEST["um_remove"])) {
	
	$_REQUEST["um_remove"] = urldecode($_REQUEST["um_remove"]);

	$modlib->remove_user_module($_REQUEST["um_remove"]);
} elseif (isset($_REQUEST["um_edit"])) {
	
	$_REQUEST["um_edit"] = urldecode($_REQUEST["um_edit"]);

	$um_info = $modlib->get_user_module($_REQUEST["um_edit"]);
	$gBitSmarty->assign_by_ref('um_name', $um_info["name"]);
	$gBitSmarty->assign_by_ref('um_title', $um_info["title"]);
	$gBitSmarty->assign_by_ref('um_data', $um_info["data"]);
} elseif (isset($_REQUEST["um_update"])) {
    
    $_REQUEST["um_update"] = urldecode($_REQUEST["um_update"]);

    $_REQUEST["um_name"] = ereg_replace( ' |-','_',$_REQUEST["um_name"] );
    $gBitSmarty->assign_by_ref('um_name', $_REQUEST["um_name"]);
    $gBitSmarty->assign_by_ref('um_title', $_REQUEST["um_title"]);
    $gBitSmarty->assign_by_ref('um_data', $_REQUEST["um_data"]);
    $modlib->replace_user_module($_REQUEST["um_name"], $_REQUEST["um_title"], $_REQUEST["um_data"]);
}


$user_modules = $modlib->list_user_modules();
$gBitSmarty->assign_by_ref('user_modules', $user_modules["data"]);

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find'
);

?>
