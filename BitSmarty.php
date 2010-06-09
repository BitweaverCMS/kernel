<?php
/**
 * Smarty Library Inteface Class
 *
 * @package Smarty
 * @version $Header$
 *
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 */

/**
 * required setup
 */
if( file_exists( UTIL_PKG_PATH.'smarty/libs/Smarty.class.php' )) {
	// set SMARTY_DIR that we have the absolute path
	define( 'SMARTY_DIR', UTIL_PKG_PATH.'smarty/libs/' );
	// If we have smarty in our kernel, use that.
	$smartyIncFile = SMARTY_DIR . 'Smarty.class.php';
} else {
	// assume it is in php's global include_path
	// don't set SMARTY_DIR if we are not using the bw copy
	$smartyIncFile = 'Smarty.class.php';
}

require_once( $smartyIncFile );

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
class BitSmarty extends Smarty {
	/**
	 * BitSmarty initiation
	 * 
	 * @access public
	 * @return void
	 */
	function BitSmarty() {
		global $smarty_force_compile, $smarty_debugging;
		Smarty::Smarty();
		$this->mCompileRsrc = NULL;
		$this->config_dir = "configs/";
		// $this->caching = FALSE;
		$this->force_compile = $smarty_force_compile;
		$this->debugging = isset($smarty_debugging) && $smarty_debugging;
		$this->assign( 'app_name', 'bitweaver' );
		$this->plugins_dir = array_merge( array( KERNEL_PKG_PATH . "smarty_bit" ), $this->plugins_dir );
		$this->register_prefilter( "add_link_ticket" );

		global $permCheck;
		$permCheck = new PermissionCheck();
		$this->register_object( 'perm', $permCheck, array(), TRUE, array( 'autoComplete' ));
		$this->assign_by_ref( 'perm', $permCheck );
	}

	/**
	 * override some smarty functions to bend them to our will
	 */
	function _smarty_include( $pParams ) {
		if( defined( 'TEMPLATE_DEBUG' ) && TEMPLATE_DEBUG == TRUE ) {
			echo "\n<!-- - - - {$pParams['smarty_include_tpl_file']} - - - -->\n";
		}
		$this->includeSiblingPhp( $pParams['smarty_include_tpl_file'], $pParams['smarty_include_vars'] );
		return parent::_smarty_include( $pParams );
	}

	function _compile_resource( $pResourceName, $pCompilePath ) {
		// this is used when auto-storing untranslated master strings
		$this->mCompileRsrc = $pResourceName;
		return parent::_compile_resource( $pResourceName, $pCompilePath );
	}

	function _fetch_resource_info( &$pParams ) {
		if( empty( $pParams['resource_name'] )) {
			return FALSE;
		} else {
			return parent::_fetch_resource_info( $pParams );
		}
	}

	function fetch( $pTplFile, $pCacheId = NULL, $pCompileId = NULL, $pDisplay = FALSE ) {
		global $gBitSystem;
		$this->verifyCompileDir();
		if( strpos( $pTplFile, ':' )) {
			list( $resource, $location ) = explode( ':', $pTplFile );
			if( $resource == 'bitpackage' ) {
				list( $package, $template ) = explode( '/', $location );
				// exclude temp, as it contains nexus menus
				if( !$gBitSystem->isPackageActive( $package ) && $package != 'temp' ) {
					return '';
				}
			}
		}

		// the PHP sibling file needs to be included here, before the fetch so caching works properly
		$this->includeSiblingPhp( $pTplFile );
		if( defined( 'TEMPLATE_DEBUG' ) && TEMPLATE_DEBUG == TRUE ) {
			echo "\n<!-- - - - {$pTplFile} - - - -->\n";
		}
		return parent::fetch( $pTplFile, $pCacheId, $pCompileId, $pDisplay );
	}

	/**
	 * THE method to invoke if you want to be sure a tpl's sibling php file gets included if it exists. This
	 * should not need to be invoked from anywhere except within this class
	 *
	 * @param string $pRsrc resource of the template, should be of the form "bitpackage:<packagename>/<templatename>"
	 * @return TRUE if a sibling php file was included
	 * @access private
	 */
	function includeSiblingPhp( $pRsrc, $pIncludeVars=NULL ) {
		global $gBitThemes;
		$ret = FALSE;
		if( strpos( $pRsrc, ':' )) {
			list( $resource, $location ) = explode( ':', $pRsrc );
			if( $resource == 'bitpackage' ) {
				// @TODO MODULE UPGRADE - we may want to simplify this after all modules have been upgraded
				// list( $package, $template ) = explode( '/', $location );
				$package = substr( $location, 0, strpos( $location, '/' ) );
				$template = substr( $location, strpos( $location, '/' ) + 1 );
				// print "( $resource, $location )  ( $package, $template )<br/>";
				$subdir = preg_match( '/mod_/', $template ) ? 'modules' : 'templates';
				if( preg_match('/mod_/', $template ) || preg_match( '/center_/', $template )) {
					global $gBitSystem;
					$path = constant( strtoupper( $package )."_PKG_PATH" );
					$modPhpFile = str_replace( '.tpl', '.php', "$path$subdir/$template" );
					if( file_exists( $modPhpFile )) {
						global $gBitSmarty, $gBitSystem, $gBitUser, $gQueryUserId, $moduleParams;
						$moduleParams = array();
						if( !empty( $pIncludeVars['module_params'] ) ) {
							// module_params were passed through via the {include},
							// e.g. {include file="bitpackage:foobar/mod_list_foo.tpl" module_params="user_id=`$gBitUser->mUserId`&sort_mode=created_desc"}
							$moduleParams['module_params'] = $gBitThemes->parseString( $pIncludeVars['module_params'] );
						} else {
							// Module Params were passed in from the template, like kernel/dynamic.tpl
							$moduleParams = $this->get_template_vars( 'moduleParams' );
						}
						include( $modPhpFile );
						$ret = TRUE;
					}
				}
			}
		}
	}

	/**
	 * verifyCompileDir 
	 * 
	 * @access public
	 * @return void
	 */
	function verifyCompileDir() {
		global $gBitSystem, $gBitLanguage, $bitdomain, $gBitThemes;
		if( !defined( "TEMP_PKG_PATH" )) {
			$temp = BIT_ROOT_PATH . "temp/";
		} else {
			$temp = TEMP_PKG_PATH;
		}
		$style = $gBitThemes->getStyle();
		$endPath = "$bitdomain/$style/".$gBitLanguage->mLanguage;

		// Compile directory
		$compDir = $temp . "templates_c/$endPath";
		$compDir = str_replace( '//', '/', $compDir );
		$compDir = clean_file_path( $compDir );
		mkdir_p( $compDir );
		$this->compile_dir = $compDir;

		// Cache directory
		$cacheDir = $temp . "cache/$endPath";
		$cacheDir = str_replace( '//', '/', $cacheDir );
		$cacheDir = clean_file_path( $cacheDir );
		mkdir_p( $cacheDir );
		$this->cache_dir = $cacheDir;
	}
}

/**
 * add_link_ticket This will insert a ticket on all template URL's that have GET parameters.
 * 
 * @param array $pTplSource source of template
 * @access public
 * @return ammended template source
 */
function add_link_ticket( $pTplSource ) {
	global $gBitUser;

	if( is_object( $gBitUser ) && $gBitUser->isRegistered() ) {
		$from = '#href="(.*PKG_URL.*php)\?(.*)&(.*)"#i';
		$to = 'href="\\1?\\2&amp;tk={$gBitUser->mTicket}&\\3"';
		$pTplSource = preg_replace( $from, $to, $pTplSource );
		$from = '#<form([^>]*)>#i';
		// div tag is for stupid XHTML compliance.
		$to = '<form\\1><div style="display:inline"><input type="hidden" name="tk" value="{$gBitUser->mTicket}" /></div>';
		$pTplSource = preg_replace( $from, $to, $pTplSource );
		if( strpos( $pTplSource, '{form}' )) {
			$pTplSource = str_replace( '{form}', '{form}<div style="display:inline"><input type="hidden" name="tk" value="{$gBitUser->mTicket}" /></div>', $pTplSource );
		} elseif( strpos( $pTplSource, '{form ' ) ) {
			$from = '#\{form(\}| [^\}]*)\}#i';
			$to = '{form\\1}<div style="display:inline"><input type="hidden" name="tk" value="{$gBitUser->mTicket}" /></div>';
			$pTplSource = preg_replace( $from, $to, $pTplSource );
		}
	}

	return $pTplSource;
}
?>
