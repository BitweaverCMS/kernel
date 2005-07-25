<?php
/**
* @package kernel
* @author spider <spider@steelsun.com>
* @version $Revision: 1.10 $
*/
// +----------------------------------------------------------------------+
// | PHP version 4.??
// +----------------------------------------------------------------------+
// | Copyright (c) 2005 bitweaver.org
// +----------------------------------------------------------------------+
// | Copyright (c) 2004-2005, Christian Fowler, et. al.
// | All Rights Reserved. See copyright.txt for details and a complete list of authors.
// | Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
// |
// | For comments, please use PEAR documentation standards!!!
// | -> see http://pear.php.net/manual/en/standards.comments.php
// |    and http://www.phpdoc.org/
// +----------------------------------------------------------------------+
// | Authors: spider <spider@steelsun.com>
// +----------------------------------------------------------------------+
// $Id: BitSystem.php,v 1.10 2005/07/25 20:02:08 squareing Exp $

/**
 * required setup
 */
require_once(KERNEL_PKG_PATH . 'BitBase.php');
require_once(KERNEL_PKG_PATH . 'BitDate.php');
require_once(KERNEL_PKG_PATH . 'BitSmarty.php');

define('DEFAULT_PACKAGE', 'kernel');
define('CENTER_COLUMN', 'c');
define('HOMEPAGE_LAYOUT', 'home');

/**
 * kernel::BitSystem
 *
 * Purpose:
 *
 *     This is the main system class that does the work of seeing Tiki has an
 * 	operable environment and has methods for modifying that environment.
 *
 * 	Currently gBitSystem derives from this class for backward compatibility sake.
 * 	Ultimate goal is to put system code from BitBase here, and base code from
 * 	gBitSystem (code that ALL features need) into BitBase and code gBitSystem that
 * 	is Package specific should be moved into that package
 *
 * @author spider <spider@steelsun.com>
 * @version $Revision: 1.10 $
 * @package kernel
 * @subpackage BitSystem
 */
class BitSystem extends BitBase
{	/**
	* @package BitSystem
	*/
	// === properties
	/**
	* * Array of  *
	*/
	var $mAppMenu;
	var $mPackages;
	var $mLayout;
	var $mStyle;
	var $mActivePackage;
	var $mInstallModules = array();
	// Used by packages to register notification events that can be subscribed to.
	var $mNotifyEvents = array();
	/**
	* Used to store contents of tiki_preferences
	* @private
	*/
	var $mPrefs;
	/**
	* Used to monitor if ::regiserPackage() was called. This is to prevent duplicate package registration for packages that have directory names different from their package name.
	* @private
	*/
	var $mRegisterCalled;
	// >>>
	// === BitSystem constructor
	/**
	* base constructor, auto assigns member db variable
	*
	* @access public
	*/
	// Constructor receiving a PEAR::Db database object.
	function BitSystem()
	{
		// Call DB constructor which will create the database member variable
		BitBase::BitBase();
		// Critical Preflight Checks
		$this->checkEnvironment();

		$this->mAppMenu = array();
		$this->mTimer = new TikiTimer();
		$this->mTimer->start();

		$this->initSmarty();
		$this->mRegisterCalled = FALSE;
	}
	// >>>
	// === initSmarty
	/**
	* Define and load Smarty components
	*
	* @param none $
	* @return none
	* @access private
	*/
	function initSmarty()
	{
		global $bitdomain, $_SERVER;

		// Set the separator for PHP generated tags to be &amp; instead of &
		// This is necessary for XHTML compliance
		ini_set("arg_separator.output", "&amp;");
		// Remove automatic quotes added to POST/COOKIE by PHP
		if (get_magic_quotes_gpc())
		{
			foreach ($_REQUEST as $k => $v)
			{
				if (!is_array($_REQUEST[$k])) $_REQUEST[$k] = stripslashes($v);
			}
		}

		if (!isset($bitdomain))
		{
			$bitdomain = "";
		}

		global $gBitSmarty;
		// make sure we only create one BitSmarty
		if( !is_object( $gBitSmarty ) ) {
			$gBitSmarty = new BitSmarty();
			// set the default handler
			$gBitSmarty->load_filter('pre', 'tr');
			// $gBitSmarty->load_filter('output','trimwhitespace');
			if (isset($_REQUEST['highlight']))
			{
				$gBitSmarty->load_filter('output', 'highlight');
			}
		}
	}

	function loadPreferences($pPackage = null)
	{
		$queryVars = array();
		$whereClause = '';

		if ($pPackage) {
			array_push($queryVars, $pPackage);
			$whereClause = ' WHERE `package`=? ';
		}

		if ( empty( $this->mPrefs ) ) {
			$query = "SELECT `name` ,`value` FROM `" . BIT_DB_PREFIX . "tiki_preferences` " . $whereClause;
			$rs = $this->query($query, $queryVars, -1, -1 );
			if ($rs) {
				while (!$rs->EOF) {
					$this->mPrefs[$rs->fields['name']] = $rs->fields['value'];
					$rs->MoveNext();
				}
			}
		}
		return count( $this->mPrefs );
	}

	// <<< storePreference
	/**
	* Tiki needs lots of settings just to operate.
	* loadPreferences assigns itself the default preferences, then loads just the differences from the database.
	* In storePreference (and only when storePreference is called) we make a second copy of defaults to see if
	* preferences you are changing is different from the default.
	* if it is the same, don't store it!
	* So instead updating the whole prefs table, only updat "delta" of the changes delta from defaults.
	*
	* @access public
	**/
	function storePreference( $name, $value, $pPackageName=NULL ) {
		global $gBitSystem;
		global $gMultisites;
		global $gRefreshSitePrefs;
		global $bitdomain;

		if (file_exists(TEMP_PKG_PATH."templates_c/" . $bitdomain . "preferences.php")) {
			@unlink (TEMP_PKG_PATH."templates_c/" . $bitdomain . "preferences.php");
		}

		// store the pref if we have a value _AND_ it is different from the default
		if( ( empty( $this->mPrefs[$name] ) || ( $this->mPrefs[$name] != $value ) ) ) {
			// store the preference in multisites, if used
			if( !empty( $gMultisites->mMultisiteId ) && isset( $gMultisites->mPrefs[$name] ) ) {
				$query = "UPDATE `".BIT_DB_PREFIX."tiki_multisite_preferences` SET `value`=? WHERE `multisite_id`=? AND `name`=?";
				$result = $this->query( $query, array( empty( $value ) ? '' : $value, $gMultisites->mMultisiteId, $name ) );
			} else {
				$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_preferences` WHERE `name`=?";
				$result = $this->query( $query, array( $name ) );
				if( isset( $value ) ) {
					$query = "INSERT INTO `".BIT_DB_PREFIX."tiki_preferences`(`name`,`value`,`package`) VALUES (?,?,?)";
					$result = $this->query( $query, array( $name, $value, strtolower( $pPackageName ) ) );
				}
			}

			// Force the ADODB cache to flush
			$this->mCacheTime = 0;
			$this->loadPreferences();
			$this->mCacheTime = BIT_QUERY_CACHE_TIME;
		}

		$this->mPrefs[$name] = $value;

		$gRefreshSitePrefs = TRUE;
		return true;
	}
	// >>>

	function getPreference($name, $default = '') {
		if( empty( $this->mPrefs ) ) {
			$this->loadPreferences();
		}
		return( empty( $this->mPrefs[$name] ) ? $default : $this->mPrefs[$name] );
	}

	// <<< expungePackagePreferences
	/**
	* Delete all prefences for the given package
	*
	* @access public
	**/
	function expungePackagePreferences( $pPackageName ) {
		if( !empty( $pPackageName ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."tiki_preferences` WHERE `package`=?";
			$result = $this->query( $query, array( strtolower( $pPackageName ) ) );
			// let's force a reload of the prefs
			unset( $this->mPrefs );
			$this->loadPreferences();
		}
	}
	// >>>


	// >>>
	// === hasValidSenderEmail
	/**
	* Determines if this site has a legitimate sender address set.
	*
	* @param  $mid the name of the template for the page content
	* @access public
	*/
	function hasValidSenderEmail( $pSenderEmail=NULL ) {
		if( empty( $pSenderEmail ) ) {
			$pSenderEmail = $this->getPreference( 'sender_email' );
		}
		return( !empty( $pSenderEmail ) && !preg_match( '/.*localhost$/', $pSenderEmail ) );
	}
	// >>>

	// >>>
	// === getErrorEmail
	/**
	* Smartly determines where error emails should go
	*
	* @access public
	*/
	function getErrorEmail() {
		if( defined('ERROR_EMAIL') ) {
			$ret = ERROR_EMAIL;
		} elseif( $this->getPreference( 'sender_email' ) ) {
			$ret = $this->getPreference( 'sender_email' );
		} elseif( !empty( $_SERVER['SERVER_ADMIN'] ) ) {
			$ret = $_SERVER['SERVER_ADMIN'];
		} else {
			$ret = 'root@localhost';
		}
	}
	// >>>


	// >>>
	// === sendEmail
	/**
	* centralized function for send emails
	*
	* @param  $mid the name of the template for the page content
	* @access public
	*/
	function sendEmail( $pMailHash ) {
		$extraHeaders = '';
		if( $this->getPreference( 'bcc_email' ) ) {
			$extraHeaders = "Bcc: ".$this->getPreference( 'bcc_email' )."\r\n";
		}
		if( !empty( $pMailHash['Reply-to'] ) ) {
			$extraHeaders = "Reply-to: ".$pMailHash['Reply-to']."\r\n";
		}

		mail($pMailHash['email'],
			$pMailHash['subject'].' '.$_SERVER["SERVER_NAME"],
			$pMailHash['body'],
			"From: ".$this->getPreference( 'sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n$extraHeaders"
		);
	}
	// >>>

	// >>>
	// === display
	/**
	* Display the main page template
	*
	* @param  $mid the name of the template for the page content
	* @access public
	*/
	function display( $pMid, $pBrowserTitle=NULL ) {
		global $gBitSmarty;
		$gBitSmarty->verifyCompileDir();

		header( 'Content-Type: text/html; charset=utf-8' );
		if( !empty( $pBrowserTitle ) ) {
			$this->setBrowserTitle( $pBrowserTitle );
		}
		if( $pMid == 'error.tpl' ) {
			$this->setBrowserTitle( !empty( $pBrowserTitle ) ? $pBrowserTitle : tra( 'Error' ) );
			$pMid = 'bitpackage:kernel/error.tpl';
		}
		$this->preDisplay( $pMid );
		$gBitSmarty->assign( 'mid', $pMid );
		$gBitSmarty->assign( 'page', !empty( $_REQUEST['page'] ) ? $_REQUEST['page'] : NULL );
		$gBitSmarty->display( 'bitpackage:kernel/bitweaver.tpl' );
		$this->postDisplay( $pMid );
	}
	// >>>
	// === preDisplay
	/**
	* Take care of any processing that needs to happen just before the template is displayed
	*
	* @param none $
	* @access private
	*/
	function preDisplay($pMid)
	{
		global $gCenterPieces, $fHomepage, $gBitSmarty, $gBitUser, $gBitLoc, $gPreviewStyle;
		// setup our theme style and check if a preview theme has been picked
		if( $gPreviewStyle !== FALSE ) {
			$this->setStyle( $gPreviewStyle );
		}
		if (empty($gBitLoc['styleSheet'])) {
			$gBitLoc['styleSheet'] = $this->getStyleCss();
		}
		$gBitLoc['headerIncFiles'] = $this->getHeaderIncFiles();
		$gBitLoc['browserStyleSheet'] = $this->getBrowserStyleCss();
		$gBitLoc['customStyleSheet'] = $this->getCustomStyleCss();
		$gBitLoc['altStyleSheets'] = $this->getAltStyleCss();
		$gBitLoc['THEMES_STYLE_URL'] = $this->getStyleUrl();
		$gBitLoc['JSCALENDAR_PKG_URL'] = UTIL_PKG_URL.'jscalendar/';
		$gBitSmarty->assign_by_ref("gBitLoc", $gBitLoc);
		// dont forget to assign slideshow stylesheet if we are viewing page as slideshow
//		$gBitSmarty->assign('slide_style', $this->getStyleCss("slide_style"));

		$this->loadLayout(($this->getPreference('feature_user_layout') == 'h' && !empty($fHomepage)) ? $fHomepage : ($this->getPreference('feature_user_layout') == 'y' ? $gBitUser->mUsername : null));
		if ($pMid == 'bitpackage:kernel/dynamic.tpl')
		{
			$gBitSmarty->assign_by_ref('gCenterPieces', $gCenterPieces);
		}
		else
		{
			// we don't want to render the centers if we aren't on a dynamic page
			unset($this->mLayout['c']);
		}

		require(KERNEL_PKG_PATH . 'menu_register_inc.php');
		require_once(KERNEL_PKG_PATH . 'modules_inc.php');

		/* force the session to close *before* displaying. Why? Note this very important comment from http://us4.php.net/exec
			edwin at bit dot nl
			23-Jan-2002 04:47
				If you are using sessions and want to start a background process, you might
				have the following problem:
				The first time you call your script everything goes fine, but when you call it again
				and the process is STILL running, your script seems to "freeze" until you kill the
				process you started the first time.

				You'll have to call session_write_close(); to solve this problem. It has something
				to do with the fact that only one script/process can operate at once on a session.
				(others will be lockedout until the script finishes)

				I don't know why it somehow seems to be influenced by background processes,
				but I tried everything and this is the only solution. (i had a perl script that
				"daemonized" it's self like the example in the perl manuals)

				Took me a long time to figure out, thanks ian@virtisp.net! :-)

			... and a similar issue can happen for very long display times.
		*/
		session_write_close();
	}

	// >>>
	// === getHeaderIncFiles
	/**
	* scan packages for <pkg>/templates/header_inc.tpl files
	*
	* @param none $
	* @access private
	* @return array of paths to existing header_inc.tpl files
	*/
	function getHeaderIncFiles() {
		global $gBitSystem, $gBitLoc;
		foreach( $gBitSystem->mPackages as $package => $info ) {
			$file = $info['path'].'templates/header_inc.tpl';
			if( is_readable( $file ) ) {
				$ret[] = $file;
			}
		}
		return !empty( $ret ) ? $ret : '';
	}

	// >>>
	// === postDisplay
	/**
	* Take care of any processing that needs to happen just after the template is displayed
	*
	* @param none $
	* @access private
	*/
	function postDisplay($pMid)
	{
	}
	// >>>
	// === setHelpInfo
	/**
	* Set the smarty variables needed to display the help link for a page.
	*
	* @param  $package Package Name
	* @param  $context Context of the help within the package
	* @param  $desc Description of the help link (not the help itself)
	* @access private
	*/
	function setHelpInfo($package, $context, $desc)
	{
		global $gBitSmarty;
		$gBitSmarty->assign('TikiHelpInfo', array('URL' => 'http://doc.bitweaver.org/wiki/index.php?page=' . $package . $context , 'Desc' => $desc));
	}
	// >>>

	// === isPackageActive
	/**
	* check's if the current package is active and being used.
	* The die code was duplicated _EVERYWHERE_ so here is an easy template to cut that down.
	* It will verify that the given package is active or it will display the error template and die()
	* @param $pPackageName the name of the package to test
	* @param $pdie force the script to immediately die if pPackageName is not active
	* @return none
	* @access public
	*
	* @param $pKey hash key
	*/
	function isPackageActive( $pPackageName )
	{
		$ret = FALSE;
		if (defined( (strtoupper( $pPackageName ).'_PKG_NAME') ) ) {
			$name = strtolower( @constant( (strtoupper( $pPackageName ).'_PKG_NAME') ) );
			if( $name ) {
				// we have migrated the old tikiwiki feature_<package> to package_<package> just for (de)activating packages
				$ret = ($this->getPreference('package_'.$name) == 'y');
			}
		}

		return( $ret );
	}


	// === verifyPackage
	/**
	* It will verify that the given package is active or it will display the error template and die()
	* @param $pPackageName the name of the package to test
	* @return none
	*
	* @param  $pKey hash key
	* @access public
	*/
	function verifyPackage( $pPackageName )
	{
		if( !$this->isPackageActive( $pPackageName ) ) {
			$this->fatalError( tra("This package is disabled").": package_$pPackageName" );
		}

		return( TRUE );
	}


	// === getPermissionInfo
	/**
	* It will get information about a permissions
	* @param $pPermission value of a given permission
	* @return none
	* @access public
	*/
	function getPermissionInfo( $pPermission = NULL, $pPackageName = NULL ) {
		$ret = NULL;
		$bindVars = array();
		$sql = 'SELECT * FROM `'.BIT_DB_PREFIX.'users_permissions` ';
		if( !empty( $pPermission ) ) {
			$sql .= ' WHERE `perm_name`=? ';
			array_push( $bindVars, $pPermission );
		} elseif ( !empty( $pPackageName ) ) {
			$sql .= ' WHERE `package` = ? ';
			array_push( $bindVars, substr($pPackageName,0,100));
		}
		$ret = $this->getAssoc( $sql, $bindVars );
		return $ret;
	}

	// === verifyPermission
	/**
	* This code was duplicated _EVERYWHERE_ so here is an easy template to cut that down.
	* It will verify if a given user has a given $permission and if not, it will display the error template and die()
	* @param $pPermission value of a given permission
	* @return none
	* @access public
	*/
	function verifyPermission( $pPermission, $pMsg = NULL )
	{
		global $gBitSmarty, $gBitUser, ${$pPermission};
		if( $gBitUser->hasPermission($pPermission) ) {
			return true;
		} else {
			$this->fatalPermission( $pPermission, $pMsg );
		}
	}

	function fatalPermission( $pPermission, $pMsg=NULL ) {
		global $gBitUser, $gBitSmarty;
		if( empty( $pMsg ) ) {
			$permDesc = $this->getPermissionInfo( $pPermission );
			$pMsg = "You do not have the required permissions ";
			if( !empty( $permDesc[$pPermission]['perm_desc'] ) ) {
				if( preg_match( '/administrator,/i', $permDesc[$pPermission]['perm_desc'] ) ) {
					$pMsg .= preg_replace( '/^administrator, can/i', ' to ', $permDesc[$pPermission]['perm_desc'] );
				} else {
					$pMsg .= preg_replace( '/^can /i', ' to ', $permDesc[$pPermission]['perm_desc'] );
				}
			}
		}
		if( !$gBitUser->isRegistered() ) {
			$pMsg .= '</p><p>You must be logged in. Please <a href="'.USERS_PKG_URL.'login.php">login</a> or <a href="'.USERS_PKG_URL.'register.php">register</a>.';
			$gBitSmarty->assign( 'template', 'bitpackage:users/login_inc.tpl' );
		}
		$gBitSmarty->assign( 'msg', tra( $pMsg ) );
		$this->display( "error.tpl" );
		die;
	}

	/**
	* This code was duplicated _EVERYWHERE_ so here is an easy template to cut that down.
	* It will verify if a given user has a given $permission and if not, it will display the error template and die()
	* @param $pPermission value of a given permission
	* @return none
	* @access public
	*/
	function confirmDialog( $pFormHash, $pMsg )
	{
		global $gBitSmarty;
		if( !empty( $pMsg ) ) {
			if( empty( $pParamHash['cancel_url'] ) ) {
				$gBitSmarty->assign( 'backJavascript', 'onclick="history.back();"' );
			}
			if( !empty( $pFormHash['input'] ) ) {
				$gBitSmarty->assign( 'inputFields', $pFormHash['input'] );
				unset( $pFormHash['input'] );
			}
			$gBitSmarty->assign( 'msgFields', $pMsg );
			$gBitSmarty->assign_by_ref( 'hiddenFields', $pFormHash );
			$this->display( 'bitpackage:kernel/confirm.tpl' );
			die;
		}
	}

	// === isFeatureActive
	/**
	* check's if the specfied feature is active
	*
	* @param  $pKey hash key
	* @return none
	* @access public
	*/
	function isFeatureActive( $pFeatureName )
	{
		$ret = FALSE;
		if( $pFeatureName ) {
			$ret = ($this->getPreference($pFeatureName) == 'y');
		}

		return( $ret );
	}


	// === verifyFeature
	/**
	* It will verify that the given feature is active or it will display the error template and die()
	* @param $pFeatureName the name of the package to test
	* @return none
	* @access public
	*
	* @param  $pKey hash key
	*/
	function verifyFeature( $pFeatureName )
	{
		if( !$this->isFeatureActive( $pFeatureName ) ) {
			$this->fatalError( tra("This feature is disabled").": $pFeatureName" );
		}

		return( TRUE );
	}


	// === registerPackage
	/**
	* Define name, location and url DEFINE's
	*
	* @param  $pKey hash key
	* @return none
	* @access public
	*/
	function registerPackage($pPackageName, $pPackagePath, $pActivatable=TRUE )
	{
		$this->mRegisterCalled = TRUE;
		if( empty( $this->mPackages ) ) {
			$this->mPackages = array();
		}
		global $gBitLoc;
		$pkgName = str_replace( ' ', '_', strtoupper( $pPackageName ) );
		$pkgNameKey = strtolower( $pkgName );

		// Define <PACKAGE>_PKG_PATH
		$pkgDefine = $pkgName.'_PKG_PATH';
		if (!defined($pkgDefine))
		{
			define($pkgDefine, $pPackagePath);
		}
		$gBitLoc[$pkgDefine] = $pPackagePath;
		$this->mPackages[$pkgNameKey]['url']  = BIT_ROOT_URL . basename( $pPackagePath ) . '/';
		$this->mPackages[$pkgNameKey]['path']  = BIT_ROOT_PATH . basename( $pPackagePath ) . '/';

		// Define <PACKAGE>_PKG_URL
		$pkgDefine = $pkgName.'_PKG_URL';
		if (!defined($pkgDefine))
		{
			define($pkgDefine, BIT_ROOT_URL . basename( $pPackagePath ) . '/');
		}
		$gBitLoc[$pkgDefine] = BIT_ROOT_URL . basename( $pPackagePath ) . '/';

		// Define <PACKAGE>_PKG_NAME
		$pkgDefine = $pkgName.'_PKG_NAME';
		if (!defined($pkgDefine))
		{
			define($pkgDefine, $pPackageName);
			$this->mPackages[$pkgNameKey]['activatable'] = $pActivatable;
		}
		$this->mPackages[$pkgNameKey]['name'] = $pPackageName;

		// Define <PACKAGE>_PKG_DIR
		$pkgDefine = $pkgName.'_PKG_DIR';
		if (!defined($pkgDefine))
		{
			define($pkgDefine, basename( $pPackagePath ));
		}
		$this->mPackages[$pkgNameKey]['dir'] = basename( $pPackagePath );

		// Define the package we are currently in
		// I tried strpos instead of preg_match here, but it didn't like strings that begin with slash?! - spiderr
		if( !defined('ACTIVE_PACKAGE') && (isset($_SERVER['ACTIVE_PACKAGE'] ) || preg_match( '/\/'.$this->mPackages[$pkgNameKey]['dir'].'\//', $_SERVER['PHP_SELF'] ) || preg_match( '/\/' . $pPackageName . '\//', $_SERVER['PHP_SELF'] )) )
		{
			if( isset($_SERVER['ACTIVE_PACKAGE'] ) ) {
				// perhaps the webserver told us the active package (probably because of mod_rewrites)
				$pPackageName = $_SERVER['ACTIVE_PACKAGE'];
			}
			define('ACTIVE_PACKAGE', $pPackageName);
			$gBitLoc['ACTIVE_PACKAGE'] = $pPackageName;
			$this->mActivePackage = $pPackageName;
		}
	}
	// >>>
	// === registerAppMenu
	/**
	* Define and load Smarty components
	*
	* @param  $pKey hash key
	* @return none
	* @access public
	*/
	function registerAppMenu($pKey, $pMenuTitle, $pTitleUrl, $pMenuTemplate, $pAdminPanel = false)
	{
		$this->mAppMenu[strtolower($pKey)] = array('title' => $pMenuTitle,
			'titleUrl' => $pTitleUrl,
			'template' => $pMenuTemplate,
			'adminPanel' => $pAdminPanel,
			'style' => 'display:' . (empty($pMenuTitle) || (isset($_COOKIE[$pKey . 'menu']) && ($_COOKIE[$pKey . 'menu'] == 'o')) ? 'block;' : 'none;')
			// TODO this display logic should maybe be moved to .tpl logic, but need to acces $_COOKIES in {$smartVar}
			);
	}
	// >>>
	// === registerSchemaTable
	/**
	* "Virtual" function stub - fully defined in BitInstaller
	*
	* @return none
	* @access public
	*/
	function registerSchemaTable( $pPackage, $pTableName, $pDataDict, $pRequired=FALSE, $pTableOptions=NULL ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		if( !empty( $pTableName ) ) {
			$this->mPackages[$pPackage]['tables'][$pTableName] = $pDataDict;
			if( !empty( $pTableOptions ) ) {
				$this->mPackages[$pPackage]['tables']['options'][$pTableName] = $pTableOptions;
			}
			// A package is required if _any_ of it's tables are required
			$this->mPackages[$pPackage]['required'] = $pRequired | (isset( $this->mPackages[$pPackage]['required'] ) ? $this->mPackages[$pPackage]['required'] : 0 );
		}
	}
	// >>>
	// === registerPackageInfo
	/**
	* "Virtual" function stub - fully defined in BitInstaller
	*
	* @return none
	* @access public
	*/
	function registerPackageInfo( $pPackage, $pInfoHash ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		$this->mPackages[$pPackage]['info'] = $pInfoHash;
	}
	// >>>
	// === registerSchemaSequences
	/**
	* accepts a sequence to be added to the install list
	*
	* @return none
	* @access public
	*/
	function registerSchemaSequences( $pPackage, $pSeqHash ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		$this->mPackages[$pPackage]['sequences'] = $pSeqHash;
	}
	// >>>
	// >>>
	// === registerSchemaIndex
	/**
	* "Virtual" function stub - fully defined in BitInstaller
	*
	* @return none
	* @access public
	*/
	function registerSchemaIndexes( $pPackage, $pIndexHash ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		$this->mPackages[$pPackage]['indexes'] = $pIndexHash;
	}
	// >>>
	// === registerSchemaDefault
	/**
	* "Virtual" function stub - fully defined in BitInstaller
	*
	* @return none
	* @access public
	*/
	function registerSchemaDefault( $pPackage, $pMixedDefaultSql ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		if( empty( $this->mPackages[$pPackage]['defaults'] ) ) {
			$this->mPackages[$pPackage]['defaults'] = array();
		}
		if( is_array( $pMixedDefaultSql ) ) {
			foreach( $pMixedDefaultSql AS $def ) {
				$this->mPackages[$pPackage]['defaults'][] = $def;
			}
		} elseif( is_string( $pMixedDefaultSql ) ) {
			array_push( $this->mPackages[$pPackage]['defaults'], $pMixedDefaultSql );
		}
	}

	/**
	* registerSchemaTable - Handles big array of update info
	*
	* @return none
	* @access public
	*/
	function registerUpgrade( $pPackage, $pUpgradeHash ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		if( !empty( $pUpgradeHash ) ) {
			$this->mUpgrades[$pPackage] = $pUpgradeHash;
		}
	}

	/**
	* Wrap registerSchemaDefault to handle an array of defaults
	*
	* @return none
	* @access public
	*/
	function registerMenuOptions( $packagedir, $menu_options ) {
		foreach( $menu_options as $opt ) {
			$this->registerSchemaDefault( $packagedir,
			"INSERT INTO `".BIT_DB_PREFIX."tiki_menu_options` (`menu_id` , `type` , `name` , `url` , `position` , `section` , `perm` , `groupname`) VALUES ($opt[0],'$opt[1]','$opt[2]','$opt[3]',$opt[4],'$opt[5]','$opt[6]','$opt[7]')");
		}
	}

	function registerUserPermissions( $packagedir, $userpermissions ) {
		foreach( $userpermissions as $perm ) {
			$this->registerSchemaDefault( $packagedir,
			"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`, `perm_desc`, `level`, `package`) VALUES ('$perm[0]', '$perm[1]', '$perm[2]', '$perm[3]')");
		}
	}

	function registerPreferences( $packagedir, $preferences ) {
		foreach( $preferences as $pref ) {
			$this->registerSchemaDefault( $packagedir,
			"INSERT INTO `".BIT_DB_PREFIX."tiki_preferences`(`package`,`name`,`value`) VALUES ('$pref[0]', '$pref[1]','$pref[2]')");
		}
	}

	function registerModules( $pModuleHash ) {
		$this->mInstallModules = array_merge( $this->mInstallModules, $pModuleHash );
	}

	function registerNotifyEvent( $pEventHash ) {
		$this->mNotifyEvents = array_merge( $this->mNotifyEvents, $pEventHash );
	}

	// >>>
	// === fatalError
	/**
	* If an unrecoverable error has occurred, this method should be invoked. script exist occurs
	*
	* @param string $ pMsg error message to be displayed
	* @return none this function will DIE DIE DIE!!!
	* @access public
	*/
	function fatalError($pMsg)
	{
		global $gBitSmarty;
		$gBitSmarty->assign('msg', tra($pMsg) );
		$this->display( 'error.tpl' );
		die;
	}
	// >>>
	// === scanPackages
	/**
	* scan all available packages. This is an *expensive* function. DO NOT call this functionally regularly , or arbitrarily. Failure to comply is punishable by death by jello suffication!
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function scanPackages($pScanFile = 'bit_setup_inc.php', $pOnce=TRUE )
	{
		global $gBitLoc, $gPreScan;
		if( empty( $gBitLoc ) ) {
			$gBitLoc = array();
		}
		$gBitLoc['BIT_ROOT_URL'] = BIT_ROOT_URL;

		if (!empty($gPreScan) && is_array($gPreScan)) {
			foreach($gPreScan as $pkgName) {
				$this->mRegisterCalled = FALSE;
				$scanFile = BIT_ROOT_PATH.$pkgName.'/'.$pScanFile;
				if (file_exists( $scanFile )) {
					if( $pOnce ) {
						include_once( $scanFile );
					} else {
						include( $scanFile );
					}

					if( $pScanFile == 'bit_setup_inc.php' ) {
						if( !$this->mRegisterCalled || $pkgName!='kernel') {
							$this->registerPackage( $pkgName, BIT_ROOT_PATH . $pkgName . '/', FALSE );
						}
					}
				}
			}
		}

		// load lib configs
		if( $pkgDir = opendir(BIT_ROOT_PATH) ) {
			// Make two passes through the root - 1. to define the DEFINES, and 2. to include the $pScanFile's
			while (false !== ($dirName = readdir($pkgDir))) {
				if (is_dir(BIT_ROOT_PATH . '/' . $dirName) && ($dirName != 'CVS') && ( preg_match( '/^\w/', $dirName)) ) {
					$this->mRegisterCalled = FALSE;
					$scanFile = BIT_ROOT_PATH.$dirName.'/'.$pScanFile;
					if (file_exists( $scanFile )) {
						if( $pOnce ) {
							include_once( $scanFile );
						} else {
							include( $scanFile );
						}
					}
					// We auto-register and directory in the root as a package if it does not call registerPackage itself
					if( $pScanFile == 'bit_setup_inc.php' ) {
						if( (!$this->mRegisterCalled || $dirName!='kernel') && empty( $this->mPackages[$dirName] ) && !file_exists( BIT_ROOT_PATH.$dirName.'/bit_setup_inc.php' ) ) {
							$this->registerPackage( $dirName, BIT_ROOT_PATH . $dirName . '/', FALSE );
						}
					}
				}
			}

			if (!defined('ACTIVE_PACKAGE')) {
				define('ACTIVE_PACKAGE', 'kernel'); // when in doubt, assume the kernel
			}

			$gBitLoc['kernel_url'] = KERNEL_PKG_URL;
			$gBitLoc['kernel_path'] = KERNEL_PKG_PATH;

			if( !defined( 'BIT_STYLES_PATH' ) && defined( 'THEMES_PKG_PATH' ) ) {
				define('BIT_STYLES_PATH', THEMES_PKG_PATH . 'styles/');
			}
			if( !defined( 'BIT_STYLES_URL' ) && defined( 'THEMES_PKG_PATH' ) ) {
				define('BIT_STYLES_URL', THEMES_PKG_URL . 'styles/');
			}
		}
asort( $this->mAppMenu );
	}
	// >>>
	// === verifyInstalledPackages
	/**
	* scan all available packages
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function verifyInstalledPackages() {
		global $gBitDbType;
		$this->scanPackages( 'admin/schema_inc.php' );
		if( $this->isDatabaseValid() ) {
			$lastQuote = strrpos( BIT_DB_PREFIX, '`' );
			if( $lastQuote != FALSE ) {
				$lastQuote++;
			}
			$prefix = substr( BIT_DB_PREFIX,  $lastQuote );
 			$showTables = ( $prefix ? $prefix.'%' : NULL );
			if( $dbTables = $this->mDb->MetaTables('TABLES', FALSE, $showTables ) ) {
				foreach( array_keys( $this->mPackages ) as $package ) {
					if( !empty( $this->mPackages[$package]['tables'] ) ) {
						foreach( array_keys( $this->mPackages[$package]['tables'] ) as $table ) {
							$fullTable = $prefix.$table;
							$tablePresent = in_array( $fullTable, $dbTables );
							if( !$tablePresent ) {
								// There is an incomplete table
								//	vd( "Missing Table: $fullTable" );
							}
							if( isset( $this->mPackages[$package]['installed'] ) ) {
								$this->mPackages[$package]['installed'] &= $tablePresent;
							} else {
								$this->mPackages[$package]['installed'] = $tablePresent;
							}
						}
					} else {
						$this->mPackages[$package]['installed'] = TRUE;
					}
					$this->mPackages[$package]['active_switch'] = $this->getPreference( 'package_'.strtolower( $package ) );
					if( !empty( $this->mPackages[$package]['required'] ) && $this->mPackages[$package]['active_switch'] != 'y' ) {
						// we have a disabled required package. turn it back on!
						$this->storePreference( 'package_'.strtolower( $package ), 'y' );
						$this->mPackages[$package]['active_switch'] = $this->getPreference( 'package_'.strtolower( $package ) );
					}
				}
			}
		}

		foreach( array_keys( $this->mPackages ) as $package ) {	
			if (!empty( $this->mPackages[$package]['installed'] ) && $this->getPreference("package_".strtolower($package)) != 'y') {
				$this->storePreference('package_'.strtolower( $package ), 'n');
			} elseif( empty( $this->mPackages[$package]['installed'] ) ) {
				// Delete the package_<pkgname> row from tiki_preferences
				$this->storePreference('package_'.strtolower( $package ), NULL );
			}
		}
	}

	// Allows a package to be selected as the homepage for the site (Admin->General Settings)
	// Calls to this function should be made from each 'homeable' package's schema_inc.php
	function makePackageHomeable($package) {
		$this->mPackages[strtolower( $package )]['homeable'] = TRUE;
	}

	// Not sure if this is needed anymore - wolff_borg
/*	function storePreferences()
	{
		if (count($this->mPrefs))
		{
			foreach(array_keys($this->mPrefs) as $name)
			{
				$this->storePreference($name, $this->mPrefs[$name]);
			}
		}
	}*/

	function getDefaultPage() {
		global $userlib, $gBitUser;
		$bitIndex = $this->getPreference("bitIndex");
		if ( $bitIndex == 'group_home') {
			// See if we have first a user assigned default group id, and second a group default system preference
			if( !empty( $gBitUser->mInfo['default_group_id'] ) && ($group_home = $gBitUser->getGroupHome( $gBitUser->mInfo['default_group_id'] ) ) ) {
			} elseif( $this->getPreference( 'default_home_group' ) && ($group_home = $gBitUser->getGroupHome( $this->getPreference( 'default_home_group' ) ) ) ) {
			}

			if( !empty( $group_home ) ) {
				if( is_numeric( $group_home ) ) {
					$url = "index.php".( !empty( $group_home ) ? "?content_id=".$group_home : "" );
				} elseif( strpos( $group_home, '/' ) === FALSE ) {
					$url = BitPage::getDisplayUrl( !empty( $group_home ) ? "?page=".$group_home : "" );
				} else {
					$url = $group_home;
				}
			}
		} elseif( $bitIndex == 'my_page' || $bitIndex == 'my_home' || $bitIndex == 'user_home'  ) {
			// TODO: my_home is deprecated, but was the default for CLYDE. remove in DILLINGER - spiderr
			if( $gBitUser->isRegistered() ) {
				if( !$gBitUser->isRegistered() ) {
					$url = USERS_PKG_URL.'login.php';
				} else {
					if( $bitIndex == 'my_page' ) {
						$url = USERS_PKG_URL . 'my.php';
					} elseif( $bitIndex == 'user_home' ) {
						$url = $gBitUser->getDisplayUrl();
					} else {
						$homePage = $gBitUser->getPreference( 'homePage' );
						if (isset($homePage) && !empty($homePage)) {
							if (strpos($homePage, '/') === false) {
								$url = BitPage::getDisplayUrl( $homePage );
							} else {
								$url = $homePage;
							}
						}
					}
				}
			} else {
				$url = USERS_PKG_URL . 'login.php';
			}
		} elseif( !empty( $bitIndex ) ) {
			$url = BIT_ROOT_URL.$bitIndex;
		}

		// if no special case was matched above, default to users' my page
		if( empty( $url ) ) {
			if( $this->isPackageActive( 'wiki' ) ) {
				$url = WIKI_PKG_URL;
			} elseif( !$gBitUser->isRegistered() ) {
				$url = USERS_PKG_URL . 'login.php';
			} else {
				$url = USERS_PKG_URL . 'my.php';
			}
		}
		return $url;
	}
	// === getStyle
	/**
	* figure out the current style
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function getStyle()
	{
		if (empty($this->mStyle))
		{
			$this->mStyle = $this->getPreference('style');
		}
		return $this->mStyle;
	}
	// === setStyle
	/**
	* figure out the current style
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function setStyle($pStyle)
	{
		global $gBitSmarty;
		$this->mStyle = $pStyle;
		$gBitSmarty->assign( 'style', $pStyle );
	}
	// === setBrowserTitle
	/**
	* set the title of the browser
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function getBrowserTitle()
	{
		global $gPageTitle;
		return( $gPageTitle );
	}
	// === setBrowserTitle
	/**
	* set the title of the browser
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function setBrowserTitle($pTitle)
	{
		global $gBitSmarty, $gPageTitle;
		$gPageTitle = $pTitle;
		$gBitSmarty->assign('browserTitle', $pTitle);
		$gBitSmarty->assign('gPageTitle', $pTitle);
	}
	// === getStyleCss
	/**
	* figure out the current style
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function getStyleCss($pStyle = null, $pUserId = NULL)
	{
		global $gBitUser;
		if (empty($pStyle))
		{
			$pStyle = $this->getStyle();
		}
		$ret = '';
		if ($pStyle == 'custom') {
			// This is a page which uses a user-customized theme
			// The user who owns the page (whose custom theme is being requested)
			$homepageUser = new BitUser($pUserId);
			$homepageUser->load();
			// Path to the user-customized css file
			$cssPath = $homepageUser->getStoragePath('theme',$homepageUser->mUserId,null).'custom.css';
			if (file_exists($cssPath)) {
				$ret = $homepageUser->getStorageURL('theme',$homepageUser->mUserId,null).'custom.css';
			}
		} else {
			if( $gBitUser->verifyStorageFile( $pStyle.'.css',$pStyle,$gBitUser->mUserId,'stylist' ) ) {
				$ret = $gBitUser->getStorageUrl( $pStyle,$gBitUser->mUserId,'stylist' ).$pStyle.'.css';
			} elseif( $gBitUser->verifyStorageFile( $pStyle.'.css',$pStyle,NULL,'stylist' ) ) {
				$ret = $gBitUser->getStorageUrl( $pStyle,NULL,'stylist' ).$pStyle.'.css';
			} elseif( file_exists( THEMES_PKG_PATH.'styles/'.$pStyle.'/'.$pStyle.'.css' ) ) {
				$ret = THEMES_PKG_URL.'styles/'.$pStyle.'/'.$pStyle.'.css';
			}
		}
		return $ret;
	}
	// === getCustomStyleCss
	/**
	* get the users custom.css file if there is one
	*
	* @param pStyle style the custom.css is part of
	* @return path to custom.css file
	* @access public
	*/
	function getCustomStyleCss( $pStyle = null )
	{
		global $gBitUser;
		$ret = null;
		if( empty( $pStyle ) ) {
			$pStyle = $this->getStyle();
		}
		if( $gBitUser->verifyStorageFile( 'custom.css',$pStyle,$gBitUser->mUserId,'stylist' ) ) {
			$ret = $gBitUser->getStorageUrl( $pStyle,$gBitUser->mUserId,'stylist' ).'custom.css';
		} elseif( $gBitUser->verifyStorageFile( 'custom.css',$pStyle,NULL,'stylist' ) ) {
			$ret = $gBitUser->getStorageUrl( $pStyle,NULL,'stylist' ).'custom.css';
		}
		return $ret;
	}
	// >>>
	// === getBrowserStyleCss
	/**
	* get browser specific css file
	*
	* @param none
	* @return path to browser specific css file
	* @access public
	*/
	function getBrowserStyleCss()
	{
		global $gBitLoc;
		require_once( UTIL_PKG_PATH.'phpsniff/phpSniff.class.php' );
		$phpsniff = new phpSniff;
		$gBitLoc['browser']['client'] = $phpsniff->property( 'browser' );
		$gBitLoc['browser']['version'] = $phpsniff->property( 'version' );
		$style = $this->getStyle();
		$ret = '';
		if( file_exists( $this->getStylePath().$this->getStyle().'_'.$phpsniff->property( 'browser' ).'.css' ) ) {
			$ret = $this->getStyleUrl().$this->getStyle().'_'.$phpsniff->property( 'browser' ).'.css';
		}
		return $ret;
	}
	// >>>
	// === getAltStyleCss
	/**
	* get alternate style sheets
	*
	* @param none
	* @return array of style sheets with name of stylesheet as array
	* @access public
	*/
	function getAltStyleCss() {
		$ret = NULL;
		$alt_path = $this->getStylePath().'alternate/';
		$alt_url = $this->getStyleUrl().'alternate/';
		if( is_dir( $alt_path ) && $handle = opendir( $alt_path ) ) {
			while( FALSE !== ( $file = readdir( $handle ) ) ) {
				if( ( $file != '.' || $file != '..' ) && preg_match( "/\.css$/i", $file ) ) {
					$p[0] = "/_/";
					$r[0] = " ";
					$p[1] = "/\.css$/i";
					$r[1] = "";
					$name = preg_replace( $p, $r, $file );
					$ret[$name] = $alt_url.$file;
				}
			}
			closedir( $handle );
		}
		return $ret;
	}
	// >>>
	// === getStyleUrl
	/**
	* figure out the current style URL
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function getStyleUrl($pStyle = null)
	{
		if (empty($pStyle))
		{
			$pStyle = $this->getStyle();
		}
		return THEMES_PKG_URL . 'styles/' . $pStyle . '/';
	}
	// >>>
	// === getStylePath
	/**
	* figure out the current style URL
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function getStylePath($pStyle = null)
	{
		if (empty($pStyle))
		{
			$pStyle = $this->getStyle();
		}
		return THEMES_PKG_PATH . 'styles/' . $pStyle . '/';
	}
	// >>>
	// === loadModules
	/**
	* load all modules. LOAD functions imply getting data from database and putting in local member variable
	*
	* @param  $pLayout can be either the username for user assigned modules, or
	* @return none
	* @access public
	*/
	function loadLayout($pUserMixed = ROOT_USER_ID, $pLayout = ACTIVE_PACKAGE, $pFallbackLayout = DEFAULT_PACKAGE, $pForceReload = false)
	{
		if ($pForceReload || empty($this->mLayout) || !count($this->mLayout))
		{
			unset($this->mLayout);
			$this->mLayout = $this->getLayout($pUserMixed, $pLayout, $pLayout, $pFallbackLayout);
		}
	}

	function getLayout($pUserMixed = null, $pLayout = ACTIVE_PACKAGE, $pFallback = true, $pFallbackLayout = DEFAULT_PACKAGE)
	{
		global $user_assigned_modules, $bit_p_configure_modules, $usermoduleslib, $gCenterPieces, $gBitUser;
		$ret = array( 'l' => NULL, 'c' => NULL, 'r' => NULL );
		$layoutUserId = ROOT_USER_ID;

		if (($this->getPreference('feature_user_layout') == 'h' || $this->getPreference('feature_user_layout') == 'y') && isset($pUserMixed)) {
			if (is_numeric($pUserMixed)) {
				$whereClause = " WHERE tl.`user_id`=?";
			} else {
				$whereClause = ", `" . BIT_DB_PREFIX . "users_users` uu WHERE tl.`user_id`=uu.`user_id` AND uu.`login`=?";
			}
			$query = "SELECT tl.`user_id` FROM `" . BIT_DB_PREFIX . "tiki_layouts` tl
					$whereClause ";
			$result = $this->query($query, array($pUserMixed));
			if ($result->fields['user_id']) {
				$layoutUserId = $result->fields['user_id'];
			}
		}
		$whereClause = 'AND ';
		$bindVars = array($layoutUserId);
		// This query will always pull ALL of the ACTIVE_PACKAGE _and_ DEFAULT_PACKAGE modules (in that order)
		// This saves a count() query to see if the ACTIVE_PACKAGE has a layout, since it usually probably doesn't
		// I don't know if it's better or not to save the count() query and retrieve more data - my gut says so,
		// but i've done no research - spiderr
		if ($pLayout != DEFAULT_PACKAGE && $pFallback && $this->mDb->mType != 'firebird' && $this->mDb->mType != 'mssql') {
			// ORDER BY comparison is crucial so current layout modules come up first
			$whereClause .= " (tl.`layout`=? OR tl.`layout`=? ) ORDER BY tl.`layout`=? DESC, ";
			array_push($bindVars, $pLayout);
			array_push($bindVars, $pFallbackLayout);
			array_push($bindVars, $pLayout);
		} elseif ($pLayout != DEFAULT_PACKAGE && $pFallback) {
			// ORDER BY comparison is crucial so current layout modules come up first
			$whereClause .= " (tl.`layout`=? OR tl.`layout`=? ) ORDER BY tl.`layout` DESC, ";
			array_push($bindVars, $pLayout);
			array_push($bindVars, $pFallbackLayout);
		} elseif ($pLayout) {
			$whereClause .= " tl.`layout`=? ORDER BY ";
			array_push($bindVars, $pLayout);
		}
		$query = "SELECT tl.`ord`, tl.`user_id`, tl.`layout`, tl.`position`, tl.`params` AS `section_params`, tlm.*, tmm.`module_rsrc` FROM `" . BIT_DB_PREFIX . "tiki_layouts` tl, `" . BIT_DB_PREFIX . "tiki_layouts_modules` tlm, `" . BIT_DB_PREFIX . "tiki_module_map` tmm
				WHERE tl.`module_id`=tlm.`module_id` AND tl.`user_id`=? AND tmm.`module_id`=tlm.`module_id` $whereClause  " . $this->convert_sortmode("ord_asc");
		$result = $this->query($query, $bindVars);
		// CHeck to see if we have ACTIVE_PACKAGE modules at the top of the results
		if (isset($result->fields['layout']) && ($result->fields['layout'] != DEFAULT_PACKAGE) && (ACTIVE_PACKAGE != DEFAULT_PACKAGE)) {
			$skipDefaults = true;
		} else {
			$skipDefaults = false;
		}

		$gCenterPieces = array();
		while (!$result->EOF) {
			if ($skipDefaults && $result->fields['layout'] == DEFAULT_PACKAGE) {
				// we're done! we've got all the non-DEFAULT_PACKAGE modules
				break;
			}
			$row = &$result->fields;
			if( !empty( $row["section_params"] ) ) {
				$row['params'] = $row['section_params'];
			}
			if( !empty( $row["groups"] ) ) {
				if( $this->isFeatureActive( 'modallgroups' ) || $gBitUser->isAdmin() ) {
					$row["visible"] = TRUE;
				} else {
					if( preg_match( '/[A-Za-z]/', $row["groups"] ) ) {
						// old style serialized group names
						$row["module_groups"] = array();
						if( $grps = @unserialize($row["groups"]) ) {
							foreach ($grps as $grp) {
								global $gBitUser;
								if( !($groupId = array_search( $grp, $gBitUser->mGroups )) ) {
									if( $gBitUser->isAdmin() ) {
										$row["module_groups"][] = $gBitUser->groupExists( $grp, '*' );
									}
								}

								if( !empty( $groupId ) ) {
									$row["module_groups"][] = $groupId;
								}
							}


						}
					} else {
						$row["module_groups"] = explode( ' ', $row["groups"] );
					}
					// Check for the right groups
					foreach( $row["module_groups"] as $modGroupId ) {
						if( $gBitUser->isInGroup( $modGroupId ) ) {
							$row["visible"] = TRUE;
							break; // no need to continue looping
						}
					}
				}
			} else {
				$row["visible"] = TRUE;
				$row["module_groups"] = array();
			}
			if (empty($ret[$row['position']])) {
				$ret[$row['position']] = array();
			}
			if ($row['position'] == CENTER_COLUMN) {
				array_push($gCenterPieces, $row['module_rsrc']);
			}
			array_push($ret[$row['position']], $row);
			$result->MoveNext();
		}
		return $ret;
	}

	/*static*/
	function genPass() {
		$vocales = "aeiouAEIOU";
		$consonantes = "bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ0123456789_";
		$r = '';
		for ($i = 0; $i < 8; $i++) {
			if ($i % 2) {
				$r .= $vocales{rand(0, strlen($vocales) - 1)};
			} else {
				$r .= $consonantes{rand(0, strlen($consonantes) - 1)};
			}
		}
		return $r;
	}

	/**
	* * Return 'windows' if windows, otherwise 'unix'
	* \static
	*/
	function os()
	{
		static $os;
		if( !isset( $os ) ) {
			if( preg_match( "/WIN/",PHP_OS ) ) {
				$os = 'windows';
			} else {
				$os = 'unix';
			}
		}
		return $os;
	}


	/**
	* * Prepend $path to the include path
	* \static
	*/
	function prependIncludePath($path)
	{
		if(!function_exists("get_include_path"))
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Function/get_include_path.php");
		if(!defined("PATH_SEPARATOR"))
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Constant/PATH_SEPARATOR.php");
		if(!function_exists("set_include_path"))
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Function/set_include_path.php");

		$include_path = get_include_path();
		if ($include_path)
		{
			$include_path = $path . PATH_SEPARATOR . $include_path;
		}
		else
		{
			$include_path = $path;
		}
		return set_include_path($include_path);
	}

	/**
	* * Append $path to the include path
	* \static
	*/
	function appendIncludePath($path)
	{
		if(!function_exists("get_include_path"))
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Function/get_include_path.php");
		if(!defined("PATH_SEPARATOR"))
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Constant/PATH_SEPARATOR.php");
		if(!function_exists("set_include_path"))
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Function/set_include_path.php");

		$include_path = get_include_path();
		if ($include_path)
		{
			$include_path .= PATH_SEPARATOR . $path;
		}
		else
		{
			$include_path = $path;
		}
		return set_include_path($include_path);
	}

	/*!
		Check that everything is set up properly

		\static
	*/
	function checkEnvironment()
	{
		static $checked;
		global $gTempDirs;

		if ($checked)
		{
			return;
		}

		$errors = '';

		$docroot = BIT_ROOT_PATH;

		if (ini_get('session.save_handler') == 'files')
		{
			$save_path = ini_get('session.save_path');

			if (!is_dir($save_path))
			{
				$errors .= "The directory '$save_path' does not exist or PHP is not allowed to access it (check open_basedir entry in php.ini).\n";
			}
			else if (!bw_is_writeable($save_path))
			{
				$errors .= "The directory '$save_path' is not writeable.\n";
			}

			if ($errors)
			{
				$save_path = getTempDir();

				if (is_dir($save_path) && bw_is_writeable($save_path))
				{
					ini_set('session.save_path', $save_path);

					$errors = '';
				}
			}
		}

		$wwwuser = '';
		$wwwgroup = '';

		if (isWindows())
		{
			if ( strpos($_SERVER["SERVER_SOFTWARE"],"IIS") && isset($_SERVER['COMPUTERNAME']) ) {
				$wwwuser = 'IUSR_'.$_SERVER['COMPUTERNAME'];
				$wwwgroup = 'IUSR_'.$_SERVER['COMPUTERNAME'];
			} else {
				$wwwuser = 'SYSTEM';
				$wwwgroup = 'SYSTEM';
			}
		}

		if (function_exists('posix_getuid'))
		{
			$userhash = @posix_getpwuid(@posix_getuid());

			$group = @posix_getpwuid(@posix_getgid());
			$wwwuser = $userhash ? $userhash['name'] : false;
			$wwwgroup = $group ? $group['name'] : false;
		}

		if (!$wwwuser)
		{
			$wwwuser = 'nobody (or the user account the web server is running under)';
		}

		if (!$wwwgroup)
		{
			$wwwgroup = 'nobody (or the group account the web server is running under)';
		}

$permFiles = array(
	'temp/'
);

		foreach($permFiles as $file) {
			$present = FALSE;
			// Create directories as needed
			$target = BIT_ROOT_PATH . $file;
			if( preg_match( '/.*\/$/', $target ) ) {
				// we have a directory
				if( !is_dir($target) ) {
					mkdir_p($target, 02775);
				}
			// Check again and report problems
				if (!is_dir($target)) {
					if (!isWindows())
					{ $errors .= "<p>The directory <b style='color:red;'>$target</b> does not exist. To create the directory, execute a command such as:<pre>
	\$ mkdir -m 777 $target
	</pre></p>";
					} else { $errors .= "<p>The directory <b style='color:red;'>$target</b> does not exist. Create the directory $target before proceeding
	</pre></p>";
					}
				} else {
					$present = TRUE;
				}
			} elseif( !file_exists( $target ) ) {
				if (!isWindows())
				{ $errors .= "<p>The file <b style='color:red;'>$target</b> does not exist. To create the file, execute a command such as:<pre>
		\$ touch $target
		\$ chmod 777 $target
	</pre></p>";
				} else { $errors .= "<p>The file <b style='color:red;'>$target</b> does not exist. Create a blank file $target before proceeding
	</pre></p>";
				}
			} else {
				$present = TRUE;
			}

			// chmod( $target, 02775 );
			if( $present && (!bw_is_writeable($target))) {
				if (!isWindows())
				{ $errors .= "<p><b style='color:red;'>$target</b> is not writeable by $wwwuser. To give $wwwuser write permission, execute a command such as:<pre>
	\$ chmod 777 $target
</pre></p>";
				} else { $errors .= "<p><b style='color:red;'>$target</b> is not writeable by $wwwuser. Check the security of the file $target before proceeding
	</pre></p>";
				}
			}
			// if (!is_dir("$docroot/$dir"))
			// {
			// $errors .= "The directory '$docroot$dir' does not exist.\n";
			// }
			// else if (!bw_is_writeable("$docroot/$dir"))
			// {
			// $errors .= "The directory '$docroot$dir' is not writeable by $wwwuser.\n";
			// }
		}

		if ($errors)
		{
			$PHP_CONFIG_FILE_PATH = PHP_CONFIG_FILE_PATH;

			ob_start();
			phpinfo (INFO_MODULES);
			$httpd_conf = 'httpd.conf';

			if (preg_match('/Server Root<\/b><\/td><td\s+align="left">([^<]*)</', ob_get_contents(), $m))
			{
				$httpd_conf = $m[1] . '/' . $httpd_conf;
			}

			ob_end_clean();

			print "
<html><body>
<h2><font color='red'>bitweaver is not properly set up:</font></h1>
<ul>
$errors
</ul>";
			if (!isWindows())
			{
				print "
Proceed to the installer <b>at <a href=\"".BIT_ROOT_URL."install/install.php\">".BIT_ROOT_URL."install/install.php</a></b> after you run the command.
<br />Consult the bitweaver<a href='http://www.bitweaver.org/wiki/index.php?page=Technical_Documentation' target='_blank'>Technical Documentation</a> if you need more help.
</body></html>";
			}
			else
			{
				print "
Proceed to the installer <b>at <a href=\"".BIT_ROOT_URL."install/install.php\">".BIT_ROOT_URL."install/install.php</a></b> after you have corrected the identified problems.
<br />Consult the bitweaver<a href='http://www.bitweaver.org/wiki/index.php?page=Technical_Documentation' target='_blank'>Technical Documentation</a> if you need more help.
</body></html>";
			}

			exit;
		}

		$checked = true;
	}

	function pagination_url($find, $sort_mode, $name1 = "", $value1 = "", $name2 = "", $value2 = "") {
		$url = ($_SERVER['PHP_SELF']);
		$url .= "?find=$find&amp;sort_mode=$sort_mode";
		($name1) ? ($url .= "&amp;$name1=$value1") : ("");
		($name2) ? ($url .= "&amp;$name2=$value2") : ("");
		return $url;
	}

	//********************* CACHE METHODS **************************//

	function list_cache($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
		$findesc = '%' . $find . '%';

		$mid = " where (`url` like ?) ";
		$bindvars=array($findesc);
		} else {
		$mid = "";
		$bindvars=array();
		}

		$query = "select `cache_id` ,`url`,`refresh` from `".BIT_DB_PREFIX."tiki_link_cache` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_link_cache` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
		$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function refresh_cache($cache_id) {
		$query = "select `url`  from `".BIT_DB_PREFIX."tiki_link_cache`
		where `cache_id`=?";

		$url = $this->getOne($query, array( $cache_id ) );
		$data = tp_http_request($url);
		$refresh = date("U");
		$query = "update `".BIT_DB_PREFIX."tiki_link_cache`
		set `data`=?, `refresh`=?
		where `cache_id`=? ";
		$result = $this->query($query, array( $data, $refresh, $cache_id) );
		return true;
	}

	function remove_cache($cache_id) {
		$query = "delete from `".BIT_DB_PREFIX."tiki_link_cache` where `cache_id`=?";

		$result = $this->query($query, array( $cache_id ) );
		return true;
	}

	function get_cache($cache_id) {
		$query = "select * from `".BIT_DB_PREFIX."tiki_link_cache`
		where `cache_id`=?";

		$result = $this->query($query, array( $cache_id ) );
		$res = $result->fetchRow();
		return $res;
	}

	//********************* DATE AND TIME METHODS **************************//

	# TODO move all of these date/time functions to a static class: BitDate
	function get_timezone_list($use_default = false) {
		static $timezone_options;

		if (!$timezone_options) {
			$timezone_options = array();

			if ($use_default)
				$timezone_options['default'] = '-- Use Default Time Zone --';

			foreach ($GLOBALS['_DATE_TIMEZONE_DATA'] as $tz_key => $tz) {
				$offset = $tz['offset'];

				$absoffset = abs($offset /= 60000);
				$plusminus = $offset < 0 ? '-' : '+';
				$gmtoff = sprintf("GMT%1s%02d:%02d", $plusminus, $absoffset / 60, $absoffset - (intval($absoffset / 60) * 60));
				$tzlongshort = $tz['longname'] . ' (' . $tz['shortname'] . ')';
				$timezone_options[$tz_key] = sprintf('%-28.28s: %-36.36s %s', $tz_key, $tzlongshort, $gmtoff);
			}
		}

		return $timezone_options;
	}

	function get_server_timezone() {
		static $server_timezone;

		if (!$server_timezone) {
			$server_time = new Date();

			$server_timezone = $server_time->tz->getID();
		}

		return $server_timezone;
	}

	# TODO rename get_site_timezone()
	function get_display_timezone() {
		static $display_timezone = false;
		global $gBitUser;

		if (!$display_timezone) {
			$server_time = $this->get_server_timezone();
		} else {
			$server_time = NULL;
		}

		if( $gBitUser->isValid() ) {
			$display_timezone = $gBitUser->getPreference('display_timezone');

			if (!$display_timezone || $display_timezone == 'default') {
				$display_timezone = $this->getPreference('display_timezone', $server_time);
			}
		} else {
			$display_timezone = $this->getPreference('display_timezone', $server_time);
		}

		return $display_timezone;
	}

	/**
	 * Retrieves the user's preferred offset for displaying dates.
	 *
	 * $user: the logged-in user.
	 * returns: the preferred offset to UTC.
	 */
	function get_display_offset($_user = false) {

		// Cache preference from DB
		$display_tz = "UTC";

		// Default to UTCget_display_offset
		$display_offset = 0;

		// Load pref from DB is cache is empty
		$display_tz = $this->get_display_timezone($_user);

		// Recompute offset each request in case DST kicked in
		if ($display_tz != "UTC" && isset($_COOKIE["tz_offset"]))
			$display_offset = intval($_COOKIE["tz_offset"]);

		return $display_offset;
	}

	/**
	 * Retrieves a BitDate object for converting to/from display/UTC timezones
	 *
	 * $user: the logged-in user
	 * returns: reference to a BitDate instance with the appropriate offsets
	 */
	function &get_date_converter($_user = false) {
		static $date_converter;

		if (!$date_converter) {
			$display_offset = $this->get_display_offset($_user);
			$date_converter = &new BitDate($display_offset);
		}

		return $date_converter;
	}

	function get_long_date_format() {
		static $long_date_format = false;

		if (!$long_date_format)
		$long_date_format = $this->getPreference('long_date_format', '%A %d of %B, %Y');

		return $long_date_format;
	}

	function get_short_date_format() {
		static $short_date_format = false;

		if (!$short_date_format)
		$short_date_format = $this->getPreference('short_date_format', '%a %d of %b, %Y');

		return $short_date_format;
	}

	function get_long_time_format() {
		static $long_time_format = false;

		if (!$long_time_format)
		$long_time_format = $this->getPreference('long_time_format', '%H:%M:%S %Z');

		return $long_time_format;
	}

	function get_short_time_format() {
		static $short_time_format = false;

		if (!$short_time_format)
		$short_time_format = $this->getPreference('short_time_format', '%H:%M %Z');

		return $short_time_format;
	}

	function get_long_datetime_format() {
		static $long_datetime_format = false;

		if (!$long_datetime_format)
		$long_datetime_format = $this->get_long_date_format(). ' [' . $this->get_long_time_format(). ']';

		return $long_datetime_format;
	}

	function get_short_datetime_format() {
		static $short_datetime_format = false;

		if (!$short_datetime_format)
		$short_datetime_format = $this->get_short_date_format(). ' [' . $this->get_short_time_format(). ']';

		return $short_datetime_format;
	}

	function server_time_to_site_time($timestamp) {
		$date = new Date($timestamp);

		$date->setTZbyID($this->get_server_timezone());
		$date->convertTZbyID($this->get_display_timezone());
		return $date->getTime();
	}

	/**

	 */
	function get_site_date($timestamp, $user = false) {
		static $localed = false;

		if (!$localed) {
		$this->set_locale($user);

		$localed = true;
		}

		$original_tz = date('T', $timestamp);

		$format = '%b %e, %Y';
		$rv = strftime($format, $timestamp);
		$rv .= " =timestamp\n";
		$rv .= strftime('%Z', $timestamp);
		$rv .= " =strftime('%Z')\n";
		$rv .= date('T', $timestamp);
		$rv .= " =date('T')\n";

		$date = &new Date($timestamp);

	# Calling new Date() changes the timezone of the $timestamp var!
	# so we only change the timezone to UTC if the original TZ wasn't UTC
	# to begin with.
	# This seems really buggy, but I don't have time to delve into right now.
		$rv .= date('T', $timestamp);
		$rv .= " =date('T')\n";

		$rv .= $date->format($format);
		$rv .= " =new Date()\n";

		$rv .= date('T', $timestamp);
		$rv .= " =date('T')\n";

		if ($original_tz == 'UTC') {
		$date->setTZbyID('UTC');

		$rv .= $date->format($format);
		$rv .= " =setTZbyID('UTC')\n";
		}

		$tz_id = $this->get_display_timezone($user);

		if ($date->tz->getID() != $tz_id) {
	# let's convert to the displayed timezone
		$date->convertTZbyID($tz_id);

		$rv .= $date->format($format);
		$rv .= " =convertTZbyID($tz_id)\n";
		}

	#return $rv;

	# if ($format == "%b %e, %Y")
	#   $format = $gBitSystem->get_short_date_format();
		return $date;
	}

	# TODO rename to server_time_to_site_time()
	function get_site_time($timestamp, $user = false) {
	#print "<pre>get_site_time()</pre>";
		$date = $this->get_site_date($timestamp, $user);

		return $date->getTime();
	}

	function date_format($format, $timestamp, $user = false) {
		//$date = $this->get_site_date($timestamp, $user);
		// JJ - ignore conversion - we have no idea what TZ they're using

		// strftime doesn't do translations correctly
		// return strftime($format,$timestamp);
		$date = new Date($timestamp);

		return $date->format($format);
	}

	function get_long_date($timestamp, $user = false) {
		return $this->date_format($this->get_long_date_format(), $timestamp, $user);
	}

	function get_short_date($timestamp, $user = false) {
		return $this->date_format($this->get_short_date_format(), $timestamp, $user);
	}

	function get_long_time($timestamp, $user = false) {
		return $this->date_format($this->get_long_time_format(), $timestamp, $user);
	}

	function get_short_time($timestamp, $user = false) {
		return $this->date_format($this->get_short_time_format(), $timestamp, $user);
	}

	function get_long_datetime($timestamp, $user = false) {
		return $this->date_format($this->get_long_datetime_format(), $timestamp, $user);
	}

	function get_short_datetime($timestamp, $user = false) {
		return $this->date_format($this->get_short_datetime_format(), $timestamp, $user);
	}

	function get_site_timezone_shortname($user = false) {
		// UTC, or blank for local
		$dc = &$this->get_date_converter($user);

		return $dc->getTzName();
	}

	function get_server_timezone_shortname($user = false) {
		// Site time is always UTC, from the user's perspective.
		return "UTC";
	}

	/**
	  get_site_time_difference - Return the number of seconds needed to add to a
	  'system' time to return a 'site' time.
	 */
	function get_site_time_difference($user = false) {
		$dc = &$this->get_date_converter($user);

		$display_offset = $dc->display_offset;
		$server_offset = $dc->server_offset;
		return $display_offset - $server_offset;
	}

	/**
	  Timezone saavy replacement for mktime()
	 */
	function make_time($hour, $minute, $second, $month, $day, $year, $timezone_id = false) {
		global $user; # ugh!

		if ($year <= 69)
			$year += 2000;

		if ($year <= 99)
		$year += 1900;

		$date = new Date();
		$date->setHour($hour);
		$date->setMinute($minute);
		$date->setSecond($second);
		$date->setMonth($month);
		$date->setDay($day);
		$date->setYear($year);

	#$rv = sprintf("make_time(): $date->format(%D %T %Z)=%s<br/>\n", $date->format('%D %T %Z'));
	#print "<pre> make_time() start";
	#print_r($date);
		if ($timezone_id)
		$date->setTZbyID($timezone_id);

	#print_r($date);
	#$rv .= sprintf("make_time(): $date->format(%D %T %Z)=%s<br/>\n", $date->format('%D %T %Z'));
	#print $rv;
		return $date->getTime();
	}

	/**
	  Timezone saavy replacement for mktime()
	 */
	function make_server_time($hour, $minute, $second, $month, $day, $year, $timezone_id = false) {
		global $user; # ugh!

		if ($year <= 69)
			$year += 2000;

		if ($year <= 99)
		$year += 1900;

		$date = new Date();
		$date->setHour($hour);
		$date->setMinute($minute);
		$date->setSecond($second);
		$date->setMonth($month);
		$date->setDay($day);
		$date->setYear($year);

	#print "<pre> make_server_time() start\n";
	#print_r($date);
		if ($timezone_id)
		$date->setTZbyID($timezone_id);

	#print_r($date);
		$date->convertTZbyID($this->get_server_timezone());
	#print_r($date);
	#print "make_server_time() end\n</pre>";
		return $date->getTime();
	}

	/**
	  Per http://www.w3.org/TR/NOTE-datetime
	 */
	function get_iso8601_datetime($timestamp, $user = false) {
		return $this->date_format('%Y-%m-%dT%H:%M:%S%O', $timestamp, $user);
	}

	function get_rfc2822_datetime($timestamp = false, $user = false) {
		if (!$timestamp)
			$timestamp = time();

	# rfc2822 requires dates to be en formatted
		$saved_locale = @setlocale(0);
		@setlocale ('en_US');
	#was return date('D, j M Y H:i:s ', $time) . $this->timezone_offset($time, 'no colon');
		$rv = $this->date_format('%a, %e %b %Y %H:%M:%S', $timestamp, $user). $this->get_rfc2822_timezone_offset($timestamp, $user);

	# switch back to the 'saved' locale
		if ($saved_locale)
			@setlocale ($saved_locale);

		return $rv;
	}

	function get_rfc2822_timezone_offset($time = false, $no_colon = false, $user = false) {
		if ($time === false)
			$time = time();

		$secs = $this->date_format('%Z', $time, $user);

		if ($secs < 0) {
			$sign = '-';

			$secs = -$secs;
		} else {
			$sign = '+';
		}

		$colon = $no_colon ? '' : ':';
		$mins = intval(($secs + 30) / 60);

		return sprintf("%s%02d%s%02d", $sign, $mins / 60, $colon, $mins % 60);
	}

	function set_locale($user = false) {
		static $locale = false;

		if (!$locale) {
			# breaks the RFC 2822 code
			$locale = @setlocale(LC_TIME, $this->get_locale($user));
			#print "<pre>set_locale(): locale=$locale\n</pre>";
		}

		return $locale;
	}

	// Check for new version
	// returns and array with information on bitweaver version
	function checkBitVersion() {
		$local= BIT_MAJOR_VERSION.'.'.BIT_MINOR_VERSION.'.'.BIT_SUB_VERSION;
		$ret['local'] = $local;

		$error['number'] = 0;
		$error['string'] = $data = '';

		if( $fsock = @fsockopen( 'www.bitweaver.org', 80, $error['number'], $error['string'], 30 ) ) {
			@fwrite( $fsock, "GET /bitversion.txt HTTP/1.1\r\n" );
			@fwrite( $fsock, "HOST: www.bitweaver.org\r\n" );
			@fwrite( $fsock, "Connection: close\r\n\r\n" );

			$get_info = FALSE;
			while( !@feof( $fsock ) ) {
				if( $get_info ) {
					$data .= @fread( $fsock, 1024 );
				} else {
					if( @fgets( $fsock, 1024 ) == "\r\n" ) {
						$get_info = TRUE;
					}
				}
			}
			@fclose( $fsock );

			// nuke all lines that don't just contain a version number
			$lines = explode( "\n", $data );
			foreach( $lines as $line ) {
				if( preg_match( "/^\d+\.\d+\.\d+$/", $line ) ) {
					$versions[] = $line;
				}
			}

			if( !empty( $versions ) && preg_match( "/\d+\.\d+\.\d+/", $versions[0] ) ) {
				sort( $versions );
				foreach( $versions as $version ) {
					if( preg_match( "/^".BIT_MAJOR_VERSION."/", $version ) ) {
						$ret['compare'] = version_compare( $local, $version );
						$ret['upgrade'] = $version;
					}
				}
				// check if there have been any major releases
				$release = explode( '.', array_pop( $versions ) );
				if( $release[0] > BIT_MAJOR_VERSION ) {
					$ret['release'] = implode( '.', $release );
				}
			} else {
				$error['number'] = 1;
				$error['string'] = tra( 'No version information could be gathered. Perhaps there was a problem connecting to bitweaver.org.' );
			}
		}
		$ret['error'] = $error;
		return $ret;
	}

	// should be moved somewhere else. unbreaking things for now - 25-JUN-2005 - spiderr
	// \todo remove html hardcoded in diff2
	function diff2($page1, $page2) {
		$page1 = split("\n", $page1);
		$page2 = split("\n", $page2);
		$z = new WikiDiff($page1, $page2);
		if ($z->isEmpty()) {
		$html = '<hr /><br />[' . tra("Versions are identical"). ']<br /><br />';
		} else {
		//$fmt = new WikiDiffFormatter;
		$fmt = new WikiUnifiedDiffFormatter;
		$html = $fmt->format($z, $page1);
		}
		return $html;
	}

}

// === installError
/**
* If an unrecoverable error has occurred, this method should be invoked. script exist occurs
*
* @param string $ pMsg error message to be displayed
* @return none this function will DIE DIE DIE!!!
* @access public
*/
function installError($pMsg = null)
{
	global $gBitDbType;
	// here we decide where to go. if there are no db settings yet, we go the welcome page.
	if( isset( $gBitDbType ) ) {
		$step = 1;
	} else {
		$step = 0;
	}

	header( "Location: http://".$_SERVER['HTTP_HOST'].BIT_ROOT_URL."install/install.php?step=".$step );
/*	// figure out our subdirectories, if any.
	echo '<html><head><meta http-equiv="pragma" content="no-cache"><meta http-equiv="expires" content="1" /></head><body>';
	echo "<p>$pMsg</p>";
	echo "<p>Run <a href=\"" . BIT_ROOT_URL . "install/install.php\">install/install.php</a> to begin the installation process.</p>";
	echo "</body></html>";*/
	die;
}

/**
 * @package kernel
 * @subpackage TikiTimer
 */
class TikiTimer
{
	function parseMicro($micro)
	{
		list($micro, $sec) = explode(' ', microtime());

		return $sec + $micro;
	}

	function start($timer = 'default')
	{
		$this->mTimer[$timer] = $this->parseMicro(microtime());
	}

	function stop($timer = 'default')
	{
		return $this->current($timer);
	}

	function elapsed($timer = 'default')
	{
		return $this->parseMicro(microtime()) - $this->mTimer[$timer];
	}
}

/*
function tra($content) {
SPIDERKILL  - need to copy tra function out of setup_inc and put here
}
*/

/* \brief  substr with a utf8 string - works only with $start and $length positive or nuls
* This function is the same as substr but works with multibyte
* In a multybyte sequence, the first byte of a multibyte sequence that represents a non-ASCII character is always in the range 0xC0 to 0xFD
* and it indicates how many bytes follow for this character.
* All further bytes in a multibyte sequence are in the range 0x80 to 0xBF.
*/
/**
 * Check mb_substr availability
 */
if (function_exists('mb_substr'))
{
	mb_internal_encoding("UTF-8");
}
else
{
	function mb_substr($str, $start, $len = '', $encoding = "UTF-8")
	{
		$limit = strlen($str);
		for ($s = 0; $start > 0;--$start) // found the real start
		{
			if ($s >= $limit)
				break;
			if ($str[$s] <= "\x7F")
				++$s;
			else
			{
				++$s; // skip length
				while ($str[$s] >= "\x80" && $str[$s] <= "\xBF")
				++$s;
			}
		}
		if ($len == '')
			return substr($str, $s);
		else
			for ($e = $s; $len > 0; --$len) // found the real end
		{
			if ($e >= $limit)
				break;
			if ($str[$e] <= "\x7F")
				++$e;
			else
			{
				++$e; //skip length
				while ($str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit)
				++$e;
			}
		}
		return substr($str, $s, $e - $s);
	}
}


?>
