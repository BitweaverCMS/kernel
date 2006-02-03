<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/module_controls_inc.php,v 1.6 2006/02/03 17:28:21 squareing Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: module_controls_inc.php,v 1.6 2006/02/03 17:28:21 squareing Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * Initialization
 */
include_once( '../bit_setup_inc.php' );

if (!$gBitUser->hasPermission( 'bit_p_configure_modules' )) {
	$gBitSmarty->assign('msg', tra("You dont have permission to use this feature"));
	$gBitSystem->display( 'error.tpl' );
	die;
}
/*if (!$gBitSystem->isFeatureActive( 'user_assigned_modules' ) && $check_req) {
	$gBitSmarty->assign('msg', tra("This feature is disabled").": user_assigned_modules");
	$gBitSystem->display( 'error.tpl' );
	die;
}*/
if (!$gBitUser->isRegistered()) {
	$gBitSmarty->assign('msg', tra("You must log in to use this feature"));
	$gBitSystem->display( 'error.tpl' );
	die;
}

$url = $_SERVER["HTTP_REFERER"];

//    global $debugger;
//    $debugger->msg('Module control clicked: '.$check_req);
    // Make defaults if user still ot configure modules for himself
//    if (!$usermoduleslib->user_has_assigned_modules($user))
//        $usermoduleslib->create_user_assigned_modules($user);

//$user_id = $gBitUser->getUserId();
$user_id = ROOT_USER_ID;

// Handle control icon click
if( isset( $_REQUEST['fMove'] ) && isset(  $_REQUEST['fPackage'] ) && isset(  $_REQUEST['fModule'] ) ) {
	switch( $_REQUEST['fMove'] ) {
		case "unassign":
			$gBitThemes->unassignModule( $_REQUEST['fModule'], $user_id, $_REQUEST['fPackage'] );
			break;
		case "up":
			$gBitThemes->moduleUp( $_REQUEST['fModule'], $user_id, $_REQUEST['fPackage'] );
			break;
		case "down":
			$gBitThemes->moduleDown( $_REQUEST['fModule'], $user_id, $_REQUEST['fPackage'] );
			break;
		case "left":
			$gBitThemes->modulePosition( $_REQUEST['fModule'], $user_id, $_REQUEST['fPackage'], 'r' );
			break;
		case "right":
			$gBitThemes->modulePosition( $_REQUEST['fModule'], $user_id, $_REQUEST['fPackage'], 'l' );
			break;
	}
}

// Remove module movemet paramaters from an URL
// \todo What if 'mc_xxx' arg was not at the end? (if smbd fix URL by hands...)
//       should I handle this very special (hack?) case?
//    $url = preg_replace('/(.*)(\?|&){1}(mc_up|mc_down|mc_move|mc_unassign)=[^&]*/','\1', $url);

// Fix locaton if parameter was removed...
header('Location: '.$url);
?>
