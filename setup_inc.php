<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/setup_inc.php,v 1.95 2007/05/05 06:39:56 spiderr Exp $
 * @package kernel
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
/**
 * required setup
 */

require_once( BIT_ROOT_PATH.'kernel/config_defaults_inc.php' );
require_once( KERNEL_PKG_PATH.'kernel_lib.php' );
require_once( KERNEL_PKG_PATH.'BitTimer.php' );

// set error reporting
error_reporting( BIT_PHP_ERROR_REPORTING );

// this is evil stuff and causes hell for us
ini_set ( 'session.use_trans_sid', 'Off' );

// clean up $_GET and make sure others are clean as well
if( !empty( $_GET ) && is_array( $_GET ) && empty( $gNoToxify ) ) {
	detoxify( $_GET, TRUE );
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
require_once( KERNEL_PKG_PATH.'BitCache.php' );
global $gBitDb;
$gBitDb = new $dbClass();

require_once( KERNEL_PKG_PATH.'BitSystem.php' );
global $gBitSmarty, $gBitSystem;
$gBitSystem = new BitSystem();

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

// this is used to override the currently set site theme. when this is set everything else is ignored
global $gPreviewStyle;
$gPreviewStyle = FALSE;

// collects information about the browser - needed for various browser specific theme settings
require_once( UTIL_PKG_PATH.'phpsniff/phpSniff.class.php' );
global $gSniffer;
$gSniffer = new phpSniff;
$gBitSmarty->assign_by_ref( 'gBrowserInfo', $gSniffer->_browser_info );
// if we're viewing this site with a text-browser, we force the text-browser theme
if( !$gSniffer->_feature_set['css1'] && !$gSniffer->_feature_set['css2'] ) {
	$gPreviewStyle = 'lynx';
}

require_once( LANGUAGES_PKG_PATH.'BitLanguage.php' );
global $gBitLanguage;
$gBitLanguage = new BitLanguage();

require_once( THEMES_PKG_PATH."BitThemes.php" );
global $gBitThemes;
$gBitThemes = new BitThemes();
$gBitSmarty->assign_by_ref( 'gBitThemes', $gBitThemes );

// set various classes global
global $gBitUser, $gTicket, $userlib, $gBitDbType;

if( $gBitSystem->isDatabaseValid() ) {
	$gBitSystem->loadConfig();

	// gzip output compression
	if( ini_get( 'zlib.output_compression' ) == 1 ) {
		$gBitSmarty->assign( 'gzip', tra( 'Enabled' ));
	} elseif( $gBitSystem->isFeatureActive( 'site_output_obzip' )) {
		ob_start( "ob_gzhandler" );
		$gBitSmarty->assign( 'gzip', tra( 'Enabled' ));
	} else {
		$gBitSmarty->assign( 'gzip', tra( 'Disabled' ));
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
		define( 'BIT_BASE_URI', 'http'.(!empty($_SERVER['HTTPS'])?'s':'').'://'.$host );
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

	// XSS security check
	if( !empty( $_REQUEST['tk'] ) && empty( $_SERVER['bot'] ) ) {
		//$gBitUser->verifyTicket();
	} elseif( !empty( $_SERVER['bot'] ) ) {
	}

	// setStyle first, in case package decides it wants to reset the style in it's own <package>/bit_setup_inc.php
	$theme = $gBitThemes->getStyle();
	$theme = !empty( $theme ) ? $theme : DEFAULT_THEME;
	// users_themes='y' is for the entire site, 'h' is just for users homepage and is dealt with on users/index.php
	if(  !empty( $gBitSystem->mDomainInfo['domain_style'] ) ) {
		$theme = $gBitSystem->mDomainInfo['domain_style'];
	} elseif( $gBitSystem->getConfig('users_themes') == 'y' ) {
		if ( $gBitUser->isRegistered() && $gBitSystem->isFeatureActive( 'users_preferences' ) ) {
			if( $userStyle = $gBitUser->getPreference('theme') ) {
				$theme = $userStyle;
			}
		}
		if( isset( $_COOKIE['tiki-theme'] )) {
			$theme = $_COOKIE['tiki-theme'];
		}
	}
	$gBitThemes->setStyle( $theme );

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
	if( $gBitSystem->isFeatureActive( 'site_closed' ) && !$gBitUser->hasPermission( 'p_access_closed_site' ) && !isset( $bypass_siteclose_check )) {
		$_REQUEST['error'] = $gBitSystem->getConfig('site_closed_msg', 'Site is closed for maintainance; please come back later.');
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
	$https_mode = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on';
	if( $https_mode ) {
		$site_http_port = 80;
		$site_https_port = isset( $_SERVER['SERVER_PORT'] ) ? $_SERVER['SERVER_PORT'] : 443;
	} else {
		$site_http_port = isset( $_SERVER['SERVER_PORT'] ) ? $_SERVER['SERVER_PORT'] : 80;
		$site_https_port = 443;
	}

	$site_https_port = $gBitSystem->getConfig( 'site_https_port', $site_https_port );
	$site_https_prefix = $gBitSystem->getConfig( 'site_https_prefix', '/' );
	// we need this for backwards compatibility - use $gBitSystem->getPrerference( 'max_records' ) if you need it, or else the spanish inquisition will come and poke you with a soft cushion
	$max_records = $gBitSystem->getConfig( "max_records", 10 );

	$gBitSmarty->assign('site_https_login', $gBitSystem->getConfig( 'site_https_login' ) );
	$gBitSmarty->assign('site_https_login_required', $gBitSystem->getConfig( 'site_https_login_required' ) );

	$login_url = USERS_PKG_URL . 'validate.php';
	$gBitSmarty->assign('login_url', $login_url);

	if( $gBitSystem->isFeatureActive( 'site_https_login' ) || $gBitSystem->isFeatureActive( 'site_https_login_required' ) )	{
		$site_https_domain = $gBitSystem->getConfig('site_https_domain', '');
		$site_http_domain = $gBitSystem->getConfig('site_http_domain', '');
		$site_http_prefix = $gBitSystem->getConfig('site_http_prefix', '');

		$http_login_url = 'http://' . $site_http_domain;

		if ($site_http_port != 80)
			$http_login_url .= ':' . $site_http_port;

		$http_login_url .= $site_http_prefix . $gBitSystem->getDefaultPage();

		if (SID)
			$http_login_url .= '?' . SID;

		$edit_data = htmlentities(isset($_REQUEST["edit"]) ? $_REQUEST["edit"] : '', ENT_QUOTES);

		$https_login_url = 'https://' . $site_https_domain;

		if ($site_https_port != 443)
			$https_login_url .= ':' . $site_https_port;

		$https_login_url .= $site_https_prefix . $gBitSystem->getDefaultPage();

		if (SID)
			$https_login_url .= '?' . SID;

		$stay_in_ssl_mode = isset($_REQUEST['stay_in_ssl_mode']) ? $_REQUEST['stay_in_ssl_mode'] : '';

		if( $gBitSystem->isFeatureActive('site_https_login_required') ) {
			// only show "Stay in SSL checkbox if we're not already in HTTPS mode"
			$show_stay_in_ssl_mode = !$https_mode ? 'y' : 'n';
			$gBitSmarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
			if (!$https_mode) {
				$https_login_url = 'https://' . $site_https_domain;
				if ($site_https_port != 443)
					$https_login_url .= ':' . $site_https_port;

				$https_login_url .= $site_https_prefix . $login_url;

				if (SID) {
					$https_login_url .= '?' . SID;
				}

				$gBitSmarty->assign('login_url', $https_login_url);
			} else {
				// We're already in HTTPS mode, so let's stay there
				$stay_in_ssl_mode = 'on';
			}
		} else {
			$gBitSmarty->assign('http_login_url', $http_login_url);
			$gBitSmarty->assign('https_login_url', $https_login_url);
			// only show "Stay in SSL checkbox if we're not already in HTTPS mode"
			$show_stay_in_ssl_mode = $https_mode ? 'y' : 'n';
		}
		$gBitSmarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
		$gBitSmarty->assign('stay_in_ssl_mode', $stay_in_ssl_mode);
	}
}

?>
