<?php
/**
 * @version $Header$
 * @package kernel
 * @subpackage functions
 */

/**
 * required setup
 */

// include the bitweaver configuration file - this needs to happen first
$config_file = empty( $_SERVER['CONFIG_INC'] ) ? BIT_ROOT_PATH.'config/kernel/config_inc.php' : $_SERVER['CONFIG_INC'];
if( file_exists( $config_file ) ) {
	include_once( $config_file );
}

// =================== Essential Defines ===================
// These defines can be set in config/kernel/config_inc.php. If they haven't been set, we set default values here
// database settings
if( !defined( 'BIT_DB_PREFIX' ) ) {
	define( 'BIT_DB_PREFIX', '' );
}
if( !defined( 'BIT_QUERY_CACHE_TIME' ) ) {
	define( 'BIT_QUERY_CACHE_TIME', 86400 );
}
// default theme after installation
if( !defined( 'DEFAULT_THEME' ) ) {
	define( 'DEFAULT_THEME', 'basic' );
}
if( !defined( 'DISPLAY_ERRORS' ) ) {
	define( 'DISPLAY_ERRORS', 0 );
}
// name of session variable in browser cookie
if( !defined( 'BIT_SESSION_NAME' ) ) {
	define( 'BIT_SESSION_NAME', 'BWSESSION' );
}
// define where errors are sent
if( !defined( 'BIT_PHP_ERROR_REPORTING' ) ) {
	define( 'BIT_PHP_ERROR_REPORTING', E_ALL );
}
// don't change / set _IDs unless you know exactly what you are doing
if( !defined( 'ROOT_USER_ID' ) ) {
	define( 'ROOT_USER_ID', 1 );
}
if( !defined( 'ANONYMOUS_USER_ID' ) ) {
	define( 'ANONYMOUS_USER_ID', -1 );
}
if( !defined( 'ANONYMOUS_GROUP_ID' ) ) {
	define( 'ANONYMOUS_GROUP_ID', -1 );
}
if( !defined( 'EVIL_EXTENSION_PATTERN' )) {
	define( 'EVIL_EXTENSION_PATTERN', "#\.(htaccess|pl|php|php3|php4|phtml|py|cgi|asp|jsp|sh|shtml)$#i" );
}

// Empty PHP_SELF and incorrect SCRIPT_NAME due to php-cgiwrap - wolff_borg
if( empty( $_SERVER['PHP_SELF'] ) ) {
	$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_URL'];
}

// BIT_ROOT_URL should be set as soon as the system is installed. until then we
// need to make sure we have the correct value, otherwise installations won't
// work. The recent changes have caused problems during installation. i'll try
// combining both methods by applying the less successful one after the more
// successful one - xing
if( !defined( 'BIT_ROOT_URL' ) ) {
	// version one which seems to only cause problems seldomly
	preg_match( '/.*'.basename( dirname( dirname( __FILE__ ) ) ).'\//', $_SERVER['PHP_SELF'], $match );
	$subpath = ( isset($match[0] ) ) ? $match[0] : '/';
	// version two which doesn't work well on it's own
	if( $subpath == "/" ) {
		$subpath = dirname( dirname( $_SERVER['PHP_SELF'] ) );
		$subpath .= ( substr( $subpath,-1,1 )!='/' ) ? '/' : '';
	}
	$subpath = str_replace( '//', '/', str_replace( "\\", '/', $subpath ) ); // do some de-windows-ification
	define( 'BIT_ROOT_URL', $subpath );
}

// when running scripts
global $gShellScript;
if( !empty( $gShellScript ) ) {
	// keep notices quiet
	$_SERVER['SCRIPT_URL'] = '';
	$_SERVER['HTTP_HOST'] = 'localhost';
	$_SERVER['HTTP_USER_AGENT'] = 'cron';
	$_SERVER['SERVER_NAME'] = '';
	$_SERVER['HTTP_SERVER_VARS'] = '';
	$_SERVER['REMOTE_ADDR'] = 'localhost';
	if( empty( $_SERVER['SERVER_ADMIN'] ) ) {
		$_SERVER['SERVER_ADMIN'] = 'root@localhost';
	}

	// Process some global arguments
	global $gArgs, $argv;
	$gArgs = array();
	if( $argv ) {
		foreach( $argv AS $arg ) {
			switch( $arg ) {
				case '--debug':
					$gDebug = TRUE;
					break;
				case strpos( $arg, '--' ) === 0:
					if( strpos( $arg, '=' ) ) {
						$gArgs[substr( $arg, 2, strpos( $arg, '=' )-2 )] = (int)substr( $arg, (strpos( $arg, '=' ) +1) );
					} else {
						$gArgs[substr( $arg, 2 )] = TRUE;
					}
					break;
			}
		}
	}
}

// If BIT_ROOT_URI hasn't been set yet, we'll try to get one from the super global $_SERVER.
// This works with apache - not sure about other servers.
if( !defined( 'BIT_ROOT_URI' )) {
  // Added check for IIS $_SERVER['HTTPS'] uses 'off' value - wolff_borg
	define( 'BIT_ROOT_URI', 'http'.((!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS'] != 'off')?'s':'').'://'.(empty($_SERVER['HTTP_HOST'])?'localhost':$_SERVER['HTTP_HOST']).BIT_ROOT_URL );
}

// custom storage host
if( !defined( 'STORAGE_HOST_URI' ) ) {
	define( 'STORAGE_HOST_URI', BIT_ROOT_URI );
}

// set the currect version of bitweaver
// if this version of bitweaver needs a visit to the installer, update the number in /bit_setup_inc.php
if( !defined( 'BIT_MAJOR_VERSION' ) ) {
	define( 'BIT_MAJOR_VERSION',	'3' );
	define( 'BIT_MINOR_VERSION',	'0' );
	define( 'BIT_SUB_VERSION',		'0' );
	define( 'BIT_LEVEL',			'alpha' ); // dev < alpha < beta < RC# < '' < pl
}

// When updating to certain versions of bitweaver, we need to force a visit to the installer to fix certain stuff in the database.
// Enter the minimum version number here in the format: '2.1.0-beta'
if( !defined( 'MIN_BIT_VERSION' ) ) {
	define( 'MIN_BIT_VERSION', '2.1.0-RC1' );
}

// These defines have to happen FIRST because core classes depend on them.
// This means these packages *CANNOT* be renamed
define( 'INSTALL_PKG_PATH',   BIT_ROOT_PATH.'install/' );
define( 'INSTALL_PKG_URL',    BIT_ROOT_URL.'install/' );
define( 'KERNEL_PKG_DIR',     'kernel' );
define( 'KERNEL_PKG_NAME',    'kernel' );
define( 'KERNEL_PKG_PATH',    BIT_ROOT_PATH.'kernel/' );
define( 'CONFIG_PKG_PATH',    BIT_ROOT_PATH.'config/' );
define( 'LANGUAGES_PKG_PATH', BIT_ROOT_PATH.'languages/' );
define( 'LIBERTY_PKG_DIR',    'liberty' );
define( 'LIBERTY_PKG_NAME',   'liberty' );
define( 'LIBERTY_PKG_PATH',   BIT_ROOT_PATH.'liberty/' );
define( 'STORAGE_PKG_NAME',   'storage' );
define( 'STORAGE_PKG_PATH',   BIT_ROOT_PATH.'storage/' );
define( 'THEMES_PKG_PATH',    BIT_ROOT_PATH.'themes/' );
define( 'USERS_PKG_PATH',     BIT_ROOT_PATH.'users/' );
define( 'UTIL_PKG_PATH',      BIT_ROOT_PATH.'util/' );


// =================== Global Variables ===================
// If for any reason this isn't set, nothing will work - nada, zilch...
if( empty( $gBitDbHost ) ) {
	$gBitDbHost   = 'localhost';
}

// $gPreScan can be used to specify the order in which packages are scanned by
// the kernel.  In the example provided below, the kernel package is processed
// first, followed by the users and liberty packages.  Any packages not
// specified in $gPreScan are processed in the traditional order
global $gPreScan;
if( empty( $gPreScan ) ) {
	$gPreScan = array( 'kernel', 'storage', 'liberty', 'themes', 'users' );
}

// here we set the default thumbsizes we use in bitweaver.
// you can override these by populating this hash in your config/kernel/config_inc.php
global $gThumbSizes;
if( empty( $gThumbSizes )) {
	$gThumbSizes = array(
		'icon'   => array( 'width' => 48,  'height' => 48 ),
		'avatar' => array( 'width' => 100, 'height' => 100 ),
		'small'  => array( 'width' => 160, 'height' => 120 ),
		'medium' => array( 'width' => 400, 'height' => 300 ),
		'large'  => array( 'width' => 800, 'height' => 600 ),
	);
}
?>
