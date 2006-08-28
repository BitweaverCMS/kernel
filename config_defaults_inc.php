<?php
/**
 * @package kernel
 * @subpackage functions
 */

/**
 * required setup
 */
$config_file = empty($_SERVER['CONFIG_INC']) ? BIT_ROOT_PATH.'kernel/config_inc.php' : $_SERVER['CONFIG_INC'];

if (file_exists($config_file ) ) {
    include_once($config_file);
}
// If these weren't defined in config_inc, let's define the now
if( !defined( 'BIT_DB_PREFIX' ) ) {
	define( 'BIT_DB_PREFIX', '' );
}
if( (!isset($gBitDbHost)) || empty($gBitDbHost) ) {
	$gBitDbHost   = 'localhost';
}
if( !defined( 'DEFAULT_THEME' ) ) {
	define( 'DEFAULT_THEME', 'basic' );
}
if( !defined( 'BIT_QUERY_CACHE_TIME' ) ) {
	define( 'BIT_QUERY_CACHE_TIME', 86400 );
}
if( !defined( 'DISPLAY_ERRORS' ) ) {
	define( 'DISPLAY_ERRORS', 0 );
}

// Empty PHP_SELF and incorrect SCRIPT_NAME due to php-cgiwrap - wolff_borg
if (empty($_SERVER['PHP_SELF']))
	$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_URL'];

// this is broken if the virtual directory under the webserver is
// not the same name as the physical directory on the drive - wolff_borg
if (!defined('BIT_ROOT_URL' )) {
    preg_match('/.*'.basename(dirname(dirname(__FILE__ ) ) ).'\//', $_SERVER['PHP_SELF'], $match  );
    $subpath = ( isset($match[0] ) ) ? $match[0] : '/';
    define('BIT_ROOT_URL', $subpath );
}

if( !defined( 'BIT_SESSION_NAME' ) ) {
	define( 'BIT_SESSION_NAME', 'BWSESSION' );
}
if( !defined( 'BIT_PHP_ERROR_REPORTING' ) ) {
	define( 'BIT_PHP_ERROR_REPORTING', E_ALL );
}
// This should *only* be changed if you are certain you know what you are doing.
if( !defined( 'ROOT_USER_ID' ) ) {
	define( 'ROOT_USER_ID', 1 );
}
// This should *only* be changed if you are certain you know what you are doing.
if( !defined( 'ANONYMOUS_USER_ID' ) ) {
	define( 'ANONYMOUS_USER_ID', -1 );
}
// This should *only* be changed if you are certain you know what you are doing.
if( !defined( 'ANONYMOUS_GROUP_ID' ) ) {
	define( 'ANONYMOUS_GROUP_ID', -1 );
}
// $gPreScan can be used to specify the order in which packages are scanned by the kernel.
// In the example provided below, the kernel package is processed first, followed by the users and liberty packages.
// Any packages not specified in $gPreScan are processed in the traditional order
global $gPreScan;
if( empty( $gPreScan ) ) {
	$gPreScan = array( 'kernel', 'users', 'liberty' );
}
?>
