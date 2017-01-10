<?php
/**
 * @package kernel
 * @subpackage modules
 */
global $gBitSystem, $gBitUser, $moduleParams;

// if we're on any page in an admin/ dir, we'll simply set this to kernel
if( $gBitUser->isAdmin() ) {
	$admin = preg_match( "!/admin/!", $_SERVER['SCRIPT_NAME'] );
} else {
	$admin =  false;
}

$package = !empty($moduleParams['module_params']['package'])?$moduleParams['module_params']['package']:$gBitSystem->getActivePackage();

if( !empty( $gBitSystem->mAppMenu[$package]['menu_template'] ) && !$admin ) {
	$_template->tpl_vars['packageMenu'] = new Smarty_variable(  $gBitSystem->mAppMenu[$package]  );
}

if( empty( $module_title )) {
	$pkgName = constant( strtoupper( $package ).'_PKG_NAME' );

	if( $pkgName == 'kernel' || $admin ) {
		$pkgName = 'Administration';
	}
	$title = $gBitSystem->getConfig( $pkgName."_menu_text", ucfirst( $pkgName ));
	$_template->tpl_vars['moduleTitle'] = new Smarty_variable(  $title  );
}
?>
