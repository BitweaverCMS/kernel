<?php
	$adminMenu = array();
	foreach( array_keys( $gBitSystem->mPackages ) as $package ) {
		$package = strtolower( $package );
		$tpl = "bitpackage:$package/menu_".$package."_admin.tpl";
		if( ($gBitSystem->isPackageActive( $package ) || $package == 'kernel') && @$smarty->template_exists( $tpl ) ) {
			$adminMenu[$package]['tpl'] = $tpl;
			$adminMenu[$package]['style'] = 'display:' . (empty($package) || (isset($_COOKIE[$package . 'admenu']) && ($_COOKIE[$package . 'admenu'] == 'o')) ? 'block;' : 'none;');
		}
	}
	$smarty->assign_by_ref( 'adminMenu', $adminMenu );
	$layoutstyle = 'display:'.((isset($_COOKIE['layoutadmenu']) && ($_COOKIE['layoutadmenu'] == 'o')) ? 'block;' : 'none;');
	$smarty->assign_by_ref( 'layoutstyle', $layoutstyle );
?>
