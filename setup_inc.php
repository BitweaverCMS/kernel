<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/setup_inc.php,v 1.125 2009/01/30 17:04:30 spiderr Exp $
 * @package kernel
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
/**
 * required setup
 */

// immediately die on request to hack our database
if(( !empty( $_REQUEST['sort_mode'] ) && !is_array( $_REQUEST['sort_mode'] ) && strpos( $_REQUEST['sort_mode'], 'http' ) !== FALSE ) || ( !empty( $_REQUEST['PGV_BASE_DIRECTORY'] ) && strpos( $_REQUEST['PGV_BASE_DIRECTORY'], 'http' ) !== FALSE )) {
	die;
}

require_once( BIT_ROOT_PATH.'kernel/config_defaults_inc.php' );
require_once( KERNEL_PKG_PATH.'kernel_lib.php' );
require_once( KERNEL_PKG_PATH.'BitTimer.php' );

// set error reporting
error_reporting( BIT_PHP_ERROR_REPORTING );

// this is evil stuff and causes hell for us
ini_set( 'session.use_trans_sid', 'Off' );

if( ini_get( 'safe_mode' ) && ini_get( 'safe_mode_gid' )) {
	umask( 0007 );
}

// clean up $_GET and make sure others are clean as well
if( !empty( $_GET ) && is_array( $_GET ) && empty( $gNoToxify ) ) {
	detoxify( $_GET, TRUE, FALSE );
	$_REQUEST = array_merge( $_REQUEST, $_GET );
}

// Force a global ADODB db object so all classes share the same connection
switch( @$gBitDbSystem ) {
	case 'pear':
		$dbClass = 'BitDbPear';
		break;
	default:
		$dbClass = 'BitDbAdodb';
		break;
}
// the installer and select admin pages required DataDict to verify package installation
global $gForceAdodb;
if( !empty( $gForceAdodb )) {
	$dbClass = 'BitDbAdodb';
}
require_once( KERNEL_PKG_PATH.$dbClass.'.php' );

// =================== Global Classes ===================
global $gBitDb;
$gBitDb = new $dbClass();
if( defined( 'QUERY_CACHE_ACTIVE' ) ) {
	$gBitDb->setCaching();
}

require_once( KERNEL_PKG_PATH.'BitSystem.php' );
global $gBitSmarty, $gBitSystem;
$gBitSystem = new BitSystem();

// first thing we do, is check to see if our version of bitweaver is up to date.
// we need to know about this before any other package is loaded to ensure that we can exclude stuff that isn't backwards compatible.
// BIT_INSTALL is set by the installer and LOGIN_VALIDATE is set in users/validate.php
if( !empty( $gBitSystem->mConfig ) && version_compare( MIN_BIT_VERSION, $gBitSystem->getVersion(), '>' ) && !( defined( 'BIT_INSTALL' ) || defined( 'LOGIN_VALIDATE' ))) {
	define( 'INSTALLER_FORCE', TRUE );
}

// allow for overridden TEMP_PKG_PATH
if( !defined( 'TEMP_PKG_PATH' ) ) {
	$tempDir = $gBitSystem->getConfig( 'site_temp_dir', BIT_ROOT_PATH.'temp/' );
	if( strrpos( $tempDir, '/' ) + 1 != strlen( $tempDir ) ) {
		$tempDir .= '/';
	}

	define( 'TEMP_PKG_PATH', $tempDir );
	define( 'TEMP_PKG_URL', BIT_ROOT_URL.'temp/' );
}

BitSystem::prependIncludePath( UTIL_PKG_PATH.'/' );
BitSystem::prependIncludePath( UTIL_PKG_PATH.'pear/' );

require_once( LANGUAGES_PKG_PATH.'BitLanguage.php' );
global $gBitLanguage;
$gBitLanguage = new BitLanguage();

// collects information about the browser - needed for various browser specific theme settings
require_once( UTIL_PKG_PATH.'phpsniff/phpSniff.class.php' );
global $gSniffer;
$gSniffer = new phpSniff;
$gBitSmarty->assign_by_ref( 'gBrowserInfo', $gSniffer->_browser_info );

// set various classes global
global $gBitUser, $gTicket, $userlib, $gBitDbType;

if( $gBitSystem->isDatabaseValid() ) {
	$gBitSystem->loadConfig();

	// output compression
	if( ini_get( 'zlib.output_compression' ) == 1 ) {
		$gBitSmarty->assign( 'output_compression', 'zlib' );
	} elseif( $gBitSystem->isFeatureActive( 'site_output_obzip' ) && !empty( $_SERVER['SCRIPT_FILENAME'] ) && !preg_match( '!/download.php$!', $_SERVER['SCRIPT_FILENAME'] )) {
		ob_start( "ob_gzhandler" );
		$gBitSmarty->assign( 'output_compression', 'gzip' );
	}

	// we need to allow up to 900 chars for this value in our 250 char table column
	$gBitSystem->setConfig( 'site_keywords',
		$gBitSystem->getConfig( 'site_keywords' ).
		$gBitSystem->getConfig( 'site_keywords_1' ).
		$gBitSystem->getConfig( 'site_keywords_2' ).
		$gBitSystem->getConfig( 'site_keywords_3' )
	);

	$host = $gBitSystem->getConfig( 'kernel_server_name', $_SERVER['HTTP_HOST'] );
	if( !defined('BIT_BASE_URI' ) ) {
		// Added check for IIS $_SERVER['HTTPS'] uses 'off' value - wolff_borg
		define( 'BIT_BASE_URI', 'http'.(( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) ? 's' : '' ).'://'.$host );
	}

	if( !defined( 'BIT_BASE_PATH' ) ) {
		$root_url_count = strlen( BIT_ROOT_URL );
		$root_path_count = strlen( BIT_ROOT_PATH );
		$path_end = $root_path_count - $root_url_count;
		define( 'BIT_BASE_PATH', ( BIT_ROOT_URL == "/" ? BIT_ROOT_PATH : substr( BIT_ROOT_PATH, 0, $path_end ) . "/" ) );
	}

	// Force full URI's for offline or exported content (newsletters, etc.)
	$root = !empty( $_REQUEST['uri_mode'] ) ? BIT_BASE_URI : BIT_ROOT_URL;
	if( $root[strlen($root)-1] != '/' ) {
		$root .= '/';
	}
	define( 'UTIL_PKG_URL', $root.'util/' );
	define( 'LIBERTY_PKG_URL', $root.'liberty/' );

	// load only installed and active packages
	$gBitSystem->scanPackages( 'bit_setup_inc.php', TRUE, 'active', TRUE, TRUE );

	// some plugins check for active packages, so we do this *after* package scanning
	$gBitSmarty->assign_by_ref( "gBitSystem", $gBitSystem );

	// some liberty plugins might need to run some functions.
	// it's necessary that we call them early on after scanPackages() has been completed.
	foreach( $gLibertySystem->getPluginFunctions( 'preload_function' ) as $func ) {
		$func();
	}

	// TODO: XSS security check
	if( !empty( $_REQUEST['tk'] ) && empty( $_SERVER['bot'] ) ) {
		//$gBitUser->verifyTicket();
	} elseif( !empty( $_SERVER['bot'] ) ) {
	}

	// this will register and set up the dropdown menus and the application menus in modules
	require_once( KERNEL_PKG_PATH.'menu_register_inc.php' );

	// added for virtual hosting suport
	if( !isset( $bitdomain )) {
		$bitdomain = "";
	} else {
		$bitdomain .= "/";
	}
	$gBitSystem->storeConfig( 'bitdomain', $bitdomain, KERNEL_PKG_NAME );

	$gBitSmarty->assign( "bitdomain", $bitdomain );
	// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
	if( !isset( $_SERVER['QUERY_STRING'] )) {
		$_SERVER['QUERY_STRING'] = '';
	}
	if( !isset( $_SERVER['REQUEST_URI'] ) || empty( $_SERVER['REQUEST_URI'] )) {
		$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'].'/'.$_SERVER['QUERY_STRING'];
	}
	if( isset( $_REQUEST['page'] )) {
		$_REQUEST['page'] = strip_tags( $_REQUEST['page'] );
	}
	global $gHideModules;
	$gBitSmarty->assign_by_ref( 'gHideModules', $gHideModules );


	// =================== Kernel ===================
	//$gBitSmarty->assign_by_ref( "gBitSystemPackages", $gBitSystem->mPackages ); doesn't seem to be used - xing

	// check to see if admin has closed the site
	if(( isset( $_SERVER['SCRIPT_URL'] ) && $_SERVER['SCRIPT_URL'] == USERS_PKG_URL.'validate.php' )) {
		$bypass_siteclose_check = 'y';
	}
	if( empty($gShellScript) && $gBitSystem->isFeatureActive( 'site_closed' ) && !$gBitUser->hasPermission( 'p_access_closed_site' ) && !isset( $bypass_siteclose_check )) {
		$_REQUEST['error'] = $gBitSystem->getConfig('site_closed_msg','&nbsp;');
		include( KERNEL_PKG_PATH . 'error_simple.php' );
		exit;
	}

	// check to see if max server load threshold is enabled
	$site_use_load_threshold = $gBitSystem->getConfig( 'site_use_load_threshold', 'n' );
	// get average server load in the last minute. Keep quiet cause virtual hosts can give perm denied or openbase_dir is open_basedir on
	if(@is_readable('/proc/loadavg') && @($load = file('/proc/loadavg'))) {
		list($server_load) = explode(' ', $load[0]);
		$gBitSmarty->assign('server_load', $server_load);
		if ($site_use_load_threshold == 'y' && !$gBitUser->hasPermission( 'p_access_closed_site' ) && !isset($bypass_siteclose_check)) {
			$site_load_threshold = $gBitSystem->getConfig('site_load_threshold', 3);
			if ($server_load > $site_load_threshold) {
				$_REQUEST['error'] = $gBitSystem->getConfig('site_busy_msg', 'Server is currently too busy; please come back later.');
				include( KERNEL_PKG_PATH . 'error_simple.php' );
				exit;
			}
		}
	}

	// if we are interactively translating the website, we force template caching on every page load.
	if( $gBitSystem->isFeatureActive( 'i18n_interactive_translation' ) && $gBitUser->hasPermission( 'p_languages_edit' ) ) {
		$gBitSmarty->assign_by_ref( "gBitTranslationHash", $gBitTranslationHash );
	} else {
		// this has to be done since the permission can't be checked in BitLanguage::translate() as it's called too soon by prefilter.tr
		$gBitSystem->setConfig( 'i18n_interactive_translation', 'n' );
	}

	// All of the below deals with HTTPS - perhaps we should move this to a separate file
	if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
		$site_http_port = 80;
		$site_https_port = isset( $_SERVER['SERVER_PORT'] ) ? $_SERVER['SERVER_PORT'] : 443;
	} else {
		$site_https_port = 443;
		$site_http_port = isset( $_SERVER['SERVER_PORT'] ) ? $_SERVER['SERVER_PORT'] : 80;
	}

	$site_https_port = $gBitSystem->getConfig( 'site_https_port', $site_https_port );
	// we need this for backwards compatibility - use $gBitSystem->getPrerference( 'max_records' ) if you need it, or else the spanish inquisition will come and poke you with a soft cushion
	$max_records = $gBitSystem->getConfig( "max_records", 10 );

	$gBitSmarty->assign('site_https_login', $gBitSystem->getConfig( 'site_https_login' ) );
	$gBitSmarty->assign('site_https_login_required', $gBitSystem->getConfig( 'site_https_login_required' ) );

	$login_url = USERS_PKG_URL . 'validate.php';
	$gBitSmarty->assign( 'login_url', $login_url );

	if( $gBitSystem->isFeatureActive( 'site_https_login' ) || $gBitSystem->isFeatureActive( 'site_https_login_required' ) )	{
		$http_login_url = 'http://' . $gBitSystem->getConfig( 'site_http_domain', $_SERVER['HTTP_HOST'] );
		if( $site_http_port != 80 ) {
			$http_login_url .= ':'.$site_http_port;
		}
		$http_login_url .= $gBitSystem->getConfig( 'site_http_prefix', BIT_ROOT_URL ).USERS_PKG_URL.'login.php';

		$https_login_url = 'https://'.$gBitSystem->getConfig( 'site_https_domain', $_SERVER['HTTP_HOST'] );
		if( $site_https_port != 443 ) {
			$https_login_url .= ':'.$site_https_port;
		}
		$https_login_url .= $gBitSystem->getConfig( 'site_https_prefix', BIT_ROOT_URL ).USERS_PKG_URL.'login.php';

		$gBitSystem->setConfig( 'http_login_url', $http_login_url );
		if( $gBitSystem->isFeatureActive('site_https_login_required') ) {
			// force the login_url to the https_login_url if needed
			if( !( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' )) {
				$gBitSmarty->assign( 'login_url', $https_login_url );
			}
		} else {
			$gBitSystem->setConfig( 'http_login_url', $http_login_url );
			$gBitSystem->setConfig( 'https_login_url', $https_login_url );
		}
	}

	// if we have a valid user but their status is unsavory then completely cut them off from accessing the site
	if( $gBitUser->getField('content_status_id') < 0 ){
		$gBitSystem->scanPackages();
		$gBitSystem->fatalError( tra( 'Access Denied' )."!" );
	}
}

// INSTALLER_FORCE was set earlier and here we force the installer if needed.
if( defined( 'INSTALLER_FORCE' )) {
	$gBitSmarty->display( "bitpackage:kernel/force_installer.tpl" );
	die;
}
?>
