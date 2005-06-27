<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/setup_inc.php,v 1.5.2.1 2005/06/27 12:49:49 lsces Exp $
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
define( 'BIT_MINOR_VERSION',	'0' );
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
global $gBitSystem;
$gBitSystem = new BitSystem();

global $gPreviewStyle;
$gPreviewStyle = FALSE;
$gBitSystem = &$gBitSystem; // kept for (LOTS OF) backward compatibility.
BitSystem::prependIncludePath(UTIL_PKG_PATH . '/');
BitSystem::prependIncludePath(UTIL_PKG_PATH . 'pear/');

require_once( LANGUAGES_PKG_PATH.'BitLanguage.php' );
global $gBitLanguage;
$gBitLanguage = new BitLanguage();

// pass version information on to smarty
$smarty->assign( 'bitMajorVersion',	BIT_MAJOR_VERSION );
$smarty->assign( 'bitMinorVersion',	BIT_MINOR_VERSION  );
$smarty->assign( 'bitSubVersion',	BIT_SUB_VERSION  );
$smarty->assign( 'bitLevel',		BIT_LEVEL );

$smarty->assign( 'PHP_SELF', $_SERVER['PHP_SELF'] );

require_once(KERNEL_PKG_PATH . 'BitCache.php');
global $gBitUser, $gTicket, $smarty, $userlib, $gBitDbType;

// for PHP<4.2.0
if (!function_exists('array_fill'))
{
	require_once(KERNEL_PKG_PATH . 'array_fill.func.php');
}
// num queries has to be global
global $num_queries;
$num_queries = 0;

// a bit hackish for now, but works.
if( $gBitDb->isValid() ) {

	$gBitSystem->loadPreferences();
	if ($gBitSystem->getPreference('feature_obzip') == 'y') {
		ob_start ("ob_gzhandler");
	}

	if (empty($gPreScan) || !is_array($gPreScan)) {
		require_once( BIT_ROOT_PATH.'users/bit_setup_inc.php' );
		require_once( BIT_ROOT_PATH.'liberty/bit_setup_inc.php' );
	}

	$gBitSystem->scanPackages();
	// some plugins check for active packages, so we do this *after* package scanning
	global $gLibertySystem;
	$gLibertySystem->scanPlugins();

	// setStyle first, in case package decides it wants to reset the style in it's own <package>/bit_setup_inc.php
	$theme = $gBitSystem->getStyle();
	$theme = !empty($theme) ? $theme : 'basic';
	if ($gBitSystem->getPreference('feature_user_theme') == 'y') {
		if (isset($_COOKIE['tiki-theme'])) {
			$theme = $_COOKIE['tiki-theme'];
		}
		if ( $gBitUser->isValid() && $gBitSystem->getPreference('feature_userPreferences') == 'y') {
			$userStyle = $gBitUser->getPreference('theme');
			$theme = !empty($userStyle) ? $userStyle : $theme;
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

	$smarty->assign("bitdomain", $bitdomain);
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
	$smarty->assign_by_ref( 'gHideModules', $gHideModules );

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
	$smarty->assign('ownurl', httpPrefix() . $_SERVER["REQUEST_URI"]);
	// **********  KERNEL  ************
	$smarty->assign_by_ref("gBitSystem", $gBitSystem);
	$smarty->assign_by_ref("gBitSystemPackages", $gBitSystem->mPackages);

	global $gBitLoc;

	$smarty->assign_by_ref("gBitLoc", $gBitLoc);
	// check to see if admin has closed the site
	$site_closed = $gBitSystem->getPreference('site_closed', 'n');
	if ($site_closed == 'y' && !$gBitUser->hasPermission('bit_p_access_closed_site') && !isset($bypass_siteclose_check))
	{
		$site_closed_msg = $gBitSystem->getPreference('site_closed_msg', 'Site is closed for maintainance; please come back later.');
		$url = KERNEL_PKG_URL . 'error_simple.php?error=' . urlencode("$site_closed_msg");
		header('location: ' . $url);
		exit;
	}
	// check to see if max server load threshold is enabled
	$use_load_threshold = $gBitSystem->getPreference('use_load_threshold', 'n');
	// get average server load in the last minute
	if (file_exists('/proc/loadavg') && $load = file('/proc/loadavg'))
	{
		list($server_load) = explode(' ', $load[0]);
		$smarty->assign('server_load', $server_load);
		if ($use_load_threshold == 'y' and !$gBitUser->hasPermission( 'bit_p_access_closed_site' ) and !isset($bypass_siteclose_check))
		{
			$load_threshold = $gBitSystem->getPreference('load_threshold', 3);
			if ($server_load > $load_threshold)
			{
				$site_busy_msg = $gBitSystem->getPreference('site_busy_msg', 'Server is currently too busy; please come back later.');
				$url = KERNEL_PKG_URL . 'error_simple.php?error=' . urlencode($site_busy_msg);
				header('location: ' . $url);
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

	$smarty->assign('http_domain', $http_domain);
	$smarty->assign('http_port', $http_port);
	$smarty->assign('http_prefix', $http_prefix);
	$smarty->assign('https_domain', $https_domain);
	$smarty->assign('https_port', $https_port);
	$smarty->assign('https_prefix', $https_prefix);

	$smarty->assign('title', $title);
	$smarty->assign('feature_server_name', $gBitSystem->getPreference( 'feature_server_name', $_SERVER["SERVER_NAME"] ));
	$smarty->assign('tmpDir', getTempDir());
	$smarty->assign('contact_user', $contact_user);
	$smarty->assign('count_admin_pvs', 'y');
	$smarty->assign('modallgroups', $modallgroups);
	$smarty->assign('modseparateanon', $modseparateanon);
	$smarty->assign('maxRecords', $maxRecords);
	$smarty->assign('direct_pagination', 'n');

	if (ini_get('zlib.output_compression') == 1) {
		$smarty->assign('gzip', 'Enabled');
	} elseif ($gBitSystem->getPreference('feature_obzip') == 'y') {
		$smarty->assign('gzip', 'Enabled');
	} else {
		$smarty->assign('gzip', 'Disabled');
	}

	$smarty->assign_by_ref('num_queries', $num_queries);
	// Assign all prefs to smarty was we are done mucking about for a 1000 lines
	$smarty->assign_by_ref('gBitSystemPrefs', $gBitSystem->mPrefs);
	$prefs = &$gBitSystem->mPrefs; // TODO $prefs is only for backward compatibility, need to remove entirely
	foreach ($prefs as $name => $val)
	{
		$$name = $val;
		$smarty->assign("$name", $val);
	}
	/* # not implemented
		$http_basic_auth = $gBitSystem->getPreference('http_basic_auth', '/');
		$smarty->assign('http_basic_auth',$http_basic_auth);
		*/
	$smarty->assign('https_login', $gBitSystem->getPreference( 'https_login' ) );
	$smarty->assign('https_login_required', $gBitSystem->getPreference( 'https_login_required' ) );

	$login_url = USERS_PKG_URL . 'validate.php';
	$smarty->assign('login_url', $login_url);

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
			$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
			if (!$https_mode) {
				$https_login_url = 'https://' . $https_domain;
				if ($https_port != 443)
					$https_login_url .= ':' . $https_port;

				$https_login_url .= $https_prefix . $login_url;

				if (SID) {
					$https_login_url .= '?' . SID;
				}

				$smarty->assign('login_url', $https_login_url);
			} else {
				// We're already in HTTPS mode, so let's stay there
				$stay_in_ssl_mode = 'on';
			}
		} else {
			$smarty->assign('http_login_url', $http_login_url);
			$smarty->assign('https_login_url', $https_login_url);
			// only show "Stay in SSL checkbox if we're not already in HTTPS mode"
			$show_stay_in_ssl_mode = $https_mode ? 'y' : 'n';
		}
		$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
		$smarty->assign('stay_in_ssl_mode', $stay_in_ssl_mode);
	}

	if( $gBitSystem->isFeatureActive( 'feature_userPreferences' ) && $gBitUser->isRegistered() ) {
		$user_dbl = $gBitUser->getPreference( 'user_dbl', 'y' );

		if ($gBitSystem->isFeatureActive( 'feature_user_theme') ) {
			$userTheme = $gBitUser->getPreference( 'theme' );
			if ($userTheme) {
				$gBitSystem->setStyle($userTheme);
			}
		}
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
		$smarty->assign('popupLinks', $popupLinks);
*/

}

?>
