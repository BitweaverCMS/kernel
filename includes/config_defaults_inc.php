<?php
/**
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

// when running scripts
global $gShellScript;
if( !empty( $gShellScript ) ) {
	$siteName = 'localhost';
	// keep notices quiet
	$serverDefaults = array(
		'DOCUMENT_ROOT' => dirname( dirname( dirname( __FILE__ ) ) ),
		'HTTP_HOST' => $siteName,
		'HTTP_SERVER_VARS' => '',
		'HTTP_USER_AGENT' => 'cron',
		'REMOTE_ADDR' => $siteName,
		'REQUEST_METHOD' => 'GET',
		'REQUEST_URI' => __FILE__,
		'REQUEST_URI' => __FILE__,
		'SCRIPT_URI' => $_SERVER['SCRIPT_FILENAME'],
		'SCRIPT_URL' => $_SERVER['SCRIPT_NAME'],
		'SERVER_ADDR' => $siteName,
		'SERVER_ADMIN' => 'root@'.$siteName,
		'SERVER_NAME' => '',
		'SERVER_PROTOCOL' => 'http',
	);

	foreach( $serverDefaults as $key=>$value ) {
		if( empty( $_SERVER[$key] ) ) {
			$_SERVER[$key] = $value;
		}
	}

	// Process some global arguments
	global $gArgs, $argv;
	$gArgs = array();
	if( $argv ) {
		foreach( $argv AS $arg ) {
			$argKey = $arg;
			if( strpos( $arg, '--' ) === 0 ) {
				$argKey = substr( $arg, 2 );
			}
			$argValue = TRUE;
			if( strpos( $arg, '=' ) ) {
				$argKey = substr( $arg, 2, strpos( $arg, '=' )-2 );
				$argValue = substr( $arg, (strpos( $arg, '=' ) +1) );
			}
			switch( $argKey ) {
				case 'debug':
					$gDebug = $argValue;
					break;
			}
			$gArgs[$argKey] = $argValue;
			$_REQUEST[$argKey] = $argValue;
		}
	}
}

// =================== Essential Defines ===================
// These defines can be set in config/kernel/config_inc.php. If they haven't been set, we set default values here
// database settings
if( !defined( 'BIT_DB_PREFIX' ) ) {
	define( 'BIT_DB_PREFIX', '' );
}
if( !defined( 'BIT_CACHE_OBJECTS' ) ) {
	define( 'BIT_CACHE_OBJECTS', TRUE );
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
	define( 'BIT_PHP_ERROR_REPORTING', E_ALL & ~E_DEPRECATED & ~E_STRICT );
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

/* Uncomment to switch to role team model ...
if( !defined( 'ROLE_MODEL' )) {
	define( 'ROLE_MODEL', true );
} */

if( !defined( 'ANONYMOUS_TEAM_ID' ) ) {
	define( 'ANONYMOUS_TEAM_ID', -1 );
}

// Uncomment the following line if you require attachment and file id's to match the content id
// This is used to simplify content mamagment where fisheye and treasury content is used internally
//define( 'LINKED_ATTACHMENTS', true );
// Empty SCRIPT_NAME and incorrect SCRIPT_NAME due to php-cgiwrap - wolff_borg
if( empty( $_SERVER['SCRIPT_NAME'] ) ) {
	$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_URL'];
}

// BIT_ROOT_URL should be set as soon as the system is installed. until then we
// need to make sure we have the correct value, otherwise installations won't
// work. The recent changes have caused problems during installation. i'll try
// combining both methods by applying the less successful one after the more
// successful one - xing
if( !defined( 'BIT_ROOT_URL' ) ) {
	// version one which seems to only cause problems seldomly
	preg_match( '/.*'.basename( dirname( dirname( __FILE__ ) ) ).'\//', $_SERVER['SCRIPT_NAME'], $match );
	$subpath = ( isset($match[0] ) ) ? $match[0] : '/';
	// version two which doesn't work well on it's own
	if( $subpath == "/" ) {
		$subpath = dirname( dirname( $_SERVER['SCRIPT_NAME'] ) );
		$subpath .= ( substr( $subpath,-1,1 )!='/' ) ? '/' : '';
	}
	$subpath = str_replace( '//', '/', str_replace( "\\", '/', $subpath ) ); // do some de-windows-ification
	define( 'BIT_ROOT_URL', $subpath );
}

// If BIT_ROOT_URI hasn't been set yet, we'll try to get one from the super global $_SERVER.
// This works with apache - not sure about other servers.
if( !defined( 'BIT_BASE_URI' )) {
  // Added check for IIS $_SERVER['HTTPS'] uses 'off' value - wolff_borg
	define( 'BIT_BASE_URI', 'http'.((!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS'] != 'off')?'s':'').'://'.(empty($_SERVER['HTTP_HOST'])?'localhost':$_SERVER['HTTP_HOST']) );
}

if( !defined( 'BIT_ROOT_URI' )) {
  // Added check for IIS $_SERVER['HTTPS'] uses 'off' value - wolff_borg
	define( 'BIT_ROOT_URI', BIT_BASE_URI.BIT_ROOT_URL );
}

// custom storage host
if( !defined( 'STORAGE_BASE_URI' ) ) {
	define( 'STORAGE_BASE_URI', BIT_BASE_URI );
}
// custom storage host
if( !defined( 'STORAGE_HOST_URI' ) ) {
	define( 'STORAGE_HOST_URI', BIT_ROOT_URI );
}

if( substr_count( $_SERVER['HTTP_HOST'], '.' ) >= 2 ) {
	define( 'BIT_BASE_HOST', substr( $_SERVER['HTTP_HOST'], strpos( $_SERVER['HTTP_HOST'], '.') + 1 ) );
} else {
	define( 'BIT_BASE_HOST',  $_SERVER['HTTP_HOST'] );
}

// set the currect version of bitweaver
// if this version of bitweaver needs a visit to the installer, update the number in /bit_setup_inc.php
if( !defined( 'BIT_MAJOR_VERSION' ) ) {
	define( 'BIT_MAJOR_VERSION',	'4' );
	define( 'BIT_MINOR_VERSION',	'0' );
	define( 'BIT_SUB_VERSION',		'0' );
	define( 'BIT_LEVEL',			'' ); // dev < alpha < beta < RC# < '' < pl
}

// When updating to certain versions of bitweaver, we need to force a visit to the installer to fix certain stuff in the database.
// Enter the minimum version number here in the format: '2.1.0-beta'
if( !defined( 'MIN_BIT_VERSION' ) ) {
	define( 'MIN_BIT_VERSION', '4.0.0' );
}

// These defines have to happen FIRST because core classes depend on them.
// This means these packages *CANNOT* be renamed
define( 'INSTALL_PKG_PATH',   BIT_ROOT_PATH.'install/' );
define( 'INSTALL_PKG_INCLUDE_PATH',   INSTALL_PKG_PATH.'includes/' );
define( 'INSTALL_PKG_CLASS_PATH',   INSTALL_PKG_INCLUDE_PATH.'classes/' );
define( 'INSTALL_PKG_URL',    BIT_ROOT_URL.'install/' );
define( 'KERNEL_PKG_DIR',     'kernel' );
define( 'KERNEL_PKG_NAME',    'kernel' );
define( 'KERNEL_PKG_PATH',    BIT_ROOT_PATH.'kernel/' );
define( 'KERNEL_PKG_URL',    BIT_ROOT_URL.KERNEL_PKG_DIR.'/' );
define( 'KERNEL_PKG_INCLUDE_PATH', KERNEL_PKG_PATH.'includes/' );
define( 'KERNEL_PKG_CLASS_PATH', KERNEL_PKG_INCLUDE_PATH.'classes/' );
define( 'KERNEL_PKG_ADMIN_PATH', KERNEL_PKG_PATH.'admin/' );

define( 'CONFIG_PKG_PATH',    BIT_ROOT_PATH.'config/' );
define( 'CONFIG_PKG_INCLUDE_PATH', CONFIG_PKG_PATH.'includes/' );
define( 'CONFIG_PKG_CLASS_PATH', CONFIG_PKG_INCLUDE_PATH.'classes/' );
define( 'CONFIG_PKG_ADMIN_PATH', CONFIG_PKG_PATH.'admin/' );

define( 'LANGUAGES_PKG_PATH', BIT_ROOT_PATH.'languages/' );
define( 'LANGUAGES_PKG_INCLUDE_PATH', LANGUAGES_PKG_PATH.'includes/' );
define( 'LANGUAGES_PKG_CLASS_PATH', LANGUAGES_PKG_INCLUDE_PATH.'classes/' );
define( 'LANGUAGES_PKG_ADMIN_PATH', LANGUAGES_PKG_PATH.'admin/' );

define( 'LIBERTY_PKG_DIR', 'liberty' );
define( 'LIBERTY_PKG_NAME', 'liberty' );
define( 'LIBERTY_PKG_PATH', BIT_ROOT_PATH.'liberty/' );
define( 'LIBERTY_PKG_INCLUDE_PATH', LIBERTY_PKG_PATH.'includes/' );
define( 'LIBERTY_PKG_CLASS_PATH', LIBERTY_PKG_INCLUDE_PATH.'classes/' );
define( 'LIBERTY_PKG_ADMIN_PATH', LIBERTY_PKG_PATH.'admin/' );

if( !defined( 'STORAGE_PKG_NAME' ) ) {
	define( 'STORAGE_PKG_NAME',   'storage' );
}
if( !defined( 'STORAGE_PKG_PATH' ) ) {
	define( 'STORAGE_PKG_PATH',   BIT_ROOT_PATH.'storage/' );
}
if( !defined( 'STORAGE_PKG_INCLUDE_PATH' ) ) {
	define( 'STORAGE_PKG_INCLUDE_PATH',   STORAGE_PKG_PATH.'includes/' );
}
if( !defined( 'STORAGE_PKG_CLASS_PATH' ) ) {
	define( 'STORAGE_PKG_CLASS_PATH',   STORAGE_PKG_INCLUDE_PATH.'classes/' );
}
if( !defined( 'STORAGE_PKG_ADMIN_PATH' ) ) {
	define( 'STORAGE_PKG_ADMIN_PATH',   STORAGE_PKG_PATH.'admin/' );
}


define( 'THEMES_PKG_PATH',    BIT_ROOT_PATH.'themes/' );
define( 'THEMES_PKG_INCLUDE_PATH', THEMES_PKG_PATH.'includes/' );
define( 'THEMES_PKG_CLASS_PATH', THEMES_PKG_INCLUDE_PATH.'classes/' );
define( 'THEMES_PKG_ADMIN_PATH', THEMES_PKG_PATH.'admin/' );

define( 'USERS_PKG_PATH',     BIT_ROOT_PATH.'users/' );
define( 'USERS_PKG_INCLUDE_PATH', USERS_PKG_PATH.'includes/' );
define( 'USERS_PKG_CLASS_PATH', USERS_PKG_INCLUDE_PATH.'classes/' );
define( 'USERS_PKG_ADMIN_PATH', USERS_PKG_PATH.'admin/' );

define( 'UTIL_PKG_PATH', BIT_ROOT_PATH.'util/' );
define( 'UTIL_PKG_INCLUDE_PATH', BIT_ROOT_PATH.'util/includes/' );
define( 'UTIL_PKG_CLASS_PATH', UTIL_PKG_INCLUDE_PATH.'classes/' );
define( 'UTIL_PKG_ADMIN_PATH', UTIL_PKG_PATH.'admin/' );

if( !defined( 'EXTERNAL_LIBS_PATH' ) ) {
	define( 'EXTERNAL_LIBS_PATH',      BIT_ROOT_PATH.'externals/' );
}

if( !defined( 'TEMP_PKG_PATH' ) ) {
	define( 'TEMP_PKG_PATH', sys_get_temp_dir().'/php/'.$_SERVER['HTTP_HOST'] ).'/';
	if( !file_exists( TEMP_PKG_PATH ) ) {
		mkdir( TEMP_PKG_PATH, 2775, TRUE );
	}
}

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
	$gPreScan = array( 'config', 'kernel', 'storage', 'liberty', 'themes', 'users' );
}

// here we set the default thumbsizes we use in bitweaver.
// order matters since successively smaller thumbs are used from the preceding thumb for speed increase.
// you can override these by populating this hash in your config/kernel/config_inc.php
global $gThumbSizes;
if( empty( $gThumbSizes )) {
	$gThumbSizes = array(
		'large'  => array( 'width' => 1200, 'height' => 900 ),
		'medium' => array( 'width' => 800, 'height' => 600 ),
		'small'  => array( 'width' => 400, 'height' => 300 ),
		'avatar' => array( 'width' => 200, 'height' => 150 ),
		'icon'   => array( 'width' => 100,  'height' => 100 ),
	);
}
?>
