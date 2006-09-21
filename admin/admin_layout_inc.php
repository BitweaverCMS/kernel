<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_layout_inc.php,v 1.23 2006/09/21 15:31:42 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

if( !isset($_REQUEST["groups"] ) ) {
	$_REQUEST["groups"] = array();
}

if( empty( $_REQUEST['module_package'] ) ) {
	$_REQUEST['module_package'] = DEFAULT_PACKAGE;
}

$gBitSmarty->assign_by_ref( 'feedback', $feedback = array() );
$layout = $gBitSystem->getLayout( ROOT_USER_ID, $_REQUEST['module_package'], FALSE );

if( empty( $_REQUEST['nojs'] ) ) {
	// load the javascript to get everythign working
	$gBitSystem->setOnloadScript('initDragDrop();');
	$gBitSmarty->assign( 'loadDragDrop', TRUE );

	// the layout has been moved around and we need to save it
	if( !empty( $_REQUEST['apply_layout'] ) || !empty( $_REQUEST['unassign'] ) ) {
		if( !empty( $_REQUEST['unassign'] ) ) {
			$unassign = array_keys( $_REQUEST['unassign'] );
			$gBitThemes->unassignModule( $unassign[0], ROOT_USER_ID, $_REQUEST['module_package'], !empty( $_REQUEST['ord'] ) ? $_REQUEST['ord'] : NULL );
			unset( $_REQUEST['modules'][$unassign[0]] );
		}
		if( $gBitThemes->storeModulesBatch( $_REQUEST ) ) {
			$feedback['success'] = tra( "The layout was successfully saved." );
		} else {
			$feedback['error'] = tra( "There was a problem storing the layout." );
		}
	}
}

$gBitSystem->verifyInstalledPackages();

if( !empty( $_REQUEST['module_name'] ) ) {
	$fAssign['name'] = $_REQUEST['module_name'];
	$gBitSmarty->assign( 'fAssign', $fAssign );
}

$formMiscFeatures = array(
	'site_right_column' => array(
		'label' => 'Right Module Column',
		'note' => 'Here you can disable the right column site-wide.',
	),
	'site_left_column' => array(
		'label' => 'Left Module Column',
		'note' => 'Here you can disable the left column site-wide.',
	),
);
$gBitSmarty->assign( 'formMiscFeatures',$formMiscFeatures );

foreach( $gBitSystem->mPackages as $key => $package ) {
	if( !empty( $package['installed'] ) && ( !empty( $package['activatable'] ) || !empty( $package['tables'] ) ) ) {
		if( $package['name'] == 'kernel' ) {
			$package['name'] = tra( 'Site Default' );
		}
		$hideColumns[strtolower( $key )] =  ucfirst( $package['name'] );
	}
}
asort( $hideColumns );
$gBitSmarty->assign( 'hideColumns', $hideColumns );

// process form - check what tab was used and set it
$processForm = set_tab();

if( $processForm == 'Hide' ) {
	foreach( array_keys( $formMiscFeatures ) as $item ) {
		simple_set_toggle( $item, THEMES_PKG_NAME );
	}

	// evaluate what columns to hide
	foreach( array_keys( $hideColumns ) as $package ) {
		// left side first
		$pref = $package."_hide_left_col";
		if( isset( $_REQUEST['hide'][$pref] ) ) {
			$gBitSystem->storeConfig( $pref, 'y', THEMES_PKG_NAME );
		} else {
			// remove the setting from the db if it's not set
			$gBitSystem->storeConfig( $pref, NULL );
		}

		// now the right side
		$pref = $package."_hide_right_col";
		if( isset( $_REQUEST['hide'][$pref] ) ) {
			$gBitSystem->storeConfig( $pref, 'y', THEMES_PKG_NAME );
		} else {
			// remove the setting from the db if it's not set
			$gBitSystem->storeConfig( $pref, NULL );
		}
	}
} elseif( isset( $_REQUEST['module'] ) ) {

	if( isset( $_REQUEST['move_module'] ) && isset(  $_REQUEST['module'] ) ) {
		switch( $_REQUEST['move_module'] ) {
			case "unassign":
				$gBitThemes->unassignModule( $_REQUEST['module'], ROOT_USER_ID, $_REQUEST['module_package'], $_REQUEST['ord'] );
				break;
			case "up":
				$gBitThemes->moduleUp( $_REQUEST['module'], ROOT_USER_ID, $_REQUEST['module_package'] );
				break;
			case "down":
				$gBitThemes->moduleDown( $_REQUEST['module'], ROOT_USER_ID, $_REQUEST['module_package'] );
				break;
			case "left":
				$gBitThemes->modulePosition( $_REQUEST['module'], ROOT_USER_ID, $_REQUEST['module_package'], 'r' );
				break;
			case "right":
				$gBitThemes->modulePosition( $_REQUEST['module'], ROOT_USER_ID, $_REQUEST['module_package'], 'l' );
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
	$fAssign['layout'] = $_REQUEST['module_package'];
	$gBitThemes->storeModule( $fAssign );
	$fAssign['user_id'] = ROOT_USER_ID;
	$fAssign['layout'] = $_REQUEST['module_package'];
	$gBitThemes->storeLayout( $fAssign );
	$gBitSmarty->assign_by_ref( 'fAssign', $fAssign );
}

$sortedPackages = $gBitSystem->mPackages;
asort( $sortedPackages );
$gBitSmarty->assign( 'sortedPackages', $sortedPackages );
$gBitSmarty->assign( 'module_package', $_REQUEST['module_package'] );

$layout = $gBitSystem->getLayout( ROOT_USER_ID, (isset( $_REQUEST['module_package'] ) ? $_REQUEST['module_package'] : NULL), FALSE );
$gBitThemes->generateModuleNames( $layout );
$gBitSmarty->assign_by_ref( 'layout', $layout );

$layoutAreas = array( 'left'=>'l', 'center'=>'c', 'right'=>'r' );
$gBitSmarty->assign_by_ref( 'layoutAreas', $layoutAreas );

$packages = array();
foreach( $gBitSystem->mPackages as $pkg ) {
	array_push( $packages, array( "name" => $pkg,
								  "site_left_column" => $gBitSystem->getConfig( $pkg."_left_column", 'y' ),
								  "site_right_column" => $gBitSystem->getConfig( $pkg."_right_column", 'y'),
								  "site_top_bar" => $gBitSystem->getConfig( $pkg."_top_bar", 'y'),
								  "site_bot_bar" => $gBitSystem->getConfig( $pkg."_bot_bar", 'y') ) );
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
$gBitSmarty->assign_by_ref( 'allCenters', $allCenters );

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
