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
	global $gBitSystem, $gNoForceStyle;

	$resources = smarty_get_bitweaver_resources( $pTplName );
	extract( $resources );

	if ( empty( $gNoForceStyle ) && file_exists( $force ) ) {
		$pTplSource = fread( fopen( $force, "r" ), filesize($force) );
		return TRUE;
	} elseif ( empty( $gNoForceStyle ) && file_exists( $force_simple ) ) {
		$pTplSource = fread( fopen( $force_simple, "r" ), filesize( $force_simple ) );
		return TRUE;
	} elseif ( file_exists( $override ) ) {
		$pTplSource = fread( fopen( $override, "r" ), filesize( $override ) );
		return TRUE;
	} elseif ( file_exists( $override_simple ) ) {
		$pTplSource = filesize( $override_simple ) ? fread( fopen( $override_simple, "r" ), filesize( $override_simple ) ) : '';
		return TRUE;
	} elseif ( file_exists( $package_template ) ) {
		$pTplSource = !filesize( $package_template ) ? '' : fread( fopen( $package_template, "r" ), filesize( $package_template ) );
		return TRUE;
	} else {
		$pTplSource = "<p>MISSING TEMPLATE:<br/> <b>p_resource_type:</b> $p_resource_type<br/><b>p_resource_name:</b> $p_resource_name<br/><b>p_template_source:</b> $p_template_source<br/><b>p_template_timestamp:</b> $p_template_timestamp<br/><b>p_smarty_obj:</b> $p_smarty_obj <br /><b>override_template:</b> $override_template<br/><b>package_template:</b>$package_template<br/><b>TIKI Package Path:</b>".BIT_ROOT_PATH;
		return TRUE;
	}

	return FALSE;
}

// the PHP sibling file needs to be included in modules_inc before this fetch so caching works properly
function smarty_resource_bitpackage_timestamp( $pTplName, &$pTplTimestamp, &$gBitSmarty ) {
	global $gBitSystem, $gNoForceStyle;

	$resources = smarty_get_bitweaver_resources( $pTplName );
	extract( $resources );

	if ( empty( $gNoForceStyle ) && file_exists( $force ) ) {
		$pTplTimestamp = filemtime( $force );
		return TRUE;
	} elseif ( empty( $gNoForceStyle ) && file_exists( $force_simple ) ) {
		$pTplTimestamp = filemtime( $force_simple );
		return TRUE;
	} elseif ( file_exists( $override ) ) {
		$pTplTimestamp = filemtime( $override );
		return TRUE;
	} elseif ( file_exists( $override_simple ) ) {
		$pTplTimestamp = filemtime( $override_simple );
		return TRUE;
	} elseif ( file_exists( $package_template ) ) {
		$pTplTimestamp = filemtime( $package_template );
		return TRUE;
	} else {
		return FALSE;
	}
}

function smarty_resource_bitpackage_secure( $pTplName, &$gBitSmarty ) {
	// assume all templates are secure
	return TRUE;
}

function smarty_resource_bitpackage_trusted( $pTplName, &$gBitSmarty ) {
	// not used for templates
}

function smarty_get_bitweaver_resources( $pTplName ) {
	global $gBitSystem;

	$path = explode( '/', $pTplName );
	$package = array_shift( $path );
	$subdir = '';
	$template = array_pop( $path );
	foreach( $path as $p ) {
		$subdir .= $p.'/';
	}
	// if it's a module, we need to look in the correct path
	$subdir .= preg_match( '/mod_/', $template ) ? 'modules' : 'templates';

	// look in themes/force/
	$ret['force']            = THEMES_PKG_PATH."/force/$package/$template";
	$ret['force_simple']     = THEMES_PKG_PATH."/force/$template";

	// look in themes/style/<stylename>/
	$ret['override']         = $gBitSystem->getStylePath()."/$package/$template";
	$ret['override_simple']  = $gBitSystem->getStylePath().$template;

	// look for default package template
	// This needs to use the _PKG_PATH constant since the temp dir isn't defined in mPackages
	$ret['package_template'] = preg_replace( "!/+!", "/", constant( strtoupper( $package ).'_PKG_PATH' )."/$subdir/$template" );
	//$ret['package_template'] = BIT_ROOT_PATH.$gBitSystem->mPackages[strtolower( $package )]['dir']."/$subdir/$template";

	//print "<br/>Time ($package $template)(FORCE: $force_simple<br/>OVERRIDE: $override $override_simple $package_template)<br/>";
	return $ret;
}
?>
