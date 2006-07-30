<?php
/**
 * Smarty Library Inteface Class
 *
 * @package Smarty
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitSmarty.php,v 1.13 2006/07/30 19:48:22 spiderr Exp $
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if( file_exists( UTIL_PKG_PATH.'smarty/libs/Smarty.class.php' ) ) {
	// set SMARTY_DIR that we have the absolute path
	define( 'SMARTY_DIR', UTIL_PKG_PATH.'smarty/libs/' );
	// If we have smarty in our kernel, use that.
	$smartyIncFile = SMARTY_DIR . 'Smarty.class.php';
} else {
	// assume it is in php's global include_path
	// don't set SMARTY_DIR if we are not using the bw copy
	$smartyIncFile = 'Smarty.class.php';
}

/**
 * required setup
 */
require_once($smartyIncFile);

/**
 * PermissionCheck
 *
 * @package kernel
 */
class PermissionCheck {
	function check( $perm ) {
		global $gBitUser;
		return $gBitUser->hasPermission( $perm );
	}
}

/**
 * BitSmarty
 *
 * @package kernel
 */
class BitSmarty extends Smarty
{
	function BitSmarty()
	{
		global $smarty_force_compile;
		Smarty::Smarty();
		$this->mCompileRsrc = NULL;
		$this->config_dir = "configs/";
		// $this->caching = false;
		$this->force_compile = $smarty_force_compile;
		$this->assign('app_name', 'bitweaver');
		$this->plugins_dir = array_merge(array(KERNEL_PKG_PATH . "smarty_bit"), $this->plugins_dir);
		$this->register_prefilter("add_link_ticket");

		global $permCheck;
		$permCheck = new PermissionCheck();
		$this->register_object('perm', $permCheck, array(), true, array('autoComplete'));
		$this->assign_by_ref( 'perm', $permCheck );
	}

	function _smarty_include ($pParams)
	{
		if( defined( 'TEMPLATE_DEBUG' ) && TEMPLATE_DEBUG == TRUE ) {
			echo "\n<!-- - - - {$pParams['smarty_include_tpl_file']} - - - -->\n";
		}
		$this->includeSiblingPhp( $pParams['smarty_include_tpl_file'] );
		return parent::_smarty_include ($pParams);
	}

	function _compile_resource($resource_name, $compile_path)
	{
		// this is used when auto-storing untranslated master strings
		$this->mCompileRsrc = $resource_name;
		return parent::_compile_resource($resource_name, $compile_path);
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false)
	{
		global $gBitSystem;
		$this->verifyCompileDir();
		$_smarty_cache_id = $_smarty_cache_id;
		$_smarty_compile_id = $_smarty_compile_id;
		if( strpos( $_smarty_tpl_file, ':' ) ) {
			list($resource, $location) = split(':', $_smarty_tpl_file);
			if ($resource == 'bitpackage') {
				list($package, $template) = split('/', $location);
				// exclude temp, as it contains nexus menus
				if( !$gBitSystem->isPackageActive( $package ) && $package != 'temp' ) {
					return '';
				}
			}
		}

		// the PHP sibling file needs to be included here, before the fetch so caching works properly
		$this->includeSiblingPhp($_smarty_tpl_file);
		if( defined( 'TEMPLATE_DEBUG' ) && TEMPLATE_DEBUG == TRUE ) {
			echo "\n<!-- - - - {$_smarty_tpl_file} - - - -->\n";
		}
		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
	}
	// {{{ includeSiblingPhp
	/**
	* THE method to invoke if you want to be sure a tpl's sibling php file gets included if it exists. This
	* should not need to be invoked from anywhere except within this class
	*
	* @param string $pRsrc resource of the template, should be of the form "bitpackage:<packagename>/<templatename>"
	* @return TRUE if a sibling php file was included
	* @access private
	*/
	function includeSiblingPhp($pRsrc)
	{
		$ret = false;
		if (strpos($pRsrc, ':'))
		{
			list($resource, $location) = split(':', $pRsrc);
			if ($resource == 'bitpackage')
			{
				list($package, $template) = split('/', $location);
				// print "( $resource, $location )  ( $package, $template )<br/>";
				$subdir = preg_match('/mod_/', $template) ? 'modules' : 'templates';
				if (preg_match('/mod_/', $template) || preg_match('/center_/', $template))
				{
					global $gBitSystem;
					$path = $gBitSystem->mPackages[$package]['path'];
					$modPhpFile = str_replace('.tpl', '.php', "$path$subdir/$template");
					if (file_exists($modPhpFile))
					{
						global $gBitSmarty, $gBitSystem, $gBitUser, $user, $smarty, $gQueryUserId, $module_rows, $module_params, $module_column;
						// Module Params were passed in from the template, like kernel/dynamic.tpl
						if( $moduleParams = $this->get_template_vars('moduleParams') ) {
							if( strpos( trim( $moduleParams['params'] ), ' ' ) ) {
								$module_params = parse_xml_attributes( $moduleParams['params'] );
							} else {
								parse_str( $moduleParams["params"], $module_params );
							}
							$module_title = ( isset( $moduleParams['title'] ) ? tra( $moduleParams['title'] ) : ( isset( $module_params['title'] ) ? $module_params['title'] : NULL ) );
							$module_rows = $moduleParams['rows'];
						}
						include_once($modPhpFile);
						$ret = true;
					}
				}
			}
		}
	}

	function verifyCompileDir()
	{
		global $gBitSystem, $gBitLanguage, $bitdomain;
		if (!defined("TEMP_PKG_PATH")) {
			$temp = BIT_ROOT_PATH . "temp/";
		} else {
			$temp = TEMP_PKG_PATH;
		}
		$style = $gBitSystem->getStyle();
		$endPath = "$bitdomain/$style/".$gBitLanguage->mLanguage;

		// Compile directory
		$compDir = $temp . "templates_c/$endPath";
		$compDir = str_replace('//', '/', $compDir);
		$compDir = clean_file_path($compDir);
		mkdir_p($compDir);
		$this->compile_dir = $compDir;

		// Cache directory
		$cacheDir = $temp . "cache/$endPath";
		$cacheDir = str_replace('//', '/', $cacheDir);
		$cacheDir = clean_file_path($cacheDir);;
		mkdir_p($cacheDir);
		$this->cache_dir = $cacheDir;

	}
}
// This will insert a ticket on all template URL's that have GET parameters.
function add_link_ticket($tpl_source, &$smarty) {
	global $gBitUser;

	if ( is_object( $gBitUser ) && $gBitUser->isValid() ) {
		$from = '#href="(.*PKG_URL.*php)\?(.*)&(.*)"#i';
		$to = 'href="\\1?\\2&amp;tk={$gBitUser->mTicket}&\\3"';
		$tpl_source = preg_replace( $from, $to, $tpl_source );
		$from = '#<form([^>]*)>#i';
		$to = '<form\\1><input type="hidden" name="tk" value="{$gBitUser->mTicket}" />';
		$tpl_source = preg_replace( $from, $to, $tpl_source );
		if( strpos( $tpl_source, '{form}' ) ) {
			$tpl_source = str_replace( '{form}', '{form}<input type="hidden" name="tk" value="{$gBitUser->mTicket}" />', $tpl_source );
		} elseif( strpos( $tpl_source, '{form ' ) ) {
			$from = '#\{form(\}| [^\}]*)\}#i';
			$to = '{form\\1}<input type="hidden" name="tk" value="{$gBitUser->mTicket}" />';
			$tpl_source = preg_replace( $from, $to, $tpl_source );
		}
	}

	return $tpl_source;
}

?>
