<?php
// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

require_once( '../../kernel/includes/setup_inc.php' );

$gBitSystem->verifyPermission( 'p_admin' );
$feedback = array();

$diskUsage = array(
	'templates_c' => array(
		'path' => TEMP_PKG_PATH.'templates_c',
		'title' => tra( 'Templates' ),
	),
	'lang' => array(
		'path' => TEMP_PKG_PATH.'lang',
		'title' => tra( 'Language Files' ),
	),
	'shoutbox' => array(
		'path' => TEMP_PKG_PATH.'shoutbox',
		'title' => tra( 'Shoutbox' ),
	),
	'modules' => array(
		'path' => TEMP_PKG_PATH.'modules/cache',
		'title' => tra( 'Modules' ),
		'subdir' => $bitdomain,
	),
	'cache' => array(
		'path' => TEMP_PKG_PATH.'cache',
		'title' => tra( 'System Cache' ),
		'subdir' => $bitdomain,
	),
	'icons' => array(
		'path' => TEMP_PKG_PATH.'themes/biticon',
		'title' => tra( 'Icons' ),
	),
	'liberty_cache' => array(
		'path' => TEMP_PKG_PATH.'liberty/cache',
		'title' => tra( 'Liberty Cache' ),
	),
	'format_help' => array(
		'path' => TEMP_PKG_PATH.'liberty/help',
		'title' => tra( 'Format Help' ),
	),
	'nexus' => array(
		'path' => TEMP_PKG_PATH.'nexus',
		'title' => tra( 'Nexus Menus' ),
	),
	'rss' => array(
		'path' => TEMP_PKG_PATH.'rss',
		'title' => tra( 'RSS Feed Cache' ),
	),
	'javascript' => array(
		'path' => STORAGE_PKG_PATH.'themes',
		'title' => tra( 'Javascript and CSS files' ),
	),
);

/* make sure we only display paths that exist
foreach( $diskUsage as $key => $item ) {
	if( !is_dir( $item['path'] )) {
		unset( $diskUsage[$key] );
	}
}*/

if( !empty( $_GET['pruned'] )) {
	$feedback['success'] = tra( 'The cache was successfully cleared.' );
}

if( !empty( $_GET['prune'] ) ) {
	foreach( $diskUsage as $key => $item ) {
		if( $_GET['prune'] == $key || $_GET['prune'] == 'all' ) {
			$dir = $item['path'].( !empty( $item['subdir'] ) ? '/'.$item['subdir'] : '' );
			if( is_dir( $dir ) && strpos( $item['path'], BIT_ROOT_PATH ) === 0 ) {
				if( unlink_r( $dir )) {
					$reload = TRUE;
				} else {
					$feedback['error'] = tra( 'There was a problem clearing out the cache.' );
				}
			}
		}
	}

	// nexus needs to rewrite the cache right away to avoid errors
	if( $gBitSystem->isPackageActive( 'nexus' ) && ( $_GET['prune'] == 'all' || $_GET['prune'] == 'nexus' )) {
		require_once( NEXUS_PKG_CLASS_PATH.'Nexus.php' );
		$nexus = new Nexus();
		$nexus->rewriteMenuCache();
	}

	// depending on what we've just nuked, we need to reload the page
	if( !empty( $reload )) {
		bit_redirect( KERNEL_PKG_URL."admin/admin_system.php?pruned=1" );
	}
}

if( !empty( $_GET['compiletemplates'] ) ) {
	cache_templates( BIT_ROOT_PATH, $gBitLanguage->getLanguage(), $_GET['compiletemplates'] );
}

foreach( $diskUsage as $key => $item ) {
	$diskUsage[$key]['du'] = du( $item['path'] );
}

$gBitSmarty->assign( 'diskUsage', $diskUsage );

$languages = array();
$languages = $gBitLanguage->listLanguages();
ksort( $languages );

$templates = array();
$langdir = TEMP_PKG_PATH."templates_c/".$gBitSystem->getConfig('style')."/";
foreach( array_keys( $languages ) as $clang ) {
	if( is_dir( $langdir.$clang ) ) {
		$templates[$clang] = array(
			'path'   => TEMP_PKG_PATH."templates_c/".$gBitSystem->getConfig( 'style' )."/",
			'title' => $languages[$clang]['full_name'],
			'du'    => du( $langdir.$clang ),
		);
	} else {
		$templates[$clang] = array(
			'path'   => TEMP_PKG_PATH."templates_c/".$gBitSystem->getConfig( 'style' )."/",
			'title' => $languages[$clang]['full_name'],
			'du'    => array(
				"count" => 0,
				"size" => 0,
			),
		);
	}
}
$gBitSmarty->assign( 'templates', $templates );
$gBitSmarty->assign( 'feedback', $feedback );

$gBitSystem->display( 'bitpackage:kernel/admin_system.tpl', tra( "System Cache" ) , array( 'display_mode' => 'admin' ));


// {{{ Functions
/**
 * du 
 * 
 * @param array $pPath 
 * @access public
 * @return boolean TRUE on success, FALSE on failure - $this->mErrors will contain reason for failure
 */
function du( $pPath ) {
	$size = $count = 0;

	if( !$pPath or !is_dir( $pPath ) ) {
		$ret['size'] = $size;
		$ret['count'] = $count;
		return $ret;
	}

	$all = opendir( $pPath );
	while( FALSE !== ( $file = readdir( $all ) ) ) {
		if( $file <> ".." and $file <> "." and $file <> "CVS" ) {
			if( is_file( $pPath.'/'.$file ) ) {
				$size += filesize( $pPath.'/'.$file );
				$count++;
				unset( $file );
			} elseif( is_dir( $pPath.'/'.$file ) ) {
				$du = du( $pPath.'/'.$file );
				$size += $du['size'];
				$count += $du['count'];
				unset( $file );
			}
		}
	}
	closedir( $all );
	unset( $all );

	$ret['size'] = $size;
	$ret['count'] = $count;
	return $ret;
}

/**
 * cache_templates 
 * 
 * @param array $pPath 
 * @param array $pOldLang 
 * @param array $pNewLang 
 * @access public
 * @return boolean TRUE on success, FALSE on failure - $this->mErrors will contain reason for failure
 */
function cache_templates( $pPath, $pOldLang, $pNewLang ) {
	global $gBitLanguage, $gBitSmarty;

	if( !$pPath or !is_dir( $pPath ) ) {
		return 0;
	}

	if( $dir = opendir( $pPath ) ) {
		while( FALSE !== ( $file = readdir( $dir ) ) ) {
			$a = explode( ".", $file );
			$ext = strtolower( end( $a ) );
			if( substr( $file, 0, 1 ) == "." or $file == 'CVS' ) {
				continue;
			}

			if( is_dir( $pPath."/".$file ) ) {
				cache_templates( $pPath."/".$file, $pOldLang, $pNewLang );
			} else {
				if( $ext == "tpl" ) {
					$file = str_replace( '//', '/', $pPath."/".$file );
					$gBitLanguage->setLanguage( $pNewLang );
					$gBitSmarty->verifyCompileDir();
					$comppath = $gBitSmarty->_get_compile_path( $file );
					$gBitLanguage->setLanguage( $pOldLang );
					// ignore files in sudirectories of templates/ - will break stuff as in the case of phpbb
					if( preg_match( "!/templates/\w*\.tpl!i", $file ) && !$gBitSmarty->_is_compiled( $file, $comppath ) ) {
						$gBitSmarty->_compile_resource( $file, $comppath );
					}
				}
			}
		}
		closedir( $dir );
	}
}
// }}}
// vim: set fdm=marker :
?>
