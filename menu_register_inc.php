<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/menu_register_inc.php,v 1.5 2005/08/24 20:52:14 squareing Exp $
 * @package kernel
 * @subpackage functions
 *
 * This file only needs to be called once, and only when you plan on rendering the app menu, or something similar.
 *
 * @todo All of the following should be moved to package specific initialization files, however, for now,
 * they are all in this single place, and eventually this file totally will go away - spiderr
 */
    global $gBitUser, $gBitSystem, $gBitSmarty;

	// =========================== Global ===========================
//	$gBitSystem->registerAppMenu( 'global', NULL, NULL, 'bitpackage:kernel/menu_global.tpl' );

	// =========================== My ===========================
	if( $gBitUser->isValid() ) {
		$displayTitle = !empty( $gBitSystem->mPrefs['site_menu_title'] ) ? $gBitSystem->mPrefs['site_menu_title'] : $gBitSystem->getPreference( 'siteTitle', 'Site' );
		$gBitSystem->registerAppMenu( 'users', 'My '.$displayTitle, ($gBitSystem->getPreference('feature_userPreferences') == 'y' ? USERS_PKG_URL.'my.php':''), 'bitpackage:users/menu_users.tpl' );
	}
	// =========================== Admin menu ===========================

	global $bit_p_admin,$bit_p_admin_chat, $bit_p_admin_categories, $bit_p_admin_banners, $bit_p_edit_templates, $bit_p_admin_dynamic, $bit_p_admin_dynamic, $bit_p_admin_mailin, $bit_p_edit_content_templates, $bit_p_edit_html_pages, $bit_p_view_referer_stats, $bit_p_admin_drawings, $bit_p_admin_shoutbox;

	array_multisort( $gBitSystem->mAppMenu );
	$gBitSmarty->assign_by_ref('appMenu',$gBitSystem->mAppMenu );
	if( $gBitUser->isAdmin() ) {
	
		$adminMenu = array();
		foreach( array_keys( $gBitSystem->mPackages ) as $package ) {
			$package = strtolower( $package );
			$tpl = "bitpackage:$package/menu_".$package."_admin.tpl";
			if( ($gBitSystem->isPackageActive( $package ) || $package == 'kernel') && @$gBitSmarty->template_exists( $tpl ) ) {
				$adminMenu[$package]['tpl'] = $tpl;
				$adminMenu[$package]['style'] = 'display:' . (empty($package) || (isset($_COOKIE[$package . 'admenu']) && ($_COOKIE[$package . 'admenu'] == 'o')) ? 'block;' : 'none;');
			}
		}
		array_multisort( $adminMenu );
		$gBitSmarty->assign_by_ref( 'adminMenu', $adminMenu );
		$layoutstyle = 'display:'.((isset($_COOKIE['layoutadmenu']) && ($_COOKIE['layoutadmenu'] == 'o')) ? 'block;' : 'none;');
		$gBitSmarty->assign_by_ref( 'layoutstyle', $layoutstyle );
	}
?>
