<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/setup_inc.php,v 1.5.2.32 2005/11/22 18:04:04 squareing Exp $
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

define( 'BIT_MAJOR_VERSION',	'1' );
define( 'BIT_MINOR_VERSION',	'1' );
define( 'BIT_SUB_VERSION',		'0' );
define( 'BIT_LEVEL',			'' ); // 'beta' or 'dev' or 'rc' etc..

define( 'BIT_PKG_PATH', BIT_ROOT_PATH );

// These defines have to happen FIRST because core classes depend on them.
// This means these packages *CANNOT* be renamed
define('KERNEL_PKG_DIR', 'kernel');
define('KERNEL_PKG_NAME', 'kernel');
define('KERNEL_PKG_PATH', BIT_PKG_PATH . 'kernel/');
define('KERNEL_PKG_URL', BIT_ROOT_URL . 'kernel/');

require_once(KERNEL_PKG_PATH . 'preflight_inc.php');

define('LIBERTY_PKG_DIR', 'liberty');
define('LIBERTY_PKG_NAME', 'liberty');
define('LIBERTY_PKG_PATH', BIT_PKG_PATH . 'liberty/');
define('LIBERTY_PKG_URL', BIT_ROOT_URL . 'liberty/');

// These are manually setup here because it's good to have a gBitUser setup prior to scanPackages
define('TEMP_PKG_PATH', BIT_PKG_PATH . 'temp/');
define('UTIL_PKG_PATH', BIT_PKG_PATH . 'util/');
define('UTIL_PKG_URL', BIT_ROOT_URL . 'util/');
define('USERS_PKG_PATH', BIT_PKG_PATH . 'users/');
define('USERS_PKG_URL', BIT_ROOT_URL . 'users/');

define('LANGUAGES_PKG_PATH', BIT_ROOT_PATH . 'languages/');
define('BIT_LANG_PATH', LANGUAGES_PKG_PATH . 'lang/');

define('BIT_MODULES_PATH', BIT_ROOT_PATH . 'modules/');
define('BIT_TEMP_PATH', BIT_ROOT_PATH . 'temp/');
define('BIT_THEME_PATH', BIT_ROOT_PATH . 'themes/');

// this is evil stuff and causes hell for us
ini_set ( 'session.use_trans_sid', 'Off' );

// Force a global ADODB db object so all classes share the same connection
require_once(KERNEL_PKG_PATH . 'BitDb.php');
global $gBitDb;
$gBitDb = new BitDb();

require_once(KERNEL_PKG_PATH . 'BitSystem.php');
global $gRefreshSitePrefs;
$gRefreshSitePrefs = FALSE;
global $gBitSmarty, $gBitSystem;
$gBitSystem = new BitSystem();

// array used to load stuff using <body onload="">
global $gBodyOnload;
$gBitSmarty->assign_by_ref( 'gBodyOnload', $gBodyOnload = array() );

global $gPreviewStyle;
$gPreviewStyle = FALSE;
BitSystem::prependIncludePath(UTIL_PKG_PATH . '/');
BitSystem::prependIncludePath(UTIL_PKG_PATH . 'pear/');

require_once( UTIL_PKG_PATH.'phpsniff/phpSniff.class.php' );
global $gSniffer;
$gSniffer = new phpSniff;
$gBitSmarty->assign_by_ref( 'browserInfo', $gSniffer->_browser_info );

require_once( LANGUAGES_PKG_PATH.'BitLanguage.php' );
global $gBitLanguage;
$gBitLanguage = new BitLanguage();

// pass version information on to smarty
$gBitSmarty->assign( 'bitMajorVersion',	BIT_MAJOR_VERSION );
$gBitSmarty->assign( 'bitMinorVersion',	BIT_MINOR_VERSION  );
$gBitSmarty->assign( 'bitSubVersion',	BIT_SUB_VERSION  );
$gBitSmarty->assign( 'bitLevel',		BIT_LEVEL );

$gBitSmarty->assign( 'PHP_SELF', $_SERVER['PHP_SELF'] );

require_once(KERNEL_PKG_PATH . 'BitCache.php');
global $gBitUser, $gTicket, $gBitSmarty, $userlib, $gBitDbType;

// for PHP<4.2.0
if (!function_exists('array_fill'))
{
	require_once(KERNEL_PKG_PATH . 'array_fill.func.php');
}
// num queries has to be global
global $num_queries;
$num_queries = 0;

if( $gBitSystem->isDatabaseValid() ) {

	$gBitSystem->loadPreferences();
	if ($gBitSystem->getPreference('feature_obzip') == 'y') {
		ob_start ("ob_gzhandler");
	}

	if (empty($gPreScan) || !is_array($gPreScan)) {
		require_once( BIT_ROOT_PATH.'users/bit_setup_inc.php' );
		require_once( BIT_ROOT_PATH.'liberty/bit_setup_inc.php' );
	}

	$host = $gBitSystem->getPreference( 'feature_server_name', $_SERVER['HTTP_HOST'] );
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
		$gBitUser->verifyTicket();
	}

	// setStyle first, in case package decides it wants to reset the style in it's own <package>/bit_setup_inc.php
	$theme = $gBitSystem->getStyle();
	$theme = !empty($theme) ? $theme : 'basic';
	// feature_user_theme='y' is for the entire site, 'h' is just for users homepage and is dealt with on users/index.php
	if( $gBitSystem->getPreference('feature_user_theme') == 'y' ) {
		if (isset($_COOKIE['tiki-theme'])) {
			$theme = $_COOKIE['tiki-theme'];
		}
		if ( $gBitUser->isRegistered() && $gBitSystem->isFeatureActive( 'feature_userPreferences' ) ) {
			if( $userStyle = $gBitUser->getPreference('theme') ) {
				$theme = $userStyle;
			}
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
	$gBitSystem->storePreference('bitdomain', $bitdomain);

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

	if (count($query) > 0)
	{
		$first = 1;
		foreach ($query as $name => $val)
		{
			if ($first)
			{
				$first = false;
				$father .= '?' . $name . '=' . $val;
			}
			else
			{
				$father .= '&amp;' . $name . '=' . $val;
			}
		}
		$father .= '&amp;';
	}
	else
	{
		$father .= '?';
	}
	$ownurl_father = $father;
	$gBitSmarty->assign('ownurl', httpPrefix() . $_SERVER["REQUEST_URI"]);
	// **********  KERNEL  ************
	$gBitSmarty->assign_by_ref("gBitSystemPackages", $gBitSystem->mPackages);

	// check to see if admin has closed the site
	if ( $gBitSystem->isFeatureActive( 'site_closed' ) && !$gBitUser->hasPermission('bit_p_access_closed_site') && !isset($bypass_siteclose_check) && (isset($_SERVER['SCRIPT_URL']) && $_SERVER['SCRIPT_URL'] != USERS_PKG_URL.'validate.php' )) {
		$_REQUEST['error'] = $gBitSystem->getPreference('site_closed_msg', 'Site is closed for maintainance; please come back later.');
		include( KERNEL_PKG_PATH . 'error_simple.php' );
		exit;
	}
	// check to see if max server load threshold is enabled
	$use_load_threshold = $gBitSystem->getPreference('use_load_threshold', 'n');
	// get average server load in the last minute
	if (@is_readable('/proc/loadavg') && $load = file('/proc/loadavg')) {
		list($server_load) = explode(' ', $load[0]);
		$gBitSmarty->assign('server_load', $server_load);
		if ($use_load_threshold == 'y' && !$gBitUser->hasPermission( 'bit_p_access_closed_site' ) && !isset($bypass_siteclose_check)) {
			$load_threshold = $gBitSystem->getPreference('load_threshold', 3);
			if ($server_load > $load_threshold) {
				$_REQUEST['error'] = $gBitSystem->getPreference('site_busy_msg', 'Server is currently too busy; please come back later.');
				include( KERNEL_PKG_PATH . 'error_simple.php' );
				exit;
			}
		}
	}

	$https_mode = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
	if ($https_mode)
	{
		$http_port = 80;
		$https_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 443;
	}
	else
	{
		$http_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
		$https_port = 443;
	}

	$title = $gBitSystem->getPreference("title", "");
	$contact_user = $gBitSystem->getPreference('contact_user', 'admin');
	$http_domain = $gBitSystem->getPreference('http_domain', '');
	$http_port = $gBitSystem->getPreference('http_port', $http_port);
	$http_prefix = $gBitSystem->getPreference('http_prefix', '/');
	$https_domain = $gBitSystem->getPreference('https_domain', '');
	$https_port = $gBitSystem->getPreference('https_port', $https_port);
	$https_prefix = $gBitSystem->getPreference('https_prefix', '/');
	$modallgroups = $gBitSystem->getPreference("modallgroups", 'y');
	$modseparateanon = $gBitSystem->getPreference("modseparateanon", 'n');
	$maxRecords = $gBitSystem->getPreference("maxRecords", 10);

	$gBitSmarty->assign('http_domain', $http_domain);
	$gBitSmarty->assign('http_port', $http_port);
	$gBitSmarty->assign('http_prefix', $http_prefix);
	$gBitSmarty->assign('https_domain', $https_domain);
	$gBitSmarty->assign('https_port', $https_port);
	$gBitSmarty->assign('https_prefix', $https_prefix);

	$gBitSmarty->assign('title', $title);
	$gBitSmarty->assign('feature_server_name', $gBitSystem->getPreference( 'feature_server_name', $_SERVER["SERVER_NAME"] ));
	$gBitSmarty->assign('tmpDir', getTempDir());
	$gBitSmarty->assign('contact_user', $contact_user);
	$gBitSmarty->assign('count_admin_pvs', 'y');
	$gBitSmarty->assign('modallgroups', $modallgroups);
	$gBitSmarty->assign('modseparateanon', $modseparateanon);
	$gBitSmarty->assign('maxRecords', $maxRecords);
	$gBitSmarty->assign('direct_pagination', 'n');

	if (ini_get('zlib.output_compression') == 1) {
		$gBitSmarty->assign('gzip', 'Enabled');
	} elseif ($gBitSystem->getPreference('feature_obzip') == 'y') {
		$gBitSmarty->assign('gzip', 'Enabled');
	} else {
		$gBitSmarty->assign('gzip', 'Disabled');
	}

	$gBitSmarty->assign_by_ref('num_queries', $num_queries);
	// Assign all prefs to smarty was we are done mucking about for a 1000 lines
	$gBitSmarty->assign_by_ref('gBitSystemPrefs', $gBitSystem->mPrefs);
	$prefs = &$gBitSystem->mPrefs; // TODO $prefs is only for backward compatibility, need to remove entirely
	foreach ($prefs as $name => $val) {
		$$name = $val;
		$gBitSmarty->assign("$name", $val);
	}
	/* # not implemented
		$http_basic_auth = $gBitSystem->getPreference('http_basic_auth', '/');
		$gBitSmarty->assign('http_basic_auth',$http_basic_auth);
		*/
	$gBitSmarty->assign('https_login', $gBitSystem->getPreference( 'https_login' ) );
	$gBitSmarty->assign('https_login_required', $gBitSystem->getPreference( 'https_login_required' ) );

	$login_url = USERS_PKG_URL . 'validate.php';
	$gBitSmarty->assign('login_url', $login_url);

	if( $gBitSystem->isFeatureActive( 'https_login' ) || $gBitSystem->isFeatureActive( 'https_login_required' ) )	{
		$http_login_url = 'http://' . $http_domain;

		if ($http_port != 80)
			$http_login_url .= ':' . $http_port;

		$http_login_url .= $http_prefix . $gBitSystem->getDefaultPage();

		if (SID)
			$http_login_url .= '?' . SID;

		$edit_data = htmlentities(isset($_REQUEST["edit"]) ? $_REQUEST["edit"] : '', ENT_QUOTES);

		$https_login_url = 'https://' . $https_domain;

		if ($https_port != 443)
			$https_login_url .= ':' . $https_port;

		$https_login_url .= $https_prefix . $gBitSystem->getDefaultPage();

		if (SID)
			$https_login_url .= '?' . SID;

		$stay_in_ssl_mode = isset($_REQUEST['stay_in_ssl_mode']) ? $_REQUEST['stay_in_ssl_mode'] : '';

		if ($https_login_required == 'y') {
			// only show "Stay in SSL checkbox if we're not already in HTTPS mode"
			$show_stay_in_ssl_mode = !$https_mode ? 'y' : 'n';
			$gBitSmarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
			if (!$https_mode) {
				$https_login_url = 'https://' . $https_domain;
				if ($https_port != 443)
					$https_login_url .= ':' . $https_port;

				$https_login_url .= $https_prefix . $login_url;

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

	/* SPIDERRKILL - i think everything below here is not fully implemented or deprecated

		//Check for an update of dynamic vars
		if(isset($bit_p_edit_dynvar) && $gBitUser->hasPermission( 'bit_p_edit_dynvar' )) {
			if(isset($_REQUEST['_dyn_update'])) { echo "****";
				foreach($_REQUEST as $name => $value) {
					if(substr($name,0,4)=='dyn_' and $name!='_dyn_update') {
						$gBitSystem->update_dynamic_variable(substr($name,4),$_REQUEST[$name]);
					}
				}
			}
		}

		if($gBitSystem->getPreference('feature_phpopentracker') == 'y') {

			include_once(BIT_PKG_PATH.'phpOpenTracker/phpOpenTracker.php');
			// log access
			phpOpenTracker::log();
		}

		$popupLinks = $gBitSystem->getPreference("popupLinks", 'n');
		$gBitSmarty->assign('popupLinks', $popupLinks);
*/

}

?>
