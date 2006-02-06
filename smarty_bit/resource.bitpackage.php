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
function smarty_resource_bitpackage_source($tpl_name, &$tpl_source, &$gBitSmarty) {
	global $gBitSystem;
	$ret = false;

	$path = explode( '/', $tpl_name );
	$package = array_shift( $path );
	$subdir = '';
	$template = array_pop( $path );
	foreach( $path as $p ) {
		$subdir .= $p.'/';
	}
	$subdir .= preg_match( '/mod_/', $template ) ? 'modules' : 'templates';

	// look in themes/force/
	$forceTemplate = THEMES_PKG_PATH."/force/$package/$template";
	$forceTemplateSimple = THEMES_PKG_PATH."/force/$template";
	// look in themes/styles/<stylename>
	$overrideTemplate = $gBitSystem->getStylePath()."/$package/$template";
	$overrideTemplateSimple = $gBitSystem->getStylePath().$template;
	// look for default package template
	$package_template = BIT_ROOT_PATH.$gBitSystem->mPackages[strtolower( $package )]['dir']."/$subdir/$template";

	//vd( $forceTemplateSimple.' - '.$overrideTemplate.' - '.$overrideTemplateSimple.' - '.$package_template );

	global $gNoForceStyle;
	if ( empty( $gNoForceStyle ) && file_exists( $forceTemplate ) ) {
		$tpl_source = fread( fopen($forceTemplate, "r"), filesize($forceTemplate) );
		$ret = TRUE;
	} elseif ( empty( $gNoForceStyle ) && file_exists( $forceTemplateSimple ) ) {
		$tpl_source = fread( fopen($forceTemplateSimple, "r"), filesize($forceTemplateSimple) );
		$ret = TRUE;
	} elseif ( file_exists( $overrideTemplate ) ) {
		$tpl_source = fread( fopen($overrideTemplate, "r"), filesize($overrideTemplate) );
		$ret = TRUE;
	} elseif ( file_exists( $overrideTemplateSimple ) ) {
		$tpl_source = filesize($overrideTemplateSimple) ? fread( fopen($overrideTemplateSimple, "r"), filesize($overrideTemplateSimple) ) : '';
		$ret = TRUE;
	} elseif ( file_exists( $package_template )) {
		$tpl_source = fread( fopen($package_template, "r"), filesize($package_template) );
		$ret = TRUE;
	} else {
		$tpl_source = "<p>MISSING TEMPLATE:<br/> <b>p_resource_type:</b> $p_resource_type<br/><b>p_resource_name:</b> $p_resource_name<br/><b>p_template_source:</b> $p_template_source<br/><b>p_template_timestamp:</b> $p_template_timestamp<br/><b>p_smarty_obj:</b> $p_smarty_obj <br /><b>override_template:</b> $override_template<br/><b>package_template:</b>$package_template<br/><b>TIKI Package Path:</b>".BIT_ROOT_PATH;
		$ret = TRUE;
	}

	return $ret;
}

// the PHP sibling file needs to be included in modules_inc before this fetch so caching works properly
function smarty_resource_bitpackage_timestamp($tpl_name, &$tpl_timestamp, &$gBitSmarty) {
	//$tpl_timestamp = time();
	//return true;
	global $gBitSystem;
	$ret = FALSE;

	$path = explode( '/', $tpl_name );
	$package = array_shift( $path );
	$subdir = '';
	$template = array_pop( $path );
	foreach( $path as $p ) {
		$subdir .= $p.'/';
	}
	$subdir .= preg_match( '/mod_/', $template ) ? 'modules' : 'templates';

	// look in themes/force/
	$forceTemplate = THEMES_PKG_PATH."/force/$package/$template";
	$forceTemplateSimple = THEMES_PKG_PATH."/force/$template";
	// look in themes/style/<stylename>/<package>/templates/
	$overrideTemplate = $gBitSystem->getStylePath()."/$package/$template";
	// look in root of style themes/style/<stylename>/
	$overrideTemplateSimple = $gBitSystem->getStylePath().$template;
	// look for default package template
	$package_template = BIT_ROOT_PATH.$gBitSystem->mPackages[strtolower( $package )]['dir']."/$subdir/$template";

	//print "<br/>Time ($package $template)(FORCE: $forceTemplateSimple<br/>OVERRIDE: $overrideTemplate $overrideTemplateSimple $package_template)<br/>";

	global $gNoForceStyle;
	if ( empty( $gNoForceStyle ) && file_exists( $forceTemplate ) ) {
		$tpl_timestamp = filemtime($forceTemplate);
		$ret = TRUE;
	} elseif ( empty( $gNoForceStyle ) && file_exists( $forceTemplateSimple ) ) {
		$tpl_timestamp = filemtime($forceTemplateSimple);
		$ret = TRUE;
	} elseif ( file_exists( $overrideTemplate ) ) {
		$tpl_timestamp = filemtime($overrideTemplate);
		$ret = TRUE;
	} elseif ( file_exists( $overrideTemplateSimple ) ) {
		$tpl_timestamp = filemtime($overrideTemplateSimple);
		$ret = TRUE;
	} elseif ( file_exists( $package_template )) {
		$tpl_timestamp = filemtime($package_template);
		$ret = TRUE;
	} else {
		$ret = FALSE;
	}

	return $ret;
}

function smarty_resource_bitpackage_secure($tpl_name, &$gBitSmarty) {
	// assume all templates are secure
	return TRUE;
}

function smarty_resource_bitpackage_trusted($tpl_name, &$gBitSmarty) {
	// not used for templates
}
?>
