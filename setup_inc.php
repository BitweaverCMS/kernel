<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/setup_inc.php,v 1.61 2006/05/29 22:01:04 squareing Exp $
 * @package kernel
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
/**
 * required setup
 */

require_once(BIT_ROOT_PATH . 'kernel/config_defaults_inc.php');

error_reporting( BIT_PHP_ERROR_REPORTING );

define( 'BIT_MAJOR_VERSION',	'2' );
define( 'BIT_MINOR_VERSION',	'0' );
define( 'BIT_SUB_VERSION',		'0' );
define( 'BIT_LEVEL',			'pre alpha' ); // 'beta' or 'dev' or 'rc' etc..

define( 'BIT_PKG_PATH', BIT_ROOT_PATH );

// These defines have to happen FIRST because core classes depend on them.
// This means these packages *CANNOT* be renamed
define('KERNEL_PKG_DIR', 'kernel');
define('KERNEL_PKG_NAME', 'kernel');
define('KERNEL_PKG_PATH', BIT_ROOT_PATH . 'kernel/');
define('KERNEL_PKG_URL', BIT_ROOT_URL . 'kernel/');

require_once(KERNEL_PKG_PATH . 'preflight_inc.php');

// clean up $_GET and make sure others are clean as well
if( !empty( $_GET ) && is_array( $_GET ) ) {
	detoxify( $_GET, TRUE );
	$_REQUEST = array_merge( $_REQUEST, $_GET );
}

// These are manually setup here because it's good to have a gBitUser setup prior to scanPackages
define('LIBERTY_PKG_DIR', 'liberty');
define('LIBERTY_PKG_NAME', 'liberty');
define('LIBERTY_PKG_PATH', BIT_ROOT_PATH . 'liberty/');
define('LIBERTY_PKG_URL', BIT_ROOT_URL . 'liberty/');

define('TEMP_PKG_PATH', BIT_ROOT_PATH . 'temp/');
define('UTIL_PKG_PATH', BIT_ROOT_PATH . 'util/');
define('UTIL_PKG_URL', BIT_ROOT_URL . 'util/');
define('USERS_PKG_PATH', BIT_ROOT_PATH . 'users/');
define('USERS_PKG_URL', BIT_ROOT_URL . 'users/');
define('LANGUAGES_PKG_PATH', BIT_ROOT_PATH . 'languages/');
define('THEMES_PKG_PATH', BIT_ROOT_PATH . 'themes/');

// this is evil stuff and causes hell for us
ini_set ( 'session.use_trans_sid', 'Off' );

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
if( !empty( $gForceAdodb ) ) {
	$dbClass = 'BitDbAdodb';
}
require_once(KERNEL_PKG_PATH . $dbClass.'.php');

// ================== INITATE GLOBAL CLASSES ==================
require_once(KERNEL_PKG_PATH . 'BitCache.php');
global $gBitDb;
$gBitDb = new $dbClass();

require_once(KERNEL_PKG_PATH . 'BitSystem.php');
global $gBitSmarty, $gBitSystem;
$gBitSystem = new BitSystem();

BitSystem::prependIncludePath(UTIL_PKG_PATH . '/');
BitSystem::prependIncludePath(UTIL_PKG_PATH . 'pear/');

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

// num queries has to be global - this this needed? - xing
global $num_queries;
$num_queries = 0;

require_once( THEMES_PKG_PATH."BitThemes.php" );
global $gBitThemes;
$gBitThemes = new BitThemes();

// set various classes global
global $gBitUser, $gTicket, $userlib, $gBitDbType;

if( $gBitSystem->isDatabaseValid() ) {
	$gBitSystem->loadConfig();
	if ($gBitSystem->getConfig('site_output_obzip') == 'y') {
		ob_start ("ob_gzhandler");
	}

	if (empty($gPreScan) || !is_array($gPreScan)) {
		require_once( BIT_ROOT_PATH.'users/bit_setup_inc.php' );
		require_once( BIT_ROOT_PATH.'liberty/bit_setup_inc.php' );
	}

	$host = $gBitSystem->getConfig( 'kernel_server_name', $_SERVER['HTTP_HOST'] );
	if( !defined('BIT_BASE_URI' ) ) {
		define( 'BIT_BASE_URI', 'http://'.$host );
	}

	$gBitSystem->scanPackages();
	// some plugins check for active packages, so we do this *after* package scanning
	global $gLibertySystem;
	$gLibertySystem->scanPlugins();
	$gBitSmarty->assign_by_ref( 'gLibertySystem', $gLibertySystem );

	$gBitSmarty->assign_by_ref("gBitSystem", $gBitSystem);
	// XSS security check
	if( !empty( $_REQUEST['tk'] ) ) {
//		$gBitUser->verifyTicket();
	}

	// setStyle first, in case package decides it wants to reset the style in it's own <package>/bit_setup_inc.php
	$theme = $gBitSystem->getStyle();
	$theme = !empty($theme) ? $theme : 'basic';
	// users_themes='y' is for the entire site, 'h' is just for users homepage and is dealt with on users/index.php
	if( $gBitSystem->getConfig('users_themes') == 'y' ) {
		if ( $gBitUser->isRegistered() && $gBitSystem->isFeatureActive( 'users_preferences' ) ) {
			if( $userStyle = $gBitUser->getPreference('theme') ) {
				$theme = $userStyle;
			}
		}
		if (isset($_COOKIE['tiki-theme'])) {
			$theme = $_COOKIE['tiki-theme'];
		}
	}
	$gBitSystem->setStyle($theme);

	require(KERNEL_PKG_PATH . 'menu_register_inc.php');
	// added for wirtual hosting suport
	if (!isset($bitdomain)) {
		$bitdomain = "";
	} else {
		$bitdomain .= "/";
	}
	$gBitSystem->storeConfig('bitdomain', $bitdomain, KERNEL_PKG_NAME );

	$gBitSmarty->assign("bitdomain", $bitdomain);
	// The votes array stores the votes the user has made
	if (!isset($_SESSION["votes"]))
	{
		$votes = array();
		// session_register("votes");
		$_SESSION["votes"] = $votes;
	}
	// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
	if (!isset($_SERVER['QUERY_STRING']))
	{
		$_SERVER['QUERY_STRING'] = '';
	}
	if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI']))
	{
		$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '/' . $_SERVER['QUERY_STRING'];
	}
	if (isset($_REQUEST['page']))
	{
		$_REQUEST['page'] = strip_tags($_REQUEST['page']);
	}
	global $gHideModules;
	$gBitSmarty->assign_by_ref( 'gHideModules', $gHideModules );

	$ownurl = httpPrefix() . $_SERVER["REQUEST_URI"];
	$parsed = parse_url($_SERVER["REQUEST_URI"]);

	if (!isset($parsed["query"]))
	{
		$parsed["query"] = '';
	}

	parse_str($parsed["query"], $query);
	$father = httpPrefix() . $parsed["path"];

	if (count($query) > 0) {
		$first = 1;
		foreach ($query as $name => $val) {
			if ($first) {
				$first = false;
				$father .= '?' . $name . '=' . $val;
			} else {
				$father .= '&amp;' . $name . '=' . $val;
			}
		}
		$father .= '&amp;';
	} else {
		$father .= '?';
	}
	$ownurl_father = $father;
	$gBitSmarty->assign('ownurl', httpPrefix() . $_SERVER["REQUEST_URI"]);
	// **********  KERNEL  ************
	$gBitSmarty->assign_by_ref("gBitSystemPackages", $gBitSystem->mPackages);

	// check to see if admin has closed the site
    if ( (isset($_SERVER['SCRIPT_URL']) && $_SERVER['SCRIPT_URL'] == USERS_PKG_URL.'validate.php' ) ) $bypass_siteclose_check = 'y';
	if ( $gBitSystem->isFeatureActive( 'site_closed' ) && !$gBitUser->hasPermission('p_access_closed_site') && !isset($bypass_siteclose_check)) {
		$_REQUEST['error'] = $gBitSystem->getConfig('site_closed_msg', 'Site is closed for maintainance; please come back later.');
		include( KERNEL_PKG_PATH . 'error_simple.php' );
		exit;
	}
	// check to see if max server load threshold is enabled
	$site_use_load_threshold = $gBitSystem->getConfig('site_use_load_threshold', 'n');
	// get average server load in the last minute. Keep quiet cause virtual hosts can give perm denied
	if (@is_readable('/proc/loadavg') && $load = file('/proc/loadavg')) {
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

	$https_mode = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
	if ($https_mode) {
		$site_http_port = 80;
		$site_https_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 443;
	} else {
		$site_http_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
		$site_https_port = 443;
	}

	$title = $gBitSystem->getConfig("title", "");
	$site_https_port = $gBitSystem->getConfig('site_https_port', $site_https_port);
	$site_https_prefix = $gBitSystem->getConfig('site_https_prefix', '/');
	// we need this for backwards compatibility - use $gBitSystem->getPrerference( 'max_records' ) if you need it, or else the spanish inquisition will come and poke you with a soft cushion
	$max_records = $gBitSystem->getConfig("max_records", 10);

	/* this stuff isn't really needed here - xing - 2006-02-06
	$site_http_domain = $gBitSystem->getConfig('site_http_domain', '');
	$site_http_port = $gBitSystem->getConfig('site_http_port', $site_http_port);
	$site_http_prefix = $gBitSystem->getConfig('site_http_prefix', '/');
	$site_show_all_modules_always = $gBitSystem->getConfig("site_show_all_modules_always", 'y');
	$modseparateanon = $gBitSystem->getConfig("modseparateanon", 'n');

	$gBitSmarty->assign('site_http_domain', $site_http_domain);
	$gBitSmarty->assign('site_http_port', $site_http_port);
	$gBitSmarty->assign('site_http_prefix', $site_http_prefix);
	$gBitSmarty->assign('site_https_domain', $site_https_domain);
	$gBitSmarty->assign('site_https_port', $site_https_port);
	$gBitSmarty->assign('site_https_prefix', $site_https_prefix);

	$gBitSmarty->assign('kernel_server_name', $gBitSystem->getConfig( 'kernel_server_name', $_SERVER["SERVER_NAME"] ));
	$gBitSmarty->assign('users_count_admin_pageviews', 'y');
	$gBitSmarty->assign('site_show_all_modules_always', $site_show_all_modules_always);
	$gBitSmarty->assign('modseparateanon', $modseparateanon);
	$gBitSmarty->assign('max_records', $max_records);
	$gBitSmarty->assign_by_ref('num_queries', $num_queries);
	$gBitSmarty->assign('site_direct_pagination', 'n');
	*/

	if (ini_get('zlib.output_compression') == 1) {
		$gBitSmarty->assign('gzip', 'Enabled');
	} elseif ($gBitSystem->isFeatureActive('site_output_obzip')) {
		$gBitSmarty->assign('gzip', 'Enabled');
	} else {
		$gBitSmarty->assign('gzip', 'Disabled');
	}

	// can't figure out if we can nuke these
	$gBitSmarty->assign('title', $title);
	$gBitSmarty->assign('site_temp_dir', getTempDir());

	$gBitSmarty->assign_by_ref( 'gBitSystemPrefs', $gBitSystem->mPrefs );

//	======================= HOPEFULLY WE CAN SURVIVE WITHOUT THIS PREFERENCE ASSIGNEMENT STUFF =================
//	$prefs = &$gBitSystem->mPrefs; // TODO $prefs is only for backward compatibility, need to remove entirely
//	foreach ($prefs as $name => $val) {
//		$$name = $val;
//		$gBitSmarty->assign("$name", $val);
//	}
//	============================================================================================================

	/* # not implemented
	$http_basic_auth = $gBitSystem->getConfig('http_basic_auth', '/');
	$gBitSmarty->assign('http_basic_auth',$http_basic_auth);
	*/
	$gBitSmarty->assign('site_https_login', $gBitSystem->getConfig( 'site_https_login' ) );
	$gBitSmarty->assign('site_https_login_required', $gBitSystem->getConfig( 'site_https_login_required' ) );

	$login_url = USERS_PKG_URL . 'validate.php';
	$gBitSmarty->assign('login_url', $login_url);

	if( $gBitSystem->isFeatureActive( 'site_https_login' ) || $gBitSystem->isFeatureActive( 'site_https_login_required' ) )	{
		$site_https_domain = $gBitSystem->getConfig('site_https_domain', '');
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

		if ($site_https_login_required == 'y') {
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

	// if we are interactively translating the website, we force template caching on every page load.
	if( $gBitSystem->isFeatureActive( 'interactive_translation' ) && $gBitUser->hasPermission( 'p_languages_edit' ) ) {
		$gBitSmarty->assign_by_ref( "gBitTranslationHash", $gBitTranslationHash );
	} else {
		// this has to be done since the permission can't be checked in BitLanguage::translate() as it's called too soon by prefilter.tr
		$gBitSystem->setPreference( 'interactive_translation', 'n' );
	}
}

?>
