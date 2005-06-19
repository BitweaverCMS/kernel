<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* File:     resource.bitpackage.php
* Type:     resource
* Name:     bitpackage
* Purpose:  Fetches templates from the correct package
* -------------------------------------------------------------
*/
function smarty_resource_bitpackage_source($tpl_name, &$tpl_source, &$smarty)
{
	global $gBitSystem, $gBitSystem; // gBitSystem is just for temporary backward compatibility
	$ret = false;

//	list( $package, $template ) = split( '/', $tpl_name );

	$path = explode( '/', $tpl_name );
	$package = array_shift( $path );
	$subdir = '';
	$template = array_pop( $path );
	foreach( $path as $p ) {
		$subdir .= $p.'/';
	}
	$subdir .= preg_match( '/mod_/', $template ) ? 'modules' : 'templates';

	// look in themes/force/
	$forceTemplate = THEMES_PKG_PATH."/force/$template";
	// look in themes/style/<stylename>/<package>/templates/
	$overrideTemplate = $gBitSystem->getStylePath()."/$package/$subdir/$template";
	// look in themes/style/<stylename>/<package>/ - module files are unique anyway
	$overrideTemplateSimple = $gBitSystem->getStylePath()."/$package/$template";
	// look in root of style themes/style/<stylename>/
	$overrideRootTemplate = $gBitSystem->getStylePath().$template;
	// look for default package template
	$package_template = BIT_PKG_PATH.$gBitSystem->mPackages[strtolower( $package )]['dir']."/$subdir/$template";

//vd( $forceTemplate.' - '.$overrideTemplate.' - '.$overrideRootTemplate.' - '.$package_template );

	global $gNoForceStyle;
	if ( empty( $gNoForceStyle ) && file_exists( $forceTemplate ) ) {
		$tpl_source = fread( fopen($forceTemplate, "r"), filesize($forceTemplate) );
		$ret = true;
	} elseif ( file_exists( $overrideTemplate ) ) {
		$tpl_source = fread( fopen($overrideTemplate, "r"), filesize($overrideTemplate) );
		$ret = true;
	} elseif ( file_exists( $overrideTemplateSimple ) ) {
		$tpl_source = fread( fopen($overrideTemplateSimple, "r"), filesize($overrideTemplateSimple) );
		$ret = true;
	} elseif ( file_exists( $overrideRootTemplate ) ) {
		$tpl_source = filesize($overrideRootTemplate) ? fread( fopen($overrideRootTemplate, "r"), filesize($overrideRootTemplate) ) : '';
		$ret = true;
	} elseif ( file_exists( $package_template )) {
		$tpl_source = fread( fopen($package_template, "r"), filesize($package_template) );
		$ret = true;
	} else {
		$tpl_source = "<p>MISSING TEMPLATE:<br/> <b>p_resource_type:</b> $p_resource_type<br><b>p_resource_name:</b> $p_resource_name<br><b>p_template_source:</b> $p_template_source<br><b>p_template_timestamp:</b> $p_template_timestamp<br><b>p_smarty_obj:</b> $p_smarty_obj <br /><b>override_template:</b> $override_template<br/><b>package_template:</b>$package_template<br/><b>TIKI Package Path:</b>".BIT_PKG_PATH;
		$ret = true;
	}

	return $ret;
}

// the PHP sibling file needs to be included in modules_inc before this fetch so caching works properly
function smarty_resource_bitpackage_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
	//$tpl_timestamp = time();
	//return true;
	global $gBitSystem;
	$ret = false;

//	list( $package, $template ) = split( '/', $tpl_name );

	$path = explode( '/', $tpl_name );
	$package = array_shift( $path );
	$subdir = '';
	$template = array_pop( $path );
	foreach( $path as $p ) {
		$subdir .= $p.'/';
	}
	$subdir .= preg_match( '/mod_/', $template ) ? 'modules' : 'templates';


	// look in themes/force/
	$forceTemplate = THEMES_PKG_PATH."/force/$template";
	// look in themes/style/<stylename>/<package>/templates/
	$overrideTemplate = $gBitSystem->getStylePath()."/$package/$subdir/$template";
	// look in themes/style/<stylename>/<package>/templates/
	$overrideTemplateSimple = $gBitSystem->getStylePath()."/$package/$template";
	// look in root of style themes/style/<stylename>/
	$overrideRootTemplate = $gBitSystem->getStylePath().$template;
	// look for default package template
	$package_template = BIT_PKG_PATH.$gBitSystem->mPackages[strtolower( $package )]['dir']."/$subdir/$template";
//print "<br/>Time ($package $template)(FORCE: $forceTemplate<br/>OVERRIDE: $overrideTemplate $overrideRootTemplate $package_template)<br/>";
	global $gNoForceStyle;
	if ( empty( $gNoForceStyle ) && file_exists( $forceTemplate ) ) {
		$tpl_timestamp = filemtime($forceTemplate);
		$ret = true;
	} elseif ( file_exists( $overrideTemplate ) ) {
		$tpl_timestamp = filemtime($overrideTemplate);
		$ret = true;
	} elseif ( file_exists( $overrideTemplateSimple ) ) {
		$tpl_timestamp = filemtime($overrideTemplateSimple);
		$ret = true;
	} elseif ( file_exists( $overrideRootTemplate ) ) {
		$tpl_timestamp = filemtime($overrideRootTemplate);
		$ret = true;
	} elseif ( file_exists( $package_template )) {
		$tpl_timestamp = filemtime($package_template);
		$ret = true;
	} else {
		$ret = false;
	}

	return $ret;
}

function smarty_resource_bitpackage_secure($tpl_name, &$smarty)
{
	// assume all templates are secure
	return true;
}

function smarty_resource_bitpackage_trusted($tpl_name, &$smarty)
{
	// not used for templates
}
?>
