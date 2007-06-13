<?php
/**
 * Main bitweaver systems functions
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitSystem.php,v 1.135 2007/06/13 22:08:55 spiderr Exp $
 * @author spider <spider@steelsun.com>
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

/**
 * required setup
 */
require_once( KERNEL_PKG_PATH . 'BitBase.php' );
require_once( KERNEL_PKG_PATH . 'BitDate.php' );
require_once( KERNEL_PKG_PATH . 'BitSmarty.php' );

define( 'DEFAULT_PACKAGE', 'kernel' );
define( 'CENTER_COLUMN', 'c' );
define( 'HOMEPAGE_LAYOUT', 'home' );

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
 *
 * @package kernel
 */
class BitSystem extends BitBase {

	// Initiate class variables

	// Information about package menus used in all menu modules and top bar
	var $mAppMenu = array();

	// Essential information about packages
	var $mPackages = array();

	// Cross Reference Package Directory Name => Package Key used as index into $mPackages
	var $mPackagesDirNameXref = array();

	// Contains site style information
	var $mStyle = array();

	// The currently active page
	var $mActivePackage;

	// Modules that need to inserted during installation
	var $mInstallModules = array();

	// Javascript to be added to the <body onload> attribute
	var $mOnload = array();

	// Used by packages to register notification events that can be subscribed to.
	var $mNotifyEvents = array();

	// Used to store contents of kernel_config
	var $mConfig;

	// Used to monitor if ::registerPackage() was called. This is used to determine whether to auto-register a package
	var $mRegisterCalled;

	// The name of the package that is currently being processed
	var $mPackageFileName;

	// Display full page or just contents?
	var $mDisplayOnlyContent;

	// Ajax framework that will be used for current page view
	var $mAjax = NULL;

	// Ajax libraries needed by current Ajax framework (MochiKit libs, etc.)
	var $mAjaxLibs = array();

	// === BitSystem constructor
	/**
	* base constructor, auto assigns member db variable
	*
	* @access public
	*/
	// Constructor receiving a PEAR::Db database object.
	function BitSystem() {
		global $gBitTimer;
		// Call DB constructor which will create the database member variable
		BitBase::BitBase();

		$this->mAppMenu = array();
		$this->mTimer = $gBitTimer;
		$this->mServerTimestamp = new BitDate();

		$this->loadConfig();

		// Critical Preflight Checks
		$this->checkEnvironment();

		$this->initSmarty();
		$this->mRegisterCalled = FALSE;
	}

	// === initSmarty
	/**
	* Define and load Smarty components
	*
	* @param none $
	* @return none
	* @access private
	*/
	function initSmarty() {
		global $bitdomain, $_SERVER, $gBitSmarty;

		// Set the separator for PHP generated tags to be &amp; instead of &
		// This is necessary for XHTML compliance
		ini_set( "arg_separator.output", "&amp;" );
		// Remove automatic quotes added to POST/COOKIE by PHP
		if( get_magic_quotes_gpc() ) {
			foreach( $_REQUEST as $k => $v ) {
				if( !is_array( $_REQUEST[$k] ) ) {
					$_REQUEST[$k] = stripslashes( $v );
				}
			}
		}

		if( !isset( $bitdomain ) ) {
			$bitdomain = "";
		}

		// make sure we only create one BitSmarty
		if( !is_object( $gBitSmarty ) ) {
			$gBitSmarty = new BitSmarty();
			// set the default handler
			$gBitSmarty->load_filter( 'pre', 'tr' );
			// $gBitSmarty->load_filter('output','trimwhitespace');
			if( isset( $_REQUEST['highlight'] ) ) {
				$gBitSmarty->load_filter( 'output', 'highlight' );
			}
		}
	}

	/**
	* Load all preferences and store them in $this->mConfig
	*
	* @param $pPackage optionally get preferences only for selected package
	*/
	function loadConfig( $pPackage = NULL ) {
		$queryVars = array();
		$whereClause = '';

		if( $pPackage ) {
			array_push( $queryVars, $pPackage );
			$whereClause = ' WHERE `package`=? ';
		}

		if ( empty( $this->mConfig ) ) {
			$this->mConfig = array();
			$query = "SELECT `config_name` ,`config_value`, `package` FROM `" . BIT_DB_PREFIX . "kernel_config` " . $whereClause;
			if( $rs = $this->mDb->query( $query, $queryVars, -1, -1 ) ) {
				while( $row = $rs->fetchRow() ) {
					$this->mConfig[$row['config_name']] = $row['config_value'];
				}
			}
		}
		return count( $this->mConfig );
	}

	// <<< getConfig
	/**
	* Add getConfig / setConfig for more uniform handling of config variables instead of spreading global vars.
	* easily get the value of any given preference stored in kernel_config
	*
	* @access public
	**/
	function getConfig( $pName, $pDefault = NULL ) {
		if( empty( $this->mConfig ) ) {
			$this->loadConfig();
		}
		return( empty( $this->mConfig[$pName] ) ? $pDefault : $this->mConfig[$pName] );
	}

	// <<< getConfigMatch
	/**
	* retreive a group of config variables
	*
	* @access public
	**/
	function getConfigMatch( $pPattern, $pSelectValue="" ) {
		if( empty( $this->mConfig ) ) {
			$this->loadConfig();
		}

		$matching_keys = array();
		$matching_keys = preg_grep( $pPattern, array_keys( $this->mConfig ) );
		$new_array = array();
		foreach( $matching_keys as $key=>$value ) {
			if ( empty( $pSelectValue ) || ( !empty( $pSelectValue ) && $this->mConfig[$value] == $pSelectValue ) ) {
				$new_array[$value] = $this->mConfig[$value];
			}
		}
		return( $new_array );
	}

	/**
	* set a group of config variables
	*
	* @access public
	**/
	function setConfigMatch( $pPattern, $pSelect_value="", $pNew_value=NULL, $pPackage=NULL ) {
		if( empty( $this->mConfig ) ) {
			$this->loadConfig();
		}

		$matching_keys = array();
		$matching_keys = preg_grep($pPattern, array_keys($this->mConfig));
		$new_array = array();
		foreach($matching_keys as $key=>$config_name) {
			if ( empty($pSelect_value) || ( !empty($pSelect_value) && $this->mConfig[$config_name] == $pSelect_value) ) {
				$this->storeConfig($config_name, $pNew_value, $pPackage);
			}
		}
	}

	/**
	* Set a hash value in the mConfig hash. This does *NOT* store the value in the database. It does no checking for existing or duplicate values. the main point of this function is to limit direct accessing of the mConfig hash. I will probably make mConfig private one day.
	*
	* @param string Hash key for the mConfig value
	* @param string Value for the mConfig hash key
	*/
	function setConfig( $pName, $pValue ) {
		$this->mConfig[$pName] = $pValue;
		return( TRUE );
	}

	// deprecated method saved compatibility until all getPreference calls have been eliminated
	function getPreference( $pName, $pDefault = '' ) {
		deprecated( 'Please use: BitSystem::getConfig()' );
		return $this->getConfig( $pName, $pDefault );
	}
	function setPreference( $pPrefName, $pPrefValue ) {
		deprecated( 'Please use: BitSystem::setConfig()' );
		$this->setConfig( $pPrefName, $pPrefValue );
	}

	// <<< storeConfig
	/**
	* bitweaver needs lots of settings just to operate.
	* loadConfig assigns itself the default preferences, then loads just the differences from the database.
	* In storeConfig (and only when storeConfig is called) we make a second copy of defaults to see if
	* preferences you are changing is different from the default.
	* if it is the same, don't store it!
	* So instead updating the whole prefs table, only updat "delta" of the changes delta from defaults.
	*
	* @access public
	**/
	function storeConfig( $pName, $pValue, $pPackage = NULL ) {
		global $gMultisites;
		//stop undefined offset error being thrown after packages are installed
		if( !empty( $this->mConfig ) ){
			// store the pref if we have a value _AND_ it is different from the default
			if( ( empty( $this->mConfig[$pName] ) || ( $this->mConfig[$pName] != $pValue ) ) ) {
				// make sure the value doesn't exceede database limitations
				$pValue = substr( $pValue, 0, 250 );

				// store the preference in multisites, if used
				if( $this->isPackageActive( 'multisites' ) && @BitBase::verifyId( $gMultisites->mMultisiteId ) && isset( $gMultisites->mConfig[$pName] ) ) {
					$query = "UPDATE `".BIT_DB_PREFIX."multisite_preferences` SET `config_value`=? WHERE `multisite_id`=? AND `config_name`=?";
					$result = $this->mDb->query( $query, array( empty( $pValue ) ? '' : $pValue, $gMultisites->mMultisiteId, $pName ) );
				} else {
					$query = "DELETE FROM `".BIT_DB_PREFIX."kernel_config` WHERE `config_name`=?";
					$result = $this->mDb->query( $query, array( $pName ) );
					// make sure only non-empty values get saved, including '0'
					if( isset( $pValue ) && ( !empty( $pValue )  || is_numeric( $pValue ) ) ) {
						$query = "INSERT INTO `".BIT_DB_PREFIX."kernel_config`(`config_name`,`config_value`,`package`) VALUES (?,?,?)";
						$result = $this->mDb->query( $query, array( $pName, $pValue, strtolower( $pPackage ) ) );
					}
				}

				// Force the ADODB cache to flush
				$isCaching = $this->mDb->isCachingActive();
				$this->mDb->setCaching( FALSE );
				$this->loadConfig();
				$this->mDb->setCaching( $isCaching );
			}
		}
		$this->setConfig( $pName, $pValue );
		return TRUE;
	}

	// <<< expungePackageConfig
	/**
	* Delete all prefences for the given package
	* @access public
	**/
	function expungePackageConfig( $pPackageName ) {
		if( !empty( $pPackageName ) ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."kernel_config` WHERE `package`=?";
			$result = $this->mDb->query( $query, array( strtolower( $pPackageName ) ) );
			// let's force a reload of the prefs
			unset( $this->mConfig );
			$this->loadConfig();
		}
	}

	// === hasValidSenderEmail
	/**
	* Determines if this site has a legitimate sender address set.
	*
	* @param  $mid the name of the template for the page content
	* @access public
	*/
	function hasValidSenderEmail( $pSenderEmail=NULL ) {
		if( empty( $pSenderEmail ) ) {
			$pSenderEmail = $this->getConfig( 'site_sender_email' );
		}
		return( !empty( $pSenderEmail ) && !preg_match( '/.*localhost$/', $pSenderEmail ) );
	}

	// === getErrorEmail
	/**
	* Smartly determines where error emails should go
	*
	* @access public
	*/
	function getErrorEmail() {
		if( defined('ERROR_EMAIL') ) {
			$ret = ERROR_EMAIL;
		} elseif( $this->getConfig( 'site_sender_email' ) ) {
			$ret = $this->getConfig( 'site_sender_email' );
		} elseif( !empty( $_SERVER['SERVER_ADMIN'] ) ) {
			$ret = $_SERVER['SERVER_ADMIN'];
		} else {
			$ret = 'root@localhost';
		}
	}

	// === sendEmail
	/**
	* centralized function for send emails
	*
	* @param  $mid the name of the template for the page content
	* @access public
	*/
	function sendEmail( $pMailHash ) {
		$extraHeaders = '';
		if( $this->getConfig( 'bcc_email' ) ) {
			$extraHeaders = "Bcc: ".$this->getConfig( 'bcc_email' )."\r\n";
		}
		if( !empty( $pMailHash['Reply-to'] ) ) {
			$extraHeaders = "Reply-to: ".$pMailHash['Reply-to']."\r\n";
		}

		mail($pMailHash['email'],
			$pMailHash['subject'].' '.$_SERVER["SERVER_NAME"],
			$pMailHash['body'],
			"From: ".$this->getConfig( 'site_sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n$extraHeaders"
		);
	}

	// === display
	/**
	 * Tell bitsystem to only render the 'mid' when doing a display.
	 *
	 * @param $pOnlyRender flag defaulting to true
	 * @access public
	 */
	function onlyRenderContent($pOnlyRender = true) {
		$this->mDisplayOnlyContent = $pOnlyRender;
	}

	function loadAjax( $pAjaxLib, $pLibHash=NULL ) {
		global $gBitSmarty, $gSniffer;
		$ret = FALSE;
		$ajaxLib = strtolower( $pAjaxLib );
		if( $gSniffer->_browser_info['javascript'] ) {
			if( $ret = ( empty( $this->mAjaxLib ) || $this->mAjaxLib == $ajaxLib ) ) {
				$gBitSmarty->assign( 'loadAjax', $ajaxLib );
				$this->mAjax = $ajaxLib;
				if( is_array( $pLibHash ) ) {
					$this->mAjaxLibs = array_merge( $this->mAjaxLibs, $pLibHash );
				}
			}
		}
		return $ret;
	}

	/**
	* Display the main page template
	*
	* @param  $mid the name of the template for the page content
	* @access public
	*/
	function display( $pMid, $pBrowserTitle=NULL ) {
		global $gBitSmarty;
		$gBitSmarty->verifyCompileDir();

		if ($this->mDisplayOnlyContent) {
			$gBitSmarty->assign_by_ref('gBitSystem', $this);
			$gBitSmarty->display($pMid);
			return;
		}

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
//		$gBitSmarty->assign( 'page', !empty( $_REQUEST['page'] ) ? $_REQUEST['page'] : NULL );
		// Make sure that the gBitSystem symbol available to templates is correct and up-to-date.
		$gBitSmarty->assign_by_ref('gBitSystem', $this);
		$gBitSmarty->display( 'bitpackage:kernel/bitweaver.tpl' );
		$this->postDisplay( $pMid );
	}

	// === preDisplay
	/**
	* Take care of any processing that needs to happen just before the template is displayed
	*
	* @param none $
	* @access private
	*/
	function preDisplay( $pMid ) {
		global $gCenterPieces, $gBitSmarty, $gBitThemes;
		define( 'JSCALENDAR_PKG_URL', UTIL_PKG_URL.'jscalendar/' );

		$gBitThemes->loadStyle();
		$gBitThemes->loadLayout();

		// check to see if we are working with a dynamic center area
		if( $pMid == 'bitpackage:kernel/dynamic.tpl' ) {
			$gBitSmarty->assign_by_ref( 'gCenterPieces', $gCenterPieces );
		} else {
			unset( $gBitThemes->mLayout['c'] );
		}

		// process layout
		require_once( THEMES_PKG_PATH.'modules_inc.php' );

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

	// === postDisplay
	/**
	* Take care of any processing that needs to happen just after the template is displayed
	*
	* @param none $
	* @access private
	*/
	function postDisplay( $pMid ) {
	}

	// === setHelpInfo
	/**
	* Set the smarty variables needed to display the help link for a page.
	*
	* @param  $package Package Name
	* @param  $context Context of the help within the package
	* @param  $desc Description of the help link (not the help itself)
	* @access private
	*/
	function setHelpInfo( $package, $context, $desc ) {
		global $gBitSmarty;
		$gBitSmarty->assign( 'TikiHelpInfo', array( 'URL' => 'http://doc.bitweaver.org/wiki/index.php?page=' . $package . $context , 'Desc' => $desc ) );
	}

	// === isPackageActive
	/**
	* check's if a package is active.
	* @param $pPackageName the name of the package to test
    *        where the package name is in the form used to index $mPackages
    *        See comments in scanPackages for more information
	* @return none
	* @access public
	*
	* @param $pKey hash key
	*/
	function isPackageActive( $pPackageName ) {

	// A package is installed if
	//    $this->getConfig('package_'.$name) == 'i'
	// or $this->getConfig('package_'.$name) == 'y'
	//
	// A package is installed and active if
	//     <package name>_PKG_NAME is defined
	// and $this->getConfig('package_'.$name) == 'y'

		$ret = FALSE;
		if( defined( strtoupper( $pPackageName ).'_PKG_NAME' ) ) {
			$name = strtolower( @constant( ( strtoupper( $pPackageName ).'_PKG_NAME' ) ) );
			if( $name ) {
				// kernel always active
				if( $name == 'kernel' ) {
					$ret = 1;
				} else {
					// we have migrated the old tikiwiki feature_<pac
					$ret = ( $this->getConfig( 'package_'.$name ) == 'y' );
				}

			}
		}

		return( $ret );
	}

	// === isPackageInstalled
	/**
	* check's if a package is Installed
	* @param $pPackageName the name of the package to test
    *        where the package name is in the form used to index $mPackages
    *        See comments in scanPackages for more information
	* @return none
	* @access public
	*
	* @param $pKey hash key
	*/
	function isPackageInstalled( $pPackageName ) {

	// A package is installed if
	//    $this->getConfig('package_'.$name) == 'i'
	// or $this->getConfig('package_'.$name) == 'y'
	//
	// A package is installed and active if
	//     <package name>_PKG_NAME is defined
	// and $this->getConfig('package_'.$name) == 'y'

		$ret = FALSE;
		if (defined( (strtoupper( $pPackageName ).'_PKG_NAME') ) ) {
			$name = strtolower( @constant( (strtoupper( $pPackageName ).'_PKG_NAME') ) );
			if( $name ) {
				// kernel always active
				if ($name == 'kernel') {
					$ret = 1;
				}
				else {
					// we have migrated the old tikiwiki feature_<pac
					$ret = ($this->getConfig('package_'.$name) == 'i')
					|| ($this->getConfig('package_'.$name) == 'y')
					;
				}

			}
		}

		return( $ret );
	}

	// === verifyPackage
	/**
	* It will verify that the given package is active or it will display the error template and die()
	* @param $pPackageName the name of the package to test
    *        where the package name is in the form used to index $mPackages
    *        See comments in scanPackages for more information
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
		$ret = $this->mDb->getAssoc( $sql, $bindVars );
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
	function verifyPermission( $pPermission, $pMsg = NULL ) {
		global $gBitSmarty, $gBitUser, ${$pPermission};
		if( empty( $pPermission ) || $gBitUser->hasPermission( $pPermission ) ) {
			return TRUE;
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
		$gBitSmarty->assign( 'fatalTitle', tra( "Permission denied." ) );
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
			$featureValue = $this->getConfig($pFeatureName);
			$ret = !empty( $featureValue ) && ( $featureValue != 'n' );
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
	function registerPackage( $pRegisterHash ) {
		if( !isset( $pRegisterHash['package_name'] )) {
			$this->fatalError( tra("Package name not set in ")."registerPackage: $this->mPackageFileName" );;
		} else {
			$name = $pRegisterHash['package_name'];
		}

		if( !isset( $pRegisterHash['package_path'] )) {
			$this->fatalError( tra("Package path not set in ")."registerPackage: $this->mPackageFileName" );;
		} else {
			$path = $pRegisterHash['package_path'];
		}

		$this->mRegisterCalled = TRUE;
		if( empty( $this->mPackages )) {
			$this->mPackages = array();
		}
		$pkgName = str_replace( ' ', '_', strtoupper( $name ));
		$pkgNameKey = strtolower( $pkgName );

		// Some package settings
		$this->mPackages[$pkgNameKey]['homeable'] = !empty( $pRegisterHash['homeable'] );
		$this->mPackages[$pkgNameKey]['required'] = !empty( $pRegisterHash['required_package'] );
		$this->mPackages[$pkgNameKey]['service']  = !empty( $pRegisterHash['service'] ) ? $pRegisterHash['service'] : FALSE;
		$this->mPackages[$pkgNameKey]['status']   = $this->getConfig( 'package_'.$pkgNameKey, 'n');

		# y = Active
		# i = Installed
		# n (or empty/null) = Not Active and Not Installed

		// set package installed and active flag
		if( $this->mPackages[$pkgNameKey]['status'] == 'a' || $this->mPackages[$pkgNameKey]['status'] == 'y' ) {
			$this->mPackages[$pkgNameKey]['active_switch'] = TRUE;
		} else {
			$this->mPackages[$pkgNameKey]['active_switch'] = FALSE;
		}

		// set package installed flag (can be installed but not active)
		if( $this->mPackages[$pkgNameKey]['active_switch'] || $this->mPackages[$pkgNameKey]['status'] == 'i' ) {
			$this->mPackages[$pkgNameKey]['installed'] = TRUE;
		} else {
			$this->mPackages[$pkgNameKey]['installed'] = FALSE;
		}

		// Define <PACKAGE>_PKG_PATH
		$pkgDefine = $pkgName.'_PKG_PATH';
		if( !defined( $pkgDefine )) {
			define( $pkgDefine, $path );
		}
		$this->mPackages[$pkgNameKey]['url']  = BIT_ROOT_URL . basename( $path ) . '/';
		$this->mPackages[$pkgNameKey]['path']  = BIT_ROOT_PATH . basename( $path ) . '/';

		// Define <PACKAGE>_PKG_URL
		$pkgDefine = $pkgName.'_PKG_URL';
		if( !defined( $pkgDefine )) {
			// Force full URI's for offline or exported content (newsletters, etc.)
			$root = !empty( $_REQUEST['uri_mode'] ) ? BIT_BASE_URI . '/' : BIT_ROOT_URL;
			define( $pkgDefine, $root . basename( $path ) . '/' );
		}

		// Define <PACKAGE>_PKG_URI
		$pkgDefine = $pkgName.'_PKG_URI';
		if( !defined( $pkgDefine ) && defined( 'BIT_BASE_URI' )) {
			define( $pkgDefine, BIT_BASE_URI . '/' . basename( $path ) . '/' );
		}

		// Define <PACKAGE>_PKG_NAME
		$pkgDefine = $pkgName.'_PKG_NAME';
		if( !defined( $pkgDefine )) {
			define( $pkgDefine, $name );
			$this->mPackages[$pkgNameKey]['activatable']  = isset( $pRegisterHash['activatable'] ) ? $pRegisterHash['activatable'] : TRUE;
		}
		$this->mPackages[$pkgNameKey]['name'] = $name;

		// Define <PACKAGE>_PKG_DIR
		$package_dir_name = basename( $path );
		$pkgDefine = $pkgName.'_PKG_DIR';
		if( !defined( $pkgDefine )) {
			define( $pkgDefine, $package_dir_name );
		}
		$this->mPackages[$pkgNameKey]['dir'] = $package_dir_name;
		$this->mPackagesDirNameXref[$package_dir_name] = $pkgNameKey;

		// Define the package we are currently in
		// I tried strpos instead of preg_match here, but it didn't like strings that begin with slash?! - spiderr
		if( !defined( 'ACTIVE_PACKAGE' ) && ( isset( $_SERVER['ACTIVE_PACKAGE'] ) || preg_match( '!/'.$this->mPackages[$pkgNameKey]['dir'].'/!', $_SERVER['PHP_SELF'] ) || preg_match( '!/'.$pkgNameKey.'/!', $_SERVER['PHP_SELF'] ))) {
			if( isset( $_SERVER['ACTIVE_PACKAGE'] )) {
				// perhaps the webserver told us the active package (probably because of mod_rewrites)
				$pkgNameKey = $_SERVER['ACTIVE_PACKAGE'];
			}
			define( 'ACTIVE_PACKAGE', $pkgNameKey );
			$this->mActivePackage = $pkgNameKey;
		}
	}

	// === registerAppMenu
	/**
	* Define and load Smarty components
	*
	* @param  $pKey hash key
	* @return none
	* @access public
	*/
	function registerAppMenu( $pMenuHash, $pMenuTitle = NULL, $pTitleUrl = NULL, $pMenuTemplate = NULL, $pAdminPanel = FALSE ) {
		if( is_array( $pMenuHash ) ) {
			// shorthand
			$pkg = $pMenuHash['package_name'];

			// prepare hash
			$pMenuHash['style']       = 'display:'.( ( isset( $_COOKIE[$pMenuHash.'menu'] ) && ( $_COOKIE[$pMenuHash.'menu'] == 'o' ) ) ? 'block;' : 'none;' );
			$pMenuHash['is_disabled'] = ( $this->getConfig( 'menu_'.$pkg ) == 'n' );
			$pMenuHash['menu_title']  = $this->getConfig( $pkg.'_menu_text',
				( !empty( $pMenuHash['menu_title'] )
					? $pMenuHash['menu_title']
					: ucfirst( constant( strtoupper( $pkg ).'_PKG_DIR' )))
			);
			$pMenuHash['menu_position'] = $this->getConfig( $pkg.'_menu_position',
				( !empty( $pMenuHash['menu_position'] )
					? $pMenuHash['menu_position']
					: NULL )
			);

			$this->mAppMenu[$pkg] = $pMenuHash;
		} else {
			deprecated( 'Please use a menu registration hash instead of individual parameters: $gBitSystem->registerAppMenu( $menuHash )' );
			$this->mAppMenu[strtolower( $pMenuHash )] = array(
				'menu_title'    => $pMenuTitle,
				'is_disabled'   => ( $this->getConfig( 'menu_'.$pMenuHash ) == 'n' ),
				'index_url'     => $pTitleUrl,
				'menu_template' => $pMenuTemplate,
				'admin_panel'   => $pAdminPanel,
				'style'         => 'display:'.( empty( $pMenuTitle ) || ( isset( $_COOKIE[$pMenuHash.'menu'] ) && ( $_COOKIE[$pMenuHash.'menu'] == 'o' ) ) ? 'block;' : 'none;' )
			);
		}
	}

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
			// Package required now set in register package function
			// A package is required if _any_ of it's tables are required
			//$this->mPackages[$pPackage]['required'] = $pRequired | (isset( $this->mPackages[$pPackage]['required'] ) ? $this->mPackages[$pPackage]['required'] : 0 );
		}
	}

	function registerSchemaConstraints( $pPackage, $pTableName, $pConstraints ) {
		$pPackage = strtolower( $pPackage);
		if( !empty( $pTableName ) ) {
			$this->mPackages[$pPackage]['constraints'][$pTableName] = $pConstraints;
		}
	}

	/**
	 * Holds the package version - required by packager - the bitweaver package manager
	 *
	 * @return none
	 * @access public
	 */
	function registerPackageVersion( $pPackage, $pVersion ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		$this->mPackages[$pPackage]['version'] = $pVersion;
	}

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
			foreach( $pMixedDefaultSql as $def ) {
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
//	function registerMenuOptions( $packagedir, $menu_options ) {
//		foreach( $menu_options as $opt ) {
//			$this->registerSchemaDefault( $packagedir,
//			"INSERT INTO `".BIT_DB_PREFIX."tiki_menu_options` (`menu_id` , `type` , `name` , `url` , `position` , `section` , `perm` , `groupname`) VALUES ($opt[0],'$opt[1]','$opt[2]','$opt[3]',$opt[4],'$opt[5]','$opt[6]','$opt[7]')");
//		}
//	}

	function registerUserPermissions( $packagedir, $userpermissions ) {
		foreach( $userpermissions as $perm ) {
			$this->mPermHash[$perm[0]] = $perm;
			$this->mPermHash[$perm[0]]['sql'] = "INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`, `perm_desc`, `perm_level`, `package`) VALUES ('$perm[0]', '$perm[1]', '$perm[2]', '$perm[3]')";
			$this->registerSchemaDefault( $packagedir,
			"INSERT INTO `".BIT_DB_PREFIX."users_permissions` (`perm_name`, `perm_desc`, `perm_level`, `package`) VALUES ('$perm[0]', '$perm[1]', '$perm[2]', '$perm[3]')");
		}
	}

	function registerConfig( $packagedir, $preferences ) {
		foreach( $preferences as $pref ) {
			$this->registerSchemaDefault( $packagedir,
			"INSERT INTO `".BIT_DB_PREFIX."kernel_config`(`package`,`config_name`,`config_value`) VALUES ('$pref[0]', '$pref[1]','$pref[2]')");
		}
	}
	function registerPreferences( $packagedir, $preferences ) {
		$this->registerConfig( $packagedir, $preferences );
	}

	function registerModules( $pModuleHash ) {
		$this->mInstallModules = array_merge( $this->mInstallModules, $pModuleHash );
	}

	function registerNotifyEvent( $pEventHash ) {
		$this->mNotifyEvents = array_merge( $this->mNotifyEvents, $pEventHash );
	}

	// === fatalError
	/**
	* If an unrecoverable error has occurred, this method should be invoked. script exist occurs
	*
	* @param string $ pMsg error message to be displayed
	* @return none this function will DIE DIE DIE!!!
	* @access public
	*/
	function fatalError( $pMsg, $pTemplate='error.tpl', $pErrorTitle="Seems there's been a problem." ) {
		global $gBitSmarty;
		$gBitSmarty->assign( 'fatalTitle', tra( $pErrorTitle ) );
		$gBitSmarty->assign( 'msg', $pMsg );
		$this->display( $pTemplate );
		die;
	}

	// === loadPackage
	/**
	* Loads a package
	*
	* @param string $ pkgDir = Directory Name of package to load
	* @param string $ pScanFile file to be looked for
	* @param string $ autoRegister - TRUE = autoregister any packages that don't register on their own, FALSE = don't
	* @param string $ pOnce - TRUE = do include_once to load file FALSE = do include to load the file
	* @return none
	* @access public
	*/
	function loadPackage( $pPkgDir, $pScanFile, $pAutoRegister=TRUE, $pOnce=TRUE ) { 
		#check if already loaded, loading again won't work with 'include_once' since
		#no register call will be done, so don't auto register.
		if( $pAutoRegister && !empty( $this->mPackagesDirNameXref[$pPkgDir] ) ) {
			$pAutoRegister = FALSE;
		}

		$this->mRegisterCalled = FALSE;
		$scanFile = BIT_ROOT_PATH.$pPkgDir.'/'.$pScanFile;
		$file_exists = 0;
		if( file_exists( $scanFile ) ) {
			$file_exists = 1;
			global $gBitSystem, $gLibertySystem, $gBitSmarty, $gBitUser, $gBitLanguage;
			$this->mPackageFileName = $scanFile;
			if( $pOnce ) {
				include_once( $scanFile );
			} else {
				include( $scanFile );
			}
		}

		if( ( $file_exists || $pPkgDir == 'kernel' ) && ( $pAutoRegister && !$this->mRegisterCalled ) ) {
			$registerHash = array(
				#for auto registered packages Registration Package Name = Package Directory Name
				'package_name' => $pPkgDir,
				'package_path' => BIT_ROOT_PATH.$pPkgDir.'/',
				'activatable' => FALSE,
			);
			if( $pPkgDir == 'kernel' ) {
				$registerHash = array_merge( $registerHash, array( 'required_package'=>TRUE ) );
			}
			$this->registerPackage( $registerHash );
		}
	}

	// === scanPackages
	/**
	*
	* scan all available packages. This is an *expensive* function. DO NOT call this functionally regularly , or arbitrarily. Failure to comply is punishable by death by jello suffication!
	*
	* @param string $ pScanFile file to be looked for
	* @param string $ pOnce - TRUE = do include_once to load file FALSE = do include to load the file
	* @param string $ pSelect - empty or 'all' = load all packages, 'installed' = load installed, 'active' = load active, 'x' = load packages with status x
	* @param string $ autoRegister - TRUE = autoregister any packages that don't register on their own, FALSE = don't
	* @param string $ fileSystemScan - TRUE = scan file system for packages to load, False = don't
	* @return none
	* 
    * Packages have three different names:
    *    The directory name where they reside on disk
    *    The Name they register themselves as when they call registerPackage 
    *    The Key for the array $this->mPackages
    *    
    * Example:
    *    A package in directory 'stars' that registers itself with a name of 'Star Ratings'
    *    would have these three names:
    *    
    *    Directory Name: 'stars'
    *    Registered Name: Star Ratings'
    *    $this->mPackages key: 'star_ratings'
    *
    *    Of course, its possible for all three names to be the same if the registered name
    *    is all lower case without spaces and is the same as the diretory name.
    *
    *    Functions that expect a package name as a parameter should make clear which form
    *    of the name they expect.
    *    
	* @access public
	*/
	function scanPackages( $pScanFile = 'bit_setup_inc.php', $pOnce=TRUE, $pSelect='', $pAutoRegister=TRUE ) {
		global $gPreScan;
		if( !empty( $gPreScan ) && is_array( $gPreScan ) ) {
			// gPreScan may hold a list of packages that must be loaded first
			foreach( $gPreScan as $pkgDir ) {
				$this->loadPackage( $pkgDir, $pScanFile, $pAutoRegister, $pOnce );
			}
		}

		// load lib configs
		if( $pkgDir = opendir( BIT_ROOT_PATH ) ) {
			while( FALSE !== ( $dirName = readdir( $pkgDir ) ) ) {
				if( ($dirName != '..')  && ($dirName != '.') && is_dir(BIT_ROOT_PATH . '/' . $dirName) && ($dirName != 'CVS') && (preg_match( '/^\w/', $dirName )) ) {
					$scanFile = BIT_ROOT_PATH.$dirName.'/'.$pScanFile;
					$this->loadPackage( $dirName, $pScanFile, $pAutoRegister, $pOnce );
				}
			}
		}

		if( !defined( 'BIT_STYLES_PATH' ) && defined( 'THEMES_PKG_PATH' ) ) {
			define( 'BIT_STYLES_PATH', THEMES_PKG_PATH . 'styles/' );
		}

		if( !defined( 'BIT_STYLES_URL' ) && defined( 'THEMES_PKG_URL' ) ) {
			define( 'BIT_STYLES_URL', THEMES_PKG_URL . 'styles/' );
		}
	}

	// === verifyInstalledPackages
	/**
	* scan all available packages
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function verifyInstalledPackages( $pSelect='installed' ) {
		global $gBitDbType;
		#load in any admin/schema_inc.php files that exist for each package
		$this->scanPackages( 'admin/schema_inc.php', TRUE, $pSelect, FALSE, TRUE );
		$ret = array();

		if( $this->isDatabaseValid() ) {
			if( strlen( BIT_DB_PREFIX ) > 0 ) {
				$lastQuote = strrpos( BIT_DB_PREFIX, '`' );
				if( $lastQuote != FALSE ) {
					$lastQuote++;
				}
				$prefix = substr( BIT_DB_PREFIX, $lastQuote );
			} else {
				$prefix = '';
			}

			$showTables = ( $prefix ? $prefix.'%' : NULL );
			if( $dbTables = $this->mDb->MetaTables('TABLES', FALSE, $showTables ) ) {
				foreach( array_keys( $this->mPackages ) as $package ) {
					// Default to true, &= will FALSE out
					$this->mPackages[$package]['installed'] = TRUE;
					if( !empty( $this->mPackages[$package]['tables'] ) ) {
						$this->mPackages[$package]['db_tables_found'] = TRUE;
						foreach( array_keys( $this->mPackages[$package]['tables'] ) as $table ) {
							// painful hardcoded exception for bitcommerce
							if( $package == 'bitcommerce' ) {
								$fullTable = $table;
							} else {
								$fullTable = $prefix.$table;
							}
							$tablePresent = in_array( $fullTable, $dbTables );
							if( $tablePresent ) {
								$ret['present'][$package][] = $table;
							} else {
								$ret['missing'][$package][] = $table;
// This is a crude but highly effective means of blurting out a very bad situation when an installed package is missing a table
if( !defined( 'IS_LIVE' ) || !IS_LIVE ) {
	vd( "Table Missing => $package : $table" );
}
							}

							$this->mPackages[$package]['installed'] &= $tablePresent;
							$this->mPackages[$package]['db_tables_found'] &= $tablePresent;
						}
					} else {
						$this->mPackages[$package]['db_tables_found'] = FALSE;
					}


					$this->mPackages[$package]['active_switch'] = $this->getConfig( 'package_'.strtolower( $package ) );
					if( !empty( $this->mPackages[$package]['required'] ) && $this->mPackages[$package]['active_switch'] != 'y' ) {
						// we have a disabled required package. turn it back on!
						$this->storeConfig( 'package_' . $package, 'y', $package );
						$this->mPackages[$package]['active_switch'] = $this->getConfig( 'package_' . $package );
					} elseif( !empty( $this->mPackages[$package]['required'] ) && $this->mPackages[$package]['installed'] &&  $this->getConfig( 'package_'.$package ) != 'i' &&  $this->getConfig( 'package_'.$package ) != 'y' ) {
						$this->storeConfig( 'package_' . $package, 'i', $package );
					} elseif( !empty( $this->mPackages[$package]['installed'] ) && !$this->isFeatureActive( 'package_'.strtolower( $package ) ) ) {
						// set package to i if it is installed but not isFeatureActive (common when re-installing packages)
						$this->storeConfig( 'package_' . $package, 'i', $package );
					}
				}
			}
		}
		return $ret;
	}

	// Allows a package to be selected as the homepage for the site (Admin->General Settings)
	// Calls to this function should be made from each 'homeable' package's schema_inc.php
	function makePackageHomeable( $package ) {
		deprecated( 'Please use: BitSystem::registerPackage( array( "homeable" => TRUE ) ) in your bit_setup_inc.php file' );
		$this->mPackages[strtolower( $package )]['homeable'] = TRUE;
	}

	function getDefaultPage() {
		global $userlib, $gBitUser, $gBitSystem;
		$bit_index = $this->getConfig( "bit_index" );
		$url = '';
		if( $bit_index == 'group_home') {
			// See if we have first a user assigned default group id, and second a group default system preference
			if( !$gBitUser->isRegistered() && ( $group_home = $gBitUser->getGroupHome( ANONYMOUS_GROUP_ID ))) {
			} elseif( @$this->verifyId( $gBitUser->mInfo['default_group_id'] ) && ( $group_home = $gBitUser->getGroupHome( $gBitUser->mInfo['default_group_id'] ))) {
			} elseif( $this->getConfig( 'default_home_group' ) && ( $group_home = $gBitUser->getGroupHome( $this->getConfig( 'default_home_group' )))) {
			}

			if( !empty( $group_home )) {
				if( $this->verifyId( $group_home ) ) {
					$url = BIT_ROOT_URL."index.php".( !empty( $group_home ) ? "?content_id=".$group_home : "" );
				} elseif( strpos( $group_home, '/' ) === FALSE ) {
					$url = BitPage::getDisplayUrl( $group_home );
				} else {
					$url = $group_home;
				}
			}
		} elseif( $bit_index == 'my_page' || $bit_index == 'my_home' || $bit_index == 'user_home'  ) {
			// TODO: my_home is deprecated, but was the default for BWR1. remove in DILLINGER - spiderr
			if( $gBitUser->isRegistered() ) {
				if( !$gBitUser->isRegistered() ) {
					$url = USERS_PKG_URL.'login.php';
				} else {
					if( $bit_index == 'my_page' ) {
						$url = USERS_PKG_URL . 'my.php';
					} elseif( $bit_index == 'user_home' ) {
						$url = $gBitUser->getDisplayUrl();
					} else {
						$users_homepage = $gBitUser->getPreference( 'users_homepage' );
						if (isset($users_homepage) && !empty($users_homepage)) {
							if (strpos($users_homepage, '/') === false) {
								$url = BitPage::getDisplayUrl( $users_homepage );
							} else {
								$url = $users_homepage;
							}
						}
					}
				}
			} else {
				$url = USERS_PKG_URL . 'login.php';
			}
		} elseif( in_array( $bit_index, array_keys( $gBitSystem->mPackages ) ) ) {
			$work = strtoupper( $bit_index ).'_PKG_URL';
			if (defined("$work")) {
				$url = constant( $work );
			}

//this sends requests to inactive packages so commented out
//for example if wiki is made not active, we can end up trying to go there
//		} elseif( !empty( $bit_index ) ) {
//			$url = BIT_ROOT_URL.$bit_index;
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
	// === setOnloadScript
	/**
	* set the title of the browser
	*
	* @param string $ pScanFile file to be looked for
	* @return none
	* @access public
	*/
	function setOnloadScript( $pJavscript )
	{
		array_push( $this->mOnload, $pJavscript );
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

	/*static*/
	function genPass() {
		$vocales = "aeiou";
		$consonantes = "bcdfghjklmnpqrstvwxyz123456789";
		$r = '';
		for( $i = 0; $i < 8; $i++ ) {
			if( $i % 2 ) {
				$r .= $vocales{rand( 0, strlen( $vocales ) - 1 )};
			} else {
				$r .= $consonantes{rand( 0, strlen( $consonantes ) - 1 )};
			}
		}
		return $r;
	}

	// === lookupMimeType
	/**
	* given an extension, return the mime type
	*
	* @param string $pExtension is the extension of the file or the complete file name
	* @return mime type of entry and populates $this->mMimeTypes with existing mime types
	* @access public
	*/
	function lookupMimeType( $pExtension ) {
		// rfc1341 - mime types are case insensitive.
		if( preg_match( "/.*\.[a-zA-Z]+$/", $pExtension ) ) {
			$pExtension = substr( $pExtension, ( strrpos( $pExtension, '.' ) + 1 )  );
		}
		$pExtension = strtolower( $pExtension );
		if( empty( $this->mMimeTypes ) ) {
			// use the local mime.types files if it is available since it may be more current
			$mimeFile = is_file( '/etc/mime.types' ) && is_readable( '/etc/mime.types' ) ? '/etc/mime.types' : KERNEL_PKG_PATH.'admin/mime.types';
			$this->mMimeTypes = array();
			if( $fp = fopen( $mimeFile,"r" ) ) {
				while( false != ($line = fgets( $fp, 4096 ) ) ) {
					if( !preg_match( "/^\s*(?!#)\s*(\S+)\s+(?=\S)(.+)/", $line, $match ) ) {
						continue;
					}
					$tmp = preg_split( "/\s/",trim( $match[2] ) );
					foreach( $tmp as $type ) {
						$this->mMimeTypes[strtolower( $type )] = $match[1];
					}
				}
				fclose( $fp );
			}
		}

		return( !empty( $this->mMimeTypes[$pExtension] ) ? $this->mMimeTypes[$pExtension] : 'application/binary' );
	}


	// === verifyFileExtension
	/**
	* given a file and optionally desired name, return the correctly extensioned file and mime type
	*
	* @param string $pFile is the actual file to inspect for magic numbers to determine type
	* @param string $pFileName is the desired name the file. This is optional in the even the pFile is non-extensioned, as is the case with file uploads
	* @return corrected file name and mime type
	* @access public
	*/
	function verifyFileExtension( $pFile, $pFileName=NULL ) {
		if( empty( $pFileName ) ) {
			$pFileName = basename( $pFile );
			$ret = $pFile;
		} else {
			$ret = $pFileName;
		}
		$extension = substr( $pFileName, strrpos( $pFileName, '.' ) + 1 );
		$lookupMime = $this->lookupMimeType( $extension );
		$verifyMime = $this->verifyMimeType( $pFile );
		if( $lookupMime != $verifyMime ) {
			if( $mimeExt = array_search( $verifyMime, $this->mMimeTypes ) ) {
				$ret = substr( $pFileName, 0, strrpos( $pFileName, '.' ) + 1 ).$mimeExt;
			}
		}
		return array( $ret, $verifyMime );
	}


	// === verifyMimeType
	/**
	* given a file, return the mime type
	*
	* @param string $pExtension is the extension of the file or the complete file name
	* @return mime type of entry and populates $this->mMimeTypes with existing mime types
	* @access public
	*/
	function verifyMimeType( $pFile ) {
		$mime = NULL;
		if( file_exists( $pFile ) ) {
			if( function_exists( 'finfo_open' ) ) {
				$finfo = finfo_open( FILEINFO_MIME );
				$mime = finfo_file( $finfo, $pFile );
				finfo_close( $finfo );
			} else {
				$mime = exec( trim( 'file -bi ' . escapeshellarg ( $pFile ) ) );
			}
			if( empty( $mime ) ) {
				$mime = $this->lookupMimeType( substr( $pFile, strrpos( $pFile, '.' ) + 1 ) );
			}
			if( $len = strpos( $mime, ';' ) || $len = strpos( $mime, ';' ) ) {
				$mime = substr( $mime, 0, $len );
			}
		}
		return $mime;
	}


	/**
	* * Return 'windows' if windows, otherwise 'unix'
	* \static
	*/
	function os() {
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
	* * Prepend $pPath to the include path
	* \static
	*/
	function prependIncludePath( $pPath ) {
		if( !function_exists( "get_include_path" ) ) {
			include_once( UTIL_PKG_PATH . "PHP_Compat/Compat/Function/get_include_path.php" );
		}
		if( !defined( "PATH_SEPARATOR" ) ) {
			include_once( UTIL_PKG_PATH . "PHP_Compat/Compat/Constant/PATH_SEPARATOR.php" );
		}
		if( !function_exists( "set_include_path" ) ) {
			include_once( UTIL_PKG_PATH . "PHP_Compat/Compat/Function/set_include_path.php" );
		}

		$include_path = get_include_path();
		if( $include_path ) {
			$include_path = $pPath . PATH_SEPARATOR . $include_path;
		} else {
			$include_path = $pPath;
		}
		return set_include_path( $include_path );
	}

	/**
	* * Append $pPath to the include path
	* \static
	*/
	function appendIncludePath( $pPath ) {
		if( !function_exists( "get_include_path" ) ) {
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Function/get_include_path.php");
		}
		if( !defined("PATH_SEPARATOR" ) ) {
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Constant/PATH_SEPARATOR.php");
		}
		if( !function_exists( "set_include_path" ) ) {
			include_once(UTIL_PKG_PATH . "PHP_Compat/Compat/Function/set_include_path.php");
		}

		$include_path = get_include_path();
		if( $include_path ) {
			$include_path .= PATH_SEPARATOR . $pPath;
		} else {
			$include_path = $pPath;
		}
		return set_include_path( $include_path );
	}

	/* Check that everything is set up properly
	* \static
	*/
	function checkEnvironment() {
		static $checked, $gTempDirs;

		if( $checked ) {
			return;
		}

		$errors = '';

		$docroot = BIT_ROOT_PATH;

		if( ini_get( 'session.save_handler' ) == 'files' ) {
			$save_path = ini_get( 'session.save_path' );

			if( empty( $save_path ) ) {
				$errors .= "The session.save_path variable is not setup correctly (its empty).\n";
			} else {
				if( strpos( $save_path, ";" ) !== FALSE ) {
					$save_path = substr( $save_path, strpos( $save_path, ";" )+1 );
				}
				$open = ini_get( 'open_basedir' );
				if( !@is_dir( $save_path ) && empty( $open ) ) {
					$errors .= "The directory '$save_path' does not exist or PHP is not allowed to access it (check open_basedir entry in php.ini).\n";
				} elseif( !bw_is_writeable( $save_path ) ) {
					$errors .= "The directory '$save_path' is not writeable.\n";
				}
			}

			if( $errors ) {
				$save_path = get_temp_dir();

				if( is_dir( $save_path ) && bw_is_writeable( $save_path ) ) {
					ini_set( 'session.save_path', $save_path );

					$errors = '';
				}
			}
		}

		$wwwuser = '';
		$wwwgroup = '';

		if( is_windows() ) {
			if( strpos( $_SERVER["SERVER_SOFTWARE"],"IIS" ) && isset( $_SERVER['COMPUTERNAME'] ) ) {
				$wwwuser = 'IUSR_'.$_SERVER['COMPUTERNAME'];
				$wwwgroup = 'IUSR_'.$_SERVER['COMPUTERNAME'];
			} else {
				$wwwuser = 'SYSTEM';
				$wwwgroup = 'SYSTEM';
			}
		}

		if( function_exists( 'posix_getuid' ) ) {
			$userhash = @posix_getpwuid( @posix_getuid() );

			$group = @posix_getpwuid( @posix_getgid() );
			$wwwuser = $userhash ? $userhash['name'] : false;
			$wwwgroup = $group ? $group['name'] : false;
		}

		if( !$wwwuser ) {
			$wwwuser = 'nobody (or the user account the web server is running under)';
		}

		if( !$wwwgroup ) {
			$wwwgroup = 'nobody (or the group account the web server is running under)';
		}

		$permFiles[] = $this->getConfig( 'site_temp_dir', BIT_ROOT_PATH.'temp/' );

		foreach( $permFiles as $file ) {
			$present = FALSE;
			// Create directories as needed
			$target = $file;
			if( preg_match( '/.*\/$/', $target ) ) {
				// we have a directory
				if( !is_dir( $target ) ) {
					mkdir_p( $target, 02775 );
				}
			// Check again and report problems
				if( !is_dir( $target ) ) {
					if( !is_windows() ) {
						$errors .= "
							<p>The directory <strong style='color:red;'>$target</strong> does not exist. To create the directory, execute a command such as:</p>
							<pre>\$ mkdir -m 777 $target</pre>
						";
					} else {
						$errors .= "<p>The directory <strong style='color:red;'>$target</strong> does not exist. Create the directory $target before proceeding</p>";
					}
				} else {
					$present = TRUE;
				}
			} elseif( !file_exists( $target ) ) {
				if( !is_windows()) {
					$errors .= "<p>The file <b style='color:red;'>$target</b> does not exist. To create the file, execute a command such as:</p>
						<pre>
							\$ touch $target
							\$ chmod 777 $target
						</pre>
					";
				} else {
					$errors .= "<p>The file <b style='color:red;'>$target</b> does not exist. Create a blank file $target before proceeding</p>";
				}
			} else {
				$present = TRUE;
			}

			// chmod( $target, 02775 );
			if( $present && ( !bw_is_writeable( $target ) ) ) {
				if (!is_windows())
				{ $errors .= "<p><strong style='color:red;'>$target</strong> is not writeable by $wwwuser. To give $wwwuser write permission, execute a command such as:</p>
					<pre>\$ chmod 777 $target</pre>";
				} else {
					$errors .= "<p><b style='color:red;'>$target</b> is not writeable by $wwwuser. Check the security of the file $target before proceeding</p>";
				}
			}
			//if (!is_dir("$docroot/$dir")) {
			//	$errors .= "The directory '$docroot$dir' does not exist.\n";
			//} else if (!bw_is_writeable("$docroot/$dir")) {
			//	$errors .= "The directory '$docroot$dir' is not writeable by $wwwuser.\n";
			//}
		}

		if( $errors ) {
			$PHP_CONFIG_FILE_PATH = PHP_CONFIG_FILE_PATH;

			ob_start();
			phpinfo (INFO_MODULES);
			$httpd_conf = 'httpd.conf';

			if (preg_match('/Server Root<\/b><\/td><td\s+align="left">([^<]*)</', ob_get_contents(), $m)) {
				$httpd_conf = $m[1] . '/' . $httpd_conf;
			}

			ob_end_clean();

			print "
				<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"DTD/xhtml1-strict.dtd\">
				<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
					<head>
						<title>bitweaver setup problems</title>
						<meta http-equiv=\"Pragma\" content=\"no-cache\" />
						<meta http-equiv=\"Expires\" content=\"-1\" />
					</head>
					<body>
						<h1 style=\"color:red;\">bitweaver is not properly set up:</h1>
						<blockquote>
							$errors
						</blockquote>
			";

			if( !defined( 'IS_LIVE' ) || !IS_LIVE ) {
				if (!is_windows()) {
					print "
						<p>Proceed to the installer <strong>at <a href=\"".BIT_ROOT_URL."install/install.php\">".BIT_ROOT_URL."install/install.php</a></strong> after you run the command.
						<br />Consult the bitweaver<a href='http://www.bitweaver.org/wiki/index.php?page=Technical_Documentation'>Technical Documentation</a> if you need more help.</p>
					";
				} else {
					print "
						<p>Proceed to the installer <strong>at <a href=\"".BIT_ROOT_URL."install/install.php\">".BIT_ROOT_URL."install/install.php</a></strong> after you have corrected the identified problems.
						<br />Consult the bitweaver<a href='http://www.bitweaver.org/wiki/index.php?page=Technical_Documentation'>Technical Documentation</a> if you need more help.</p>
					";
				}
				print "</body></html>";
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

	function list_cache($offset, $max_records, $sort_mode, $find) {

		if ($find) {
		$findesc = '%' . $find . '%';

		$mid = " where (`url` like ?) ";
		$bindvars=array($findesc);
		} else {
		$mid = "";
		$bindvars=array();
		}

		$query = "select `cache_id` ,`url`,`refresh` from `".BIT_DB_PREFIX."liberty_link_cache` $mid order by ".$this->mDb->convertSortmode($sort_mode);
		$query_cant = "select count(*) from `".BIT_DB_PREFIX."liberty_link_cache` $mid";
		$result = $this->mDb->query($query,$bindvars,$max_records,$offset);
		$cant = $this->mDb->getOne($query_cant,$bindvars);
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
		global $gBitSystem;
		$query = "select `url`  from `".BIT_DB_PREFIX."liberty_link_cache`
		where `cache_id`=?";

		$url = $this->mDb->getOne($query, array( $cache_id ) );
		$data = bit_http_request($url);
		$refresh = $gBitSystem->getUTCTime();
		$query = "update `".BIT_DB_PREFIX."liberty_link_cache`
		set `data`=?, `refresh`=?
		where `cache_id`=? ";
		$result = $this->mDb->query($query, array( $data, $refresh, $cache_id) );
		return true;
	}

	function remove_cache($cache_id) {
		$query = "delete from `".BIT_DB_PREFIX."liberty_link_cache` where `cache_id`=?";

		$result = $this->mDb->query($query, array( $cache_id ) );
		return true;
	}

	function get_cache($cache_id) {
		$query = "select * from `".BIT_DB_PREFIX."liberty_link_cache`
		where `cache_id`=?";

		$result = $this->mDb->query($query, array( $cache_id ) );
		$res = $result->fetchRow();
		return $res;
	}

	//********************* DATE AND TIME METHODS **************************//

	/**
	 * Retrieve a current UTC timestamp
	 * Simple map to BitDate object allowing tidy display elsewhere
	 */
	function getUTCTime() {
		return	$this->mServerTimestamp->getUTCTime();
	}

	/**
	 * Retrieve a current UTC ISO timestamp
	 * Simple map to BitDate object allowing tidy display elsewhere
	 */
	function getUTCTimestamp() {
		return	$this->mServerTimestamp->getUTCTimestamp();
	}

	/**
	 * Retrieves the user's preferred offset for displaying dates.
	 */
	function get_display_offset( $pUser = FALSE ) {
		return $this->mServerTimestamp->get_display_offset( $pUser );
	}

	/**
	 * Retrieves the user's preferred long date format for displaying dates.
	 */
	function get_long_date_format() {
		static $site_long_date_format = FALSE;

		if( !$site_long_date_format ) {
			$site_long_date_format = $this->getConfig( 'site_long_date_format', '%A %d of %B, %Y' );
		}

		return $site_long_date_format;
	}

	/**
	 * Retrieves the user's preferred short date format for displaying dates.
	 */
	function get_short_date_format() {
		static $site_short_date_format = FALSE;

		if( !$site_short_date_format ) {
			$site_short_date_format = $this->getConfig( 'site_short_date_format', '%d %b %Y' );
		}

		return $site_short_date_format;
	}

	/**
	 * Retrieves the user's preferred long time format for displaying dates.
	 */
	function get_long_time_format() {
		static $site_long_time_format = FALSE;

		if( !$site_long_time_format ) {
			$site_long_time_format = $this->getConfig( 'site_long_time_format', '%H:%M:%S %Z' );
		}

		return $site_long_time_format;
	}

	/**
	 * Retrieves the user's preferred short time format for displaying dates.
	 */
	function get_short_time_format() {
		static $site_short_time_format = FALSE;

		if( !$site_short_time_format ) {
			$site_short_time_format = $this->getConfig( 'site_short_time_format', '%H:%M %Z' );
		}

		return $site_short_time_format;
	}

	/**
	 * Retrieves the user's preferred long date/time format for displaying dates.
	 */
	function get_long_datetime_format() {
		static $long_datetime_format = FALSE;

		if( !$long_datetime_format ) {
			$long_datetime_format = $this->get_long_date_format().' ['.$this->get_long_time_format().']';
		}

		return $long_datetime_format;
	}

	/**
	 * Retrieves the user's preferred short date/time format for displaying dates.
	 */
	function get_short_datetime_format() {
		static $short_datetime_format = FALSE;

		if( !$short_datetime_format ) {
			$short_datetime_format = $this->get_short_date_format().' ['.$this->get_short_time_format().']';
		}

		return $short_datetime_format;
	}

	/*
	 * Only used in rang_lib.php which needs tidying up to use smarty templates
	 */
	function get_long_datetime( $pTimestamp, $pUser = FALSE ) {
		return $this->mServerTimestamp->strftime( $this->get_long_datetime_format(), $pTimestamp, $pUser );
	}

	// Check for new version
	// returns an array with information on bitweaver version
	function checkBitVersion() {
		$local= BIT_MAJOR_VERSION.'.'.BIT_MINOR_VERSION.'.'.BIT_SUB_VERSION;
		$ret['local'] = $local;

		$error['number'] = 0;
		$error['string'] = $data = '';

		// cache the bitversion.txt file locally and update only once a day
		if( !is_file( TEMP_PKG_PATH.'bitversion.txt' ) || ( time() - filemtime( TEMP_PKG_PATH.'bitversion.txt' )) > 86400 ) {
			if( $h = fopen( TEMP_PKG_PATH.'bitversion.txt', 'w' )) {
				$data = bit_http_request( 'http://www.bitweaver.org/bitversion.txt' );
				if( !preg_match( "/not found/i", $data )) {
					fwrite( $h, $data );
					fclose( $h );
				}
			}
		}

		if( is_readable( TEMP_PKG_PATH.'bitversion.txt' ) ) {
			$h = fopen( TEMP_PKG_PATH.'bitversion.txt', 'r' );
			if( isset( $h ) ) {
				$data = fread( $h, 1024 );
				fclose( $h );
			}

			// nuke all lines that don't just contain a version number
			$lines = explode( "\n", $data );
			foreach( $lines as $line ) {
				if( preg_match( "/^\d+\.\d+\.\d+$/", $line ) ) {
					$versions[] = $line;
				}
			}

			if( !empty( $data ) && !empty( $versions ) && preg_match( "/\d+\.\d+\.\d+/", $versions[0] ) ) {
				sort( $versions );
				foreach( $versions as $version ) {
					if( preg_match( "/^".BIT_MAJOR_VERSION."\./", $version ) ) {
						$ret['compare'] = version_compare( $local, $version );
						$ret['upgrade'] = $version;
						$ret['page'] = preg_replace( "/\.\d+$/", "", $version );
					}
				}
				// check if there have been any major releases
				$release = explode( '.', array_pop( $versions ) );
				if( $release[0] > BIT_MAJOR_VERSION ) {
					$ret['release'] = implode( '.', $release );
					$ret['page'] = $release[0].'.'.$release[1];
				} elseif( $release[0] < BIT_MAJOR_VERSION ) {
					$ret['compare'] = version_compare( $local, $version );
					$ret['upgrade'] = $version;
				}
			} else {
				$error['number'] = 1;
				$error['string'] = tra( 'No version information available. Check your connection to bitweaver.org' );
			}
		}
		// append any release level
		$ret['local'] .= ' '.BIT_LEVEL;
		$ret['error'] = $error;
		return $ret;
	}

	/**
	* Statically callable function to determine if the current call was made using Ajax
	*
	* @access public
	**/
	function isAjaxRequest() {
		return( (!empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') || !empty( $_REQUEST['ajax_xml'] ) );
	}

	/**
	* Statically callable function to see if browser supports javascript
	* determined by cookie set in bitweaver.js
	* @access public
	**/
	function isJavascriptEnabled(){
		return (!empty($_COOKIE['javascript_enabled']) && $_COOKIE['javascript_enabled'] == 'y');
	}
	

	// should be moved somewhere else. unbreaking things for now - 25-JUN-2005 - spiderr
	// \TODO remove html hardcoded in diff2
	function diff2( $page1, $page2 ) {
		$page1 = split( "\n", $page1 );
		$page2 = split( "\n", $page2 );
		$z = new WikiDiff( $page1, $page2 );
		if( $z->isEmpty() ) {
			$html = '<hr /><br />['.tra("Versions are identical").']<br /><br />';
		} else {
			//$fmt = new WikiDiffFormatter;
			$fmt = new WikiUnifiedDiffFormatter;
			$html = $fmt->format( $z, $page1 );
		}
		return $html;
	}

	function storeVersion( $pPackage, $pVersion ) {
		global $gBitSystem;
		if( !empty( $gBitSystem->mPackages[$pPackage] )) {
			$gBitSystem->storeConfig( "package_".$pPackage."_version", $pVersion, $pPackage );
			return TRUE;
		}
		return FALSE;
	}

	function getVersion( $pPackage, $pDefault = NULL ) {
		global $gBitSystem;
		return $gBitSystem->getConfig( "package_".$pPackage."_version", $pDefault );
	}





	// ==================== deprecated methods - will be removed soon ====================
	function getTplIncludeFiles( $pFilename ) {
		global $gBitThemes;
		deprecated( 'This is now in BitThemes instead of BitSystem.' );
		return $gBitThemes->getTplIncludeFiles( $pFilename );
	}
	function getStyle() {
		global $gBitThemes;
		deprecated( 'This is now in BitThemes instead of BitSystem.' );
		return $gBitThemes->getStyle( $pStyle );
	}
	function setStyle( $pStyle ) {
		global $gBitThemes;
		deprecated( 'This is now in BitThemes instead of BitSystem.' );
		return $gBitThemes->setStyle( $pStyle );
	}
	function getStyleCss( $pStyle = NULL, $pUserId = NULL ) {
		global $gBitThemes;
		deprecated( 'This is now in BitThemes instead of BitSystem.' );
		return $gBitThemes->getStyleCss( $pStyle, $pUserId );
	}
	function getCustomStyleCss( $pStyle = null ) {
		global $gBitThemes;
		deprecated( 'This is now in BitThemes instead of BitSystem.' );
		return $gBitThemes->getCustomStyleCss( $pStyle );
	}
	function getBrowserStyleCss() {
		global $gBitThemes;
		deprecated( 'This is now in BitThemes instead of BitSystem.' );
		return $gBitThemes->getBrowserStyleCss();
	}
	function getStyleUrl( $pStyle = NULL ) {
		global $gBitThemes;
		deprecated( 'This is now in BitThemes instead of BitSystem.' );
		return $gBitThemes->getStyleUrl( $pStyle );
	}
	function getStylePath( $pStyle = NULL ) {
		global $gBitThemes;
		deprecated( 'This is now in BitThemes instead of BitSystem.' );
		return $gBitThemes->getStylePath( $pStyle );
	}
}
?>
