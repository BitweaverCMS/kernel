<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_layout_inc.php,v 1.14 2006/02/06 22:56:46 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

if( !isset($_REQUEST["groups"] ) ) {
	$_REQUEST["groups"] = array();
}

if( empty( $_REQUEST['fPackage'] ) ) {
	$_REQUEST['fPackage'] = DEFAULT_PACKAGE;
}

$gBitSmarty->assign_by_ref( 'feedback', $feedback = array() );
$layout = $gBitSystem->getLayout( ROOT_USER_ID, $_REQUEST['fPackage'], FALSE );

if( empty( $_REQUEST['nojs'] ) ) {
	// load the javascript to get everythign working
	$gBitSmarty->assign( 'loadDragDrop', TRUE );

	// the layout has been moved around and we need to save it
	if( !empty( $_REQUEST['apply_layout'] ) || !empty( $_REQUEST['unassign'] ) ) {
		if( !empty( $_REQUEST['unassign'] ) ) {
			$unassign = array_keys( $_REQUEST['unassign'] );
			$gBitThemes->unassignModule( $unassign[0], ROOT_USER_ID, $_REQUEST['fPackage'] );
			unset( $_REQUEST['modules'][$unassign[0]] );
		}
		if( $gBitThemes->storeModulesBatch( $_REQUEST ) ) {
			$feedback['success'] = tra( "The layout was successfully saved." );
		} else {
			$feedback['error'] = tra( "There was a problem storing the layout." );
		}
	}
}

if( !empty( $_REQUEST['module_name'] ) ) {
	$fAssign['name'] = $_REQUEST['module_name'];
	$gBitSmarty->assign( 'fAssign', $fAssign );
}

$formMiscFeatures = array(
	'top_bar' => array(
		'label' => 'Top bar menu',
		'note' => 'Here you can enable or disable the menubar at the top of the page (available in most themes). Before you disable this bar, please make sure you have some means of navigation set up to access at least the administration page.',
	),
	'hide_my_top_bar_link' => array(
		'label' => 'Hide "My" Link',
		'note' => 'Hide the <strong>My &lt;sitename&gt;</strong> link from users that are not logged in.',
	),
	'top_bar_dropdown' => array(
		'label' => 'Dropdown menu',
		'note' => 'Use the CSS driven dropdown menus in the top bar. Compatibility and further reading can be found at <a class="external" href="http://www.htmldog.com/articles/suckerfish/dropdowns/">Suckerfish Dropdowns</a>.',
	),
	'right_column' => array(
		'label' => 'Right Module Column',
		'note' => 'Here you can disable the right column site-wide.',
	),
	'left_column' => array(
		'label' => 'Left Module Column',
		'note' => 'Here you can disable the left column site-wide.',
	),
);
$gBitSmarty->assign( 'formMiscFeatures',$formMiscFeatures );

// process form - check what tab was used and set it
$processForm = set_tab();

if( $processForm == 'Misc' ) {
	foreach( array_keys( $formMiscFeatures ) as $item ) {
		simple_set_toggle( $item );
	}
} elseif( isset( $_REQUEST['fModule'] ) ) {

	if( isset( $_REQUEST['fMove'] ) && isset(  $_REQUEST['fModule'] ) ) {
		switch( $_REQUEST['fMove'] ) {
			case "unassign":
				$gBitThemes->unassignModule( $_REQUEST['fModule'], ROOT_USER_ID, $_REQUEST['fPackage'] );
				break;
			case "up":
				$gBitThemes->moduleUp( $_REQUEST['fModule'], ROOT_USER_ID, $_REQUEST['fPackage'] );
				break;
			case "down":
				$gBitThemes->moduleDown( $_REQUEST['fModule'], ROOT_USER_ID, $_REQUEST['fPackage'] );
				break;
			case "left":
				$gBitThemes->modulePosition( $_REQUEST['fModule'], ROOT_USER_ID, $_REQUEST['fPackage'], 'r' );
				break;
			case "right":
				$gBitThemes->modulePosition( $_REQUEST['fModule'], ROOT_USER_ID, $_REQUEST['fPackage'], 'l' );
				break;
		}
	}
} elseif( isset( $fSubmitCustomize ) ) {
//	TODO
} elseif (isset($_REQUEST["edit_assign"])) {
	$_REQUEST["edit_assign"] = urldecode($_REQUEST["edit_assign"]);

	$info = $gBitThemes->get_assigned_module($_REQUEST["edit_assign"]);
	$grps = '';

	if ($info["groups"]) {
		$module_groups = unserialize($info["groups"]);

		foreach ($module_groups as $amodule) {
			if( is_numeric( $amodule ) ) {
				$grps = $grps . ' $amodule ';
			}
		}
	}

	if (!isset($info['rows']) || empty($info['rows'])) {
		$info['rows'] = 0;
	}

	$gBitSmarty->assign('module_groups', $grps);
	$gBitSmarty->assign_by_ref('assign_name', $info["name"]);
	//$gBitSmarty->assign_by_ref('assign_title',$info["title"]);
	$gBitSmarty->assign_by_ref('assign_position', $info["position"]);
	$gBitSmarty->assign_by_ref('assign_cache', $info["cache_time"]);
	$gBitSmarty->assign_by_ref('assign_rows', $info["rows"]);
	$gBitSmarty->assign_by_ref('assign_params', $info["params"]);
	$gBitSmarty->assign_by_ref('assign_type', $info["type"]);

	if (isset($info["ord"])) {
		$cosa = "" . $info["ord"];
	} else {
		$cosa = "";
	}

	$gBitSmarty->assign_by_ref('assign_order', $cosa);
} elseif( $processForm == 'Center' || $processForm == 'Column' ) {
	if( !empty( $_REQUEST['groups'] ) ) {
		$_REQUEST['fAssign']['groups'] = '';
		foreach( $_REQUEST['groups'] as $groupId ) {
			$_REQUEST['fAssign']['groups'] .= $groupId.' ';
		}
	}
	$fAssign = &$_REQUEST['fAssign'];
	$fAssign['layout'] = $_REQUEST['fPackage'];
	$gBitThemes->storeModule( $fAssign );
	$fAssign['user_id'] = ROOT_USER_ID;
	$fAssign['layout'] = $_REQUEST['fPackage'];
	$gBitThemes->storeLayout( $fAssign );
	$gBitSmarty->assign_by_ref( 'fAssign', $fAssign );
}

$sortedPackages = $gBitSystem->mPackages;
sort( $sortedPackages );
$gBitSmarty->assign( 'sortedPackages', $sortedPackages );
$gBitSmarty->assign( 'fPackage', $_REQUEST['fPackage'] );

$layout = $gBitSystem->getLayout( ROOT_USER_ID, (isset( $_REQUEST['fPackage'] ) ? $_REQUEST['fPackage'] : NULL), FALSE );
$gBitThemes->generateModuleNames( $layout );
$gBitSmarty->assign_by_ref( 'layout', $layout );

$layoutAreas = array( 'left'=>'l', 'center'=>'c', 'right'=>'r' );
$gBitSmarty->assign_by_ref( 'layoutAreas', $layoutAreas );

$packages = array();
foreach( $gBitSystem->mPackages as $pkg ) {
	array_push( $packages, array( "name" => $pkg,
								  "left_column" => $gBitSystem->getPreference( $pkg."_left_column", 'y' ),
								  "right_column" => $gBitSystem->getPreference( $pkg."_right_column", 'y'),
								  "top_bar" => $gBitSystem->getPreference( $pkg."_top_bar", 'y'),
								  "bot_bar" => $gBitSystem->getPreference( $pkg."_bot_bar", 'y') ) );
}

//****** Setup assign modules panel
$module_groups = array();

$allModules = $gBitThemes->getAllModules();
ksort( $allModules );
$gBitSmarty->assign_by_ref( 'allModules', $allModules );

$allModulesHelp = $gBitThemes->getAllModules( 'modules', 'help_mod_' );
ksort( $allModulesHelp );
$gBitSmarty->assign_by_ref( 'allModulesHelp', $allModulesHelp );

$allCenters = $gBitThemes->getAllModules( 'templates', 'center_' );
ksort( $allCenters );
$sections['kernel'] = tra( "Site Default" );
foreach( array_keys( $allCenters ) as $pkg ) {
	$sections[strtolower( $pkg )] = ucfirst( $pkg );
}
$gBitSmarty->assign_by_ref( 'allCenters', $allCenters );
$gBitSmarty->assign_by_ref( 'sections', $sections );

$orders = array();

for ($i = 1; $i < 50; $i++) {
	$orders[] = $i;
}

$gBitSmarty->assign_by_ref('orders', $orders);
$groups = $gBitUser->getAllUserGroups( ROOT_USER_ID );
//vd($groups);

foreach( array_keys( $groups ) as $groupId) {
	if (in_array($groups[$groupId]["group_name"], $module_groups)) {
		$groups[$groupId]["selected"] = 'y';
	} else {
		$groups[$groupId]["selected"] = 'n';
	}
	//vd($groups[$i]);
}

$gBitSmarty->assign_by_ref("groups", $groups);
?>
