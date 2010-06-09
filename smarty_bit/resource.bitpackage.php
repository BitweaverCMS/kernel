<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.bitpackage.php
 * Type:     resource
 * Name:     bitpackage
 * Purpose:  Fetches templates from the correct package
 * -------------------------------------------------------------
 */
function smarty_resource_bitpackage_source( $pTplName, &$pTplSource, &$gBitSmarty ) {
	$resources = smarty_get_bitweaver_resources( $pTplName );
	foreach( $resources as $resource ) {
		if( file_exists( $resource )) {
			$pTplSource = file_get_contents( $resource );
			return TRUE;
		}
	}

	vd( "Missing template:" );
	vd( $resources );
	return FALSE;
}

// the PHP sibling file needs to be included in modules_inc before this fetch so caching works properly
function smarty_resource_bitpackage_timestamp( $pTplName, &$pTplTimestamp, &$gBitSmarty ) {
	foreach( smarty_get_bitweaver_resources( $pTplName ) as $resource ) {
		if( file_exists( $resource )) {
			$pTplTimestamp = filemtime( $resource );
			return TRUE;
		}
	}
	return FALSE;
}

function smarty_resource_bitpackage_secure( $pTplName, &$gBitSmarty ) {
	// assume all templates are secure
	return TRUE;
}

function smarty_resource_bitpackage_trusted( $pTplName, &$gBitSmarty ) {
	// not used for templates
}

function smarty_get_bitweaver_resources( $pTplName ) {
	global $gBitThemes, $gNoForceStyle;

	$path = explode( '/', $pTplName );
	$package = array_shift( $path );
	$template = array_pop( $path );
	$subdir = '';
	foreach( $path as $p ) {
		$subdir .= $p.'/';
	}

	// files found in temp are special - these are stored in temp/<pkg>/(templates|modules)/<template.tpl>
	if( $package == 'temp' ) {
		// if it's a module, we need to look in the correct place
		$subdir .= ( preg_match( '/\b(help_)?mod_/', $template ) ? 'modules' : 'templates' );
		// we can't override these templates - they only exist in temp
		$ret['package_template'] = constant( strtoupper( $package ).'_PKG_PATH' )."$subdir/$template";
	} else {
		if( empty( $gNoForceStyle )) {
			// look in themes/force/
			$ret['force']        = THEMES_PKG_PATH."force/$package/$subdir$template";
			$ret['force_simple'] = THEMES_PKG_PATH."force/$subdir$template";
		}

		// look in themes/style/<stylename>/
		$ret['override']        = $gBitThemes->getStylePath()."$package/$subdir$template";
		$ret['override_simple'] = $gBitThemes->getStylePath().$subdir.$template;

		// if it's a module, we need to look in the correct place
		$subdir = ( preg_match( '/\b(help_)?mod_/', $template ) ? 'modules' : 'templates' )."/".$subdir;

		// look for default package template
		$ret['package_template'] = constant( strtoupper( $package ).'_PKG_PATH' )."$subdir$template";
	}

	return $ret;
}
?>
