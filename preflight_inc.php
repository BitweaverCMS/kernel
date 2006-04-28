<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/preflight_inc.php,v 1.12 2006/04/28 15:25:55 squareing Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * * Return system defined temporary directory.
 * In Unix, this is usually /tmp
 * In Windows, this is usually c:\windows\temp or c:\winnt\temp
 * \static
 */
function getTempDir() {
	static $tempdir;
	if (!$tempdir) {
		global $gTempDir;
		if( !empty( $gTempDir ) ) {
			$tempdir = $gTempDir;
		} else {
			if (!isWindows()) {
				$tempfile = tempnam(((@ini_get('safe_mode'))
							? ($_SERVER['DOCUMENT_ROOT'] . '/temp/')
							: (false)), 'foo');
				$tempdir = dirname($tempfile);
				@unlink($tempfile);
			} else {
				$tempdir = getenv("TMP");
			}
		}
	}
	return $tempdir;
}

/**
 * * Return true if windows, otherwise false
 * \static
 */
function isWindows() {
	static $windows;
	if (!isset($windows)) {
		$windows = substr(PHP_OS, 0, 3) == 'WIN';
	}
	return $windows;
}

function mkdir_p($target, $perms = 0777) {
	global $gDebug;

	if (ini_get('safe_mode')) {
		$target = preg_replace('/^\/tmp/', $_SERVER['DOCUMENT_ROOT'] . '/temp', $target);
	}
	//echo "mkdir_p($target, $perms)<br />\n";
	if (file_exists($target) || is_dir($target)) {
		if ($gDebug) echo "mkdir_p() - file already exists $target<br>";
		return 0;
	}

	if (isWindows()) {
	} else {
		if (substr($target, 0, 1) != '/') {
			if ($gDebug) echo "mkdir_p() - prepending with a /<br>";
			$target = "/$target";
		}
		if( ereg('\.\.', $target) ) {
			if ($gDebug) echo "mkdir_p() - invalid Unix path $target<br>";
			return 0;
		}
	}

	$oldu = umask(0);
	if (@mkdir($target, $perms)) {
		umask($oldu);
		if ($gDebug) echo "mkdir_p() - creating $target<br>";
		return 1;
	} else {
		umask($oldu);
		$parent = substr($target, 0, (strrpos($target, '/')));
		if ($gDebug) {
			echo "mkdir_p() - trying to create parent $parent<br>";
		}
		if (mkdir_p($parent, $perms)) {
			// make the actual target!
			@mkdir($target, $perms);
			return 1;
		}
	}
}

/**
 * Used to check php.ini settings
 * @param pName setting name
 * @param pValue setting value
 * @param pComp setting comparison
**/
function chkPhpSetting($pName, $pValue, $pComp='') {
	$actual = ini_get($pName);
	eregi("^([0-9]+)[KMG]$", $actual, $x);
	$actual = (isset($x)) ? $x[1] : $actual;
	switch($pComp) {
		case ">=":
			$success = ($actual >= $pValue) ? 1 : 0;
			break;
		default:
			$success = ($actual == $pValue) ? 1 : 0;
	}
	return $success;
	// redundant $data = serialize(array("check" => $pValue, "actual" => $actual));
}

// added check for Windows - wolff_borg - see http://bugs.php.net/bug.php?id=27609
function bw_is_writeable($filename) {
	if (!isWindows()) {
		return is_writeable($filename);
	} else {
		$writeable = FALSE;
		if (is_dir($filename)) {
			$rnd = rand();
			$writeable = @fopen($filename."/".$rnd,"a");
			if ($writeable) {
				fclose($writeable);
				unlink($filename."/".$rnd);
				$writeable = true;
			}
		} else {
			$writeable = @fopen($filename,"a");
			if ($writeable) {
				fclose($writeable);
				$writeable = true;
			}
		}
		return $writeable;
	}
}

// for PHP<4.2.0
if (!function_exists('array_fill')) {
	require_once(KERNEL_PKG_PATH . 'array_fill.func.php');
}

// if you have a situation where you simply print any $_REQUEST on screen, use this to remove any malicious stuff
function detoxify( &$pParamHash, $pHtml = FALSE ) {
	if( !empty( $pParamHash ) && is_array( $pParamHash ) ) {
		foreach( $pParamHash as $key => $value ) {
			if( isset( $value ) && is_array( $value ) ) {
				detoxify( $value );
			} else {
				if( $pHtml ) {
					$pParamHash[$key] = htmlspecialchars( urldecode( $value ) );
				} elseif( preg_match( "/<script[^>]*>/i", urldecode( $value ) ) ) {
					unset( $pParamHash[$key] );
				}
			}
		}
	}
}

// simple function to include in deprecated function calls. makes the developer replace with newer code
function deprecated( $pReplace = NULL ) {
	$trace = debug_backtrace();
	//vd( $trace);
	$out = "Deprecated function call:\n\tfunction: ".$trace[1]['class']."::".$trace[1]['function']."()\n\tfile: ".$trace[1]['file']."\n\tline: ".$trace[1]['line'];
	if( !empty( $pReplace ) ) {
		$out .= "\n\treplace with: ".$pReplace;
	}
	vd( $out );
}

?>
