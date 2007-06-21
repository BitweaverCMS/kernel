<?php
/**
 * @package kernel
 * @subpackage modules
 */
global $gBitSystem, $gBitUser;

// if we're on any page in an admin/ dir, we'll simply set this to kernel
if( $gBitUser->isAdmin() ) {
	$admin = preg_match( "!/admin/!", $_SERVER['PHP_SELF'] );
}

if( !empty( $gBitSystem->mAppMenu[ACTIVE_PACKAGE]['menu_template'] ) && !$admin ) {
	$gBitSmarty->assign( 'packageMenu', $gBitSystem->mAppMenu[ACTIVE_PACKAGE] );
}

if( empty( $module_title )) {
	$pkgName = constant( strtoupper( ACTIVE_PACKAGE ).'_PKG_NAME' );

	if( $pkgName == 'kernel' || $admin ) {
		$pkgName = 'Administration';
	}
	$title = $gBitSystem->getConfig( $pkgName."_menu_text", ucfirst( $pkgName ));
	$gBitSmarty->assign( 'moduleTitle', $title );
}
?>
