<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/kernel_lib.php,v 1.4 2007/06/22 10:15:51 lsces Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * some PHP compatability issues need to be dealt with
 * array_fill
 */
if( !function_exists( 'array_fill' )) {
	require_once( KERNEL_PKG_PATH.'array_fill.func.php' );
}

// str_split
include_once( UTIL_PKG_PATH.'PHP_Compat/Compat/Function/str_split.php' );

/** \brief  substr with a utf8 string - works only with $start and $length positive or nuls
 * This function is the same as substr but works with multibyte
 * In a multybyte sequence, the first byte of a multibyte sequence that represents a non-ASCII character is always in the range 0xC0 to 0xFD
 * and it indicates how many bytes follow for this character.
 * All further bytes in a multibyte sequence are in the range 0x80 to 0xBF.
 */
/**
 * Check mb_substr availability
 */
if( function_exists( 'mb_substr' )) {
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

if( !function_exists( 'file_get_contents' )) {
	function file_get_contents( $pFile ) {
		ob_start();

		$retval = @readfile( $pFile );

		if( false !== $retval ) { // no readfile error
			$retval = ob_get_contents();
		}

		ob_end_clean();
		return $retval;
	}
}

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
/**
 * get_include_contents -- handy function for getting the contents of a
 * php include as a string
 *
 * @param $pFile the file to include
 * @return a string with the results of the file
 **/
function get_include_contents($pFile) {
  if (is_file($pFile)) {
    ob_start();
	global $gContent,$gBitSystem, $gBitSmarty, $gLibertySystem, $gBitUser;
    include $pFile;
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }
  return false;
}

/**
 * Translate a string
 * 
 * @param string $pString String that needs to be translated
 * @access public
 * @return void
 */
function tra( $pString ) {
	global $gBitLanguage;
	return( $gBitLanguage->translate( $pString ) );
}

/**
 * recursively remove files and directories
 * 
 * @param string $pPath directory we want to remove
 * @param boolean $pFollowLinks follow symlinks or not
 * @access public
 * @return TRUE on success, FALSE on failure
 */
function unlink_r( $pPath, $pFollowLinks = FALSE ) {
	if( is_dir( $pPath ) ) {
		$dir = opendir( $pPath ) ;
		while( FALSE !== ( $entry = readdir( $dir ) ) ) {
			if( is_file( "$pPath/$entry" ) || ( !$pFollowLinks && is_link( "$pPath/$entry" ) ) ) {
				unlink( "$pPath/$entry" );
			} elseif( is_dir( "$pPath/$entry" ) && $entry != '.' && $entry != '..' ) {
				unlink_r( "$pPath/$entry" ) ;
			}
		}
		closedir( $dir ) ;
		return rmdir( $pPath );
	}
}

/**
 * Fetch the contents of a file on a remote host
 * 
 * @param array $pUrl url to file to fetch
 * @access public
 * @return FALSE on failure, contents of file on success
 */
function bit_http_request( $pUrl ) {
	global $gBitSystem;
	$ret = FALSE;

	if( !empty( $pUrl )) {
		$pUrl = trim( $pUrl );

		// rewrite url if sloppy # added a case for https urls
		if( !preg_match( "!^https?://!", $pUrl )) {
			$pUrl = "http://".$pUrl;
		}

		if( !preg_match("/^[-_a-zA-Z0-9:\/\.\?&;=\+]*$/", $pUrl )) {
			return FALSE;
		}

		// try using curl first as it allows the use of a proxy
		if( function_exists( 'curl_init' )) {
			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, $pUrl );
			curl_setopt( $curl, CURLOPT_HEADER, 0 );
			curl_setopt( $curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curl, CURLOPT_TIMEOUT, 5 );

			// Proxy settings
			if( $gBitSystem->isFeatureActive( 'site_use_proxy' )) {
				curl_setopt( $curl, CURLOPT_PROXY, $gBitSystem->getConfig( 'site_proxy_host' ));
				curl_setopt( $curl, CURLOPT_PROXYPORT, $gBitSystem->getConfig( 'site_proxy_port' ));
				curl_setopt( $curl, CURLOPT_HTTPPROXYTUNNEL, 1);
			}

			$ret = curl_exec( $curl );
			curl_close( $curl );
		} else {
			// try using fsock now
			$parsed = parse_url( $pUrl );
			if( $fsock = @fsockopen( $parsed['host'], 80, $error['number'], $error['string'], 5 )) {
				@fwrite( $fsock, "GET ".$parsed['path'].( !empty( $parsed['query'] ) ? '?'.$parsed['query'] : '' )." HTTP/1.1\r\n" );
				@fwrite( $fsock, "HOST: {$parsed['host']}\r\n" );
				@fwrite( $fsock, "Connection: close\r\n\r\n" );

				$get_info = FALSE;
				while( !@feof( $fsock )) {
					if( $get_info ) {
						$ret .= @fread( $fsock, 1024 );
					} else {
						if( @fgets( $fsock, 1024 ) == "\r\n" ) {
							$get_info = TRUE;
						}
					}
				}
				@fclose( $fsock );
				if( !empty( $error['string'] )) {
					return FALSE;
				}
			}
		}
	}

	return $ret;
}

/**
 * Parse XML Attributes and return an array
 *
 * this function has a whopper of a RegEx.
 * I nabbed it from http://www.phpbuilder.com/annotate/message.php3?id=1000234 - XOXO spiderr
 * 
 * @param array $pString XML type string of parameters
 * @access public
 * @return TRUE on success, FALSE on failure
 */
function parse_xml_attributes( $pString ) {
	//$parameters = array( '', '' );
	$parameters = array();
	$regexp_str = "/([A-Za-z0-9_-]+)(?:\\s*=\\s*(?:(\"|\')((?:[\\\\].|[^\\\\]?)*?)(?:\\2)|([^=\\s]*)))?/";
	preg_match_all( $regexp_str, $pString, $matches, PREG_SET_ORDER );
	while( list( $key, $match ) = each( $matches ) ) {
		$attrib = $match[1];
		$value = $match[sizeof( $match )-1];      // The value can be at different indexes because of optional quotes, but we know it's always at the end.
		$value = preg_replace( "/\\\\(.)/","\\1",$value );
		$parameters[$attrib] = trim( $value, '\"' );
	}
	return $parameters;
}

/**
 * XML Entity Mandatory Escape Characters
 * 
 * @param array $string 
 * @param array $quote_style 
 * @access public
 * @return TRUE on success, FALSE on failure
 */
function xmlentities( $string, $quote_style=ENT_QUOTES ) {
	static $trans;
	if (!isset($trans)) {
		$trans = get_html_translation_table(HTML_ENTITIES, $quote_style);
		foreach ($trans as $key => $value)
			$trans[$key] = '&#'.ord($key).';';
		// dont translate the '&' in case it is part of &xxx;
		$trans[chr(38)] = '&';
	}
	// after the initial translation, _do_ map standalone '&' into '&#38;'
	return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/","&#38;" , strtr($string, $trans));
}

/**
 * Redirect to another page or site
 * @param string The url to redirect to
 */
function bit_redirect( $pUrl ) {
	// clean up URL before executing it
	while( strstr( $pUrl, '&&' ) ) {
		$pUrl = str_replace( '&&', '&', $pUrl );
	}

	while( strstr( $pUrl, '&amp;&amp;' ) ) {
		$pUrl = str_replace( '&amp;&amp;', '&amp;', $pUrl );
	}

	// header locates should not have the &amp; in the address it breaks things
	while( strstr( $pUrl, '&amp;' ) ) {
		$pUrl = str_replace( '&amp;', '&', $pUrl );
	}

	header('Location: ' . $pUrl);
	session_write_close();
	exit();
}

/**
 * array_diff_keys 
 * 
 * @access public
 */
function array_diff_keys() {
	$args = func_get_args();

	$res = $args[0];
	if(!is_array($res)) {
		return array();
	}

	for($i=1;$i<count($args);$i++) {
		if(!is_array($args[$i])) {
			continue;
		}
		foreach ($args[$i] as $key => $data) {
			unset($res[$key]);
		}
	}
	return $res;
}

/**
 * trim_array 
 * 
 * @param array $pArray 
 * @access public
 */
function trim_array( &$pArray ) {
	if( is_array( $pArray ) ) {
		foreach( array_keys( $pArray ) as $key ) {
			if( is_string( $pArray[$key] ) ) {
				$pArray[$key] = trim( $pArray[$key] );
			}
		}
	}
}


/**
 * ordinalize 
 * 
 * @param numeric $num Number to append th, st, nd, rd to - only makes sense when languages is english
 * @access public
 */
function ordinalize( $num ) {
	$ord = '';
	if( is_numeric( $num ) ) {
		if( $num >= 11 and $num <= 19 ) {
			$ord = tra( "th" );
		} elseif( $num % 10 == 1 ) {
			$ord = tra( "st" );
		} elseif( $num % 10 == 2 ) {
			$ord = tra( "nd" );
		} elseif( $num % 10 == 3 ) {
			$ord = tra( "rd" );
		} else {
			$ord = tra( "th" );
		}
	}

	return $num.$ord;
}

/**
 * Cleans file path according to system we're on
 * 
 * @param array $pPath 
 * @access public
 * @return TRUE on success, FALSE on failure
 */
function clean_file_path( $pPath ) {
	$pPath = (!empty($_SERVER["SERVER_SOFTWARE"]) && strpos($_SERVER["SERVER_SOFTWARE"],"IIS") ? str_replace( '\/', '\\', $pPath) : $pPath);
	return $pPath;
}

function chkgd2() {
	if (!isset($_SESSION['havegd2'])) {
#	TODO test this logic in PHP 4.3
#	if (version_compare(phpversion(), "4.3.0") >= 0) {
#		$_SESSION['havegd2'] = true;
#	} else {
		ob_start();

		phpinfo (INFO_MODULES);
		$_SESSION['havegd2'] = preg_match('/GD Version.*2.0/', ob_get_contents());
		ob_end_clean();
#	}
	}

	return $_SESSION['havegd2'];
}

function httpScheme() {
	return 'http'.( ( isset($_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' ) ) ? 's' : '' );
}

function httpPrefix() {
	return 'http'.((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 's' : '').'://'.$_SERVER['HTTP_HOST'];
}

/**
* If an unrecoverable error has occurred, this method should be invoked. script exist occurs
*
* @param string $ pMsg error message to be displayed
* @return none this function will DIE DIE DIE!!!
* @access public
*/
function install_error( $pMsg = null ) {
	global $gBitDbType;
	// here we decide where to go. if there are no db settings yet, we go the welcome page.
	if( isset( $gBitDbType )) {
		$step = 1;
	} else {
		$step = 0;
	}

	header( "Location: http://".$_SERVER['HTTP_HOST'].BIT_ROOT_URL."install/install.php?step=".$step );
	die;
}

/**
 * A set of compare functions that can be used in conjunction with usort() type functions
 *
 * @param array $ar1
 * @param array $ar2
 * @access public
 * @return TRUE on success, FALSE on failure
 */
function usort_by_title( $ar1, $ar2 ) {
	if( !empty( $ar1['title'] ) && !empty( $ar2['title'] ) ) {
		return strcasecmp( $ar1['title'], $ar2['title'] );
	} else {
		return 0;
	}
}

function compare_links( $ar1, $ar2 ) {
	return $ar1["links"] - $ar2["links"];
}

function compare_backlinks( $ar1, $ar2 ) {
	return $ar1["backlinks"] - $ar2["backlinks"];
}

function r_compare_links( $ar1, $ar2 ) {
	return $ar2["links"] - $ar1["links"];
}

function r_compare_backlinks( $ar1, $ar2 ) {
	return $ar2["backlinks"] - $ar1["backlinks"];
}

function compare_images( $ar1, $ar2 ) {
	return $ar1["images"] - $ar2["images"];
}

function r_compare_images( $ar1, $ar2 ) {
	return $ar2["images"] - $ar1["images"];
}

function compare_files( $ar1, $ar2 ) {
	return $ar1["files"] - $ar2["files"];
}

function r_compare_files( $ar1, $ar2 ) {
	return $ar2["files"] - $ar1["files"];
}

function compare_versions( $ar1, $ar2 ) {
	return $ar1["versions"] - $ar2["versions"];
}

function r_compare_versions( $ar1, $ar2 ) {
	return $ar2["versions"] - $ar1["versions"];
}

function compare_changed( $ar1, $ar2 ) {
	return $ar1["lastChanged"] - $ar2["lastChanged"];
}

function r_compare_changed( $ar1, $ar2 ) {
	return $ar2["lastChanged"] - $ar1["lastChanged"];
}

?>
