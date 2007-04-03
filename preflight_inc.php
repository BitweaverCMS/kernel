<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/preflight_inc.php,v 1.23 2007/04/03 14:28:45 squareing Exp $
 * @package kernel
 * @subpackage functions
 */

// some PHP compatability issues need to be dealt with
// array_fill
if( !function_exists( 'array_fill' )) {
	require_once( KERNEL_PKG_PATH.'array_fill.func.php' );
}
// str_split
include_once( UTIL_PKG_PATH.'PHP_Compat/Compat/Function/str_split.php' );

/**
 * Return system defined temporary directory.
 * In Unix, this is usually /tmp
 * In Windows, this is usually c:\windows\temp or c:\winnt\temp
 * 
 * @access public
 * @return system defined temporary directory.
 */
function get_temp_dir() {
	static $tempdir;
	if( !$tempdir ) {
		global $gTempDir;
		if( !empty( $gTempDir ) ) {
			$tempdir = $gTempDir;
		} else {
			if( !is_windows() ) {
				$tempfile = tempnam((( @ini_get( 'safe_mode' ))
							?( $_SERVER['DOCUMENT_ROOT'] . '/temp/' )
							:( FALSE )), 'foo' );
				$tempdir = dirname( $tempfile );
				@unlink( $tempfile );
			} else {
				$tempdir = getenv( "TMP" );
			}
		}
	}
	return $tempdir;
}

/**
 * is_windows 
 * 
 * @access public
 * @return TRUE if we are on windows, FALSE otherwise
 */
function is_windows() {
	static $windows;
	if( !isset( $windows )) {
		$windows = substr(PHP_OS, 0, 3) == 'WIN';
	}
	return $windows;
}

/**
 * Recursively create directories
 * 
 * @param array $pTarget target directory
 * @param float $pPerms octal permissions
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function mkdir_p( $pTarget, $pPerms = 0755 ) {
	global $gDebug;
	//$gDebug = TRUE

	$pTarget = trim( $pTarget );
	if( $pTarget == '/' ) {
		return 1;
	}

	if( ini_get( 'safe_mode' )) {
		$pTarget = preg_replace('/^\/tmp/', $_SERVER['DOCUMENT_ROOT'] . '/temp', $pTarget);
	}

	if( file_exists( $pTarget ) || is_dir( $pTarget )) {
		if( $gDebug ) echo "mkdir_p() - file already exists $pTarget<br />";
		return 0;
	}

	if( !is_windows() ) {
		if( substr( $pTarget, 0, 1 ) != '/' ) {
			if( $gDebug ) echo "mkdir_p() - prepending with a /<br />";
			$pTarget = "/$pTarget";
		}
		if( ereg( '\.\.', $pTarget )) {
			if( $gDebug ) echo "mkdir_p() - invalid Unix path $pTarget<br />";
			return 0;
		}
	}

	$oldu = umask( 0 );
	if( @mkdir( $pTarget, $pPerms )) {
		if( $gDebug ) echo "mkdir_p() - creating $pTarget<br />";
		umask( $oldu );
		return 1;
	} else {
		if( $gDebug ) echo "mkdir_p() - trying to create parent $parent<br />";
		umask( $oldu );
		$parent = substr( $pTarget, 0, ( strrpos( $pTarget, '/' )));

		// recursively create parents
		if( mkdir_p( $parent, $pPerms )) {
			// make the actual target!
			if( @mkdir( $pTarget, $pPerms )) {
				return 1;
			} else {
				error_log( "mkdir() - could not create $pTarget" );
			}
		}
	}
}

/**
 * check to see if particular directories are wroteable by bitweaver
 * added check for Windows - wolff_borg - see http://bugs.php.net/bug.php?id=27609
 * 
 * @param array $pPath path to file or dir
 * @access public
 * @return TRUE on success, FALSE on failure
 */
function bw_is_writeable( $pPath ) {
	if( !is_windows() ) {
		return is_writeable( $pPath );
	} else {
		$writeable = FALSE;
		if( is_dir( $pPath )) {
			$rnd = rand();
			$writeable = @fopen( $pPath."/".$rnd, "a" );
			if( $writeable ) {
				fclose( $writeable );
				unlink( $pPath."/".$rnd );
				$writeable = TRUE;
			}
		} else {
			$writeable = @fopen( $pPath,"a" );
			if( $writeable ) {
				fclose( $writeable );
				$writeable = TRUE;
			}
		}
		return $writeable;
	}
}

/**
 * clean up an array of values and remove any dangerous html - particularly useful for cleaning up $_GET and $_REQUEST
 * 
 * @param array $pParamHash array to be cleaned
 * @param boolean $pHtml set true to escape HTML code as well
 * @access public
 * @return void
 */
function detoxify( &$pParamHash, $pHtml = FALSE ) {
	if( !empty( $pParamHash ) && is_array( $pParamHash ) ) {
		foreach( $pParamHash as $key => $value ) {
			if( isset( $value ) && is_array( $value ) ) {
				detoxify( $value );
			} else {
				if( $pHtml ) {
					$pParamHash[$key] = htmlspecialchars( urldecode( $value ), ENT_NOQUOTES );
				} elseif( preg_match( "/<script[^>]*>/i", urldecode( $value ) ) ) {
					unset( $pParamHash[$key] );
				}
			}
		}
	}
}

/* \brief  substr with a utf8 string - works only with $start and $length positive or nuls
* This function is the same as substr but works with multibyte
* In a multybyte sequence, the first byte of a multibyte sequence that represents a non-ASCII character is always in the range 0xC0 to 0xFD
* and it indicates how many bytes follow for this character.
* All further bytes in a multibyte sequence are in the range 0x80 to 0xBF.
*/
/**
 * Check mb_substr availability
 */
if( function_exists('mb_substr' ) ) {
	mb_internal_encoding( "UTF-8" );
} else {
	function mb_substr( $str, $start, $len = '', $encoding = "UTF-8" ) {
		$limit = strlen( $str );
		for( $s = 0; $start > 0;--$start ) {
			if( $s >= $limit ) {
				break;
			}

			if( $str[$s] <= "\x7F" ) {
				++$s;
			} else {
				++$s; // skip length
				while( $str[$s] >= "\x80" && $str[$s] <= "\xBF" ) {
					++$s;
				}
			}
		}
		if( $len == '' ) {
			return substr( $str, $s );
		}
		else {
			// found the real end
			for( $e = $s; $len > 0; --$len ) {
				if( $e >= $limit ) {
					break;
				}

				if( $str[$e] <= "\x7F" ) {
					++$e;
				} else {
					++$e; //skip length
					while( $str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit ) {
						++$e;
					}
				}
			}
		}
		return substr( $str, $s, $e - $s );
	}
}

/**
 * simple function to include in deprecated function calls. makes the developer replace with newer code
 * 
 * @param array $pReplace code that needs replacing
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function deprecated( $pReplace = NULL ) {
	$trace = debug_backtrace();
	//vd( $trace);
	$out = "Deprecated function call:\n\tfunction: ".$trace[1]['class']."::".$trace[1]['function']."()\n\tfile: ".$trace[1]['file']."\n\tline: ".$trace[1]['line'];
	if( !empty( $pReplace ) ) {
		$out .= "\n\t".$pReplace;
	}
	if( !defined( 'IS_LIVE' ) || IS_LIVE == FALSE ) {
		vd( $out );
	} else {
		error_log( $out );
	}
}

/**
 * html encode all characters
 * taken from: http://www.bbsinc.com/iso8859.html
 * 
 * @param sting $pData string that might contain an email address
 * @access public
 * @return encoded email address
 */
define( 'EMAIL_ADDRESS_REGEX', '\w[-.\w]*\@[-.\w]+\.\w{2,3}' );
function encode_email_addresses( $pData ) {
	$trans = array(
		// Upper case
		'A' => '&#065;',
		'B' => '&#066;',
		'C' => '&#067;',
		'D' => '&#068;',
		'E' => '&#069;',
		'F' => '&#070;',
		'G' => '&#071;',
		'H' => '&#072;',
		'I' => '&#073;',
		'J' => '&#074;',
		'K' => '&#075;',
		'L' => '&#076;',
		'M' => '&#077;',
		'N' => '&#078;',
		'O' => '&#079;',
		'P' => '&#080;',
		'Q' => '&#081;',
		'R' => '&#082;',
		'S' => '&#083;',
		'T' => '&#084;',
		'U' => '&#085;',
		'V' => '&#086;',
		'W' => '&#087;',
		'X' => '&#088;',
		'Y' => '&#089;',
		'Z' => '&#090;',

		// lower case
		'a' => '&#097;',
		'b' => '&#098;',
		'c' => '&#099;',
		'd' => '&#100;',
		'e' => '&#101;',
		'f' => '&#102;',
		'g' => '&#103;',
		'h' => '&#104;',
		'i' => '&#105;',
		'j' => '&#106;',
		'k' => '&#107;',
		'l' => '&#108;',
		'm' => '&#109;',
		'n' => '&#110;',
		'o' => '&#111;',
		'p' => '&#112;',
		'q' => '&#113;',
		'r' => '&#114;',
		's' => '&#115;',
		't' => '&#116;',
		'u' => '&#117;',
		'v' => '&#118;',
		'w' => '&#119;',
		'x' => '&#120;',
		'y' => '&#121;',
		'z' => '&#122;',

		// digits
		'0' => '&#048;',
		'1' => '&#049;',
		'2' => '&#050;',
		'3' => '&#051;',
		'4' => '&#052;',
		'5' => '&#053;',
		'6' => '&#054;',
		'7' => '&#055;',
		'8' => '&#056;',
		'9' => '&#057;',

		// special chars
		'_' => '&#095;',
		'-' => '&#045;',
		'.' => '&#046;',
		'@' => '&#064;',

		//'[' => '&#091;',
		//']' => '&#093;',
		//'|' => '&#124;',
		//'{' => '&#123;',
		//'}' => '&#125;',
		//'~' => '&#126;',
	);
	preg_match_all( "!\b".EMAIL_ADDRESS_REGEX."\b!", $pData, $addresses );
	foreach( $addresses[0] as $address ) {
		$encoded = strtr( $address, $trans );
		$pData = preg_replace( "/\b".preg_quote( $address )."\b/", $encoded, $pData );
	}

	return $pData;
}

// --------------- apparently not in use anymore
// we do this in the installer only and we have our own functions in there
/**
 * Used to check php.ini settings
 * @param pName setting name
 * @param pValue setting value
 * @param pComp setting comparison
**/
#function chkPhpSetting($pName, $pValue, $pComp='') {
#	$actual = ini_get($pName);
#	eregi("^([0-9]+)[KMG]$", $actual, $x);
#	$actual = (isset($x)) ? $x[1] : $actual;
#	switch($pComp) {
#		case ">=":
#			$success = ($actual >= $pValue) ? 1 : 0;
#			break;
#		default:
#			$success = ($actual == $pValue) ? 1 : 0;
#	}
#	return $success;
#	// redundant $data = serialize(array("check" => $pValue, "actual" => $actual));
#}

?>
