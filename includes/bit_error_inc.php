<?php
/**
 * Custom ADODB Error Handler. This will be called with the following params
 *
 * @package kernel
 * @subpackage functions
 * @version V3.94  13 Oct 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
 * Released under both BSD license and Lesser GPL library license.
 * Whenever there is any discrepancy between the two licenses,
 * the BSD license will take precedence.
 *
 * Set tabs to 4 for best viewing.
 *
 * Latest version is available at http://php.weblogs.com
 *
 */

/**
 * set error handling
 */
if( !defined( 'BIT_INSTALL' ) &&  !defined( 'ADODB_ERROR_HANDLER' )  ) {
	define( 'ADODB_ERROR_HANDLER', 'bitdb_error_handler' );
}

/**
 * Switch debug level in database
 *
 **/
function bit_db_debug( $pLevel = 99 ) {
	global $gDebug, $gBitDb;
	$gDebug = $pLevel;
	if( is_object( $gBitDb) ) {
		$gBitDb->debug( $pLevel );
	}
}

/**
 * Output in a pseudo standard output format
 *
 *    LogFormat "%V %h %l %{USERID}e %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-agent}i\" \"%{Cookie}n\""  combinedcookie
 *
 **/
function bit_print_log( $pLogParams, $pLogMessages ) {
	global $gBitUser;
	$virtualHost = BitBase::getParameter( $pLogParams, 'hostname', $_SERVER['HTTP_HOST'] );
	$remoteAddr = BitBase::getParameter( $pLogParams, 'remote_addr', $_SERVER['REMOTE_ADDR'] );
	$userAgent = BitBase::getParameter( $pLogParams, 'user_agent', $_SERVER['HTTP_USER_AGENT'] );
	$statusCode = BitBase::getParameter( $pLogParams, 'status_code', 200 );
	$scriptFilename = BitBase::getParameter( $pLogParams, 'script_uri', $_SERVER['SCRIPT_FILENAME'] );
	$ident = BitBase::getParameter( $pLogParams, 'ident', '-' );
	$userName = BitBase::getParameter( $pLogParams, 'username', $gBitUser->getField('username') );
	$executionTime = BitBase::getParameter( $pLogParams, 'exectime', '-' );
	$logTimestamp = BitBase::getParameter( $pLogParams, 'timestamp', date( '[d/M/Y:H:i:s O]' ) );

	for( $i = 1; $i < func_num_args(); $i++ ) { 
    	if( $pLogMessage = func_get_arg( $i ) ) {
			$errlines = explode( "\n", (is_array( $pLogMessage ) || is_object( $pLogMessage ) ? vc( $pLogMessage, FALSE ) : $pLogMessage) );
			foreach ($errlines as $txt) { 
				print "$virtualHost $remoteAddr $ident $userName $logTimestamp \"$scriptFilename\" $statusCode $executionTime \"$userAgent\" $pLogMessage\n";
			}
		} else {
			print "$virtualHost $remoteAddr $ident $userName $logTimestamp \"$scriptFilename\" $statusCode $executionTime \"$userAgent\"\n";
		}
	} 
}

function bit_error_log() {
	$chunks = array();
	for( $i = 0; $i < func_num_args(); $i++ ) { 
    	if( $pLogMessage = func_get_arg( $i ) ) {
			$errlines = explode( "\n", (is_array( $pLogMessage ) || is_object( $pLogMessage ) ? vc( $pLogMessage, FALSE ) : $pLogMessage) );
			foreach ($errlines as $txt) { 
				error_log($txt);
				$chunks[] = $txt;
			}
		}
	} 
	error_log( 'SCRIPT_URI: '.BitBase::getParameter( $_SERVER, 'SCRIPT_URI', 'OUTPUT' )."\n".bit_stack( 5 ) );

	// Optional reporters (Sentry/GlitchTip) do not see PHP error_log. Magick
	// CLI failures and similar operational errors only call bit_error_log().
	if( $chunks ) {
		$full = implode( "\n", $chunks );
		$summary = $chunks[0];
		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 8 );
		$callerFile = '';
		$callerLine = 0;
		foreach( $trace as $frame ) {
			$file = isset( $frame['file'] ) ? (string)$frame['file'] : '';
			if( $file === '' || basename( $file ) === 'bit_error_inc.php' ) {
				continue;
			}
			$callerFile = $file;
			$callerLine = isset( $frame['line'] ) ? (int)$frame['line'] : 0;
			break;
		}
		$hash = bit_error_build_report_hash(
			0,
			'ERROR_LOG',
			$summary,
			$callerFile,
			$callerLine,
			'error_log'
		);
		if( $full !== $summary ) {
			$hash['detail'] = $full;
		}
		bit_error_notify( $hash );
	}
}

function emergency_break(  ) {
	global $gBitSystem;
	if( is_object( $gBitSystem ) ) {
		$gBitSystem->setHttpStatus( HttpStatusCodes::HTTP_BAD_REQUEST );
	}

	vd( 'EMERGENCY BREAK' );
	foreach (func_get_args () as $arg){
		vd( $arg );
	}
	bt(); die;
}

if( !function_exists( 'eb' ) ) {
	function eb() {
		emergency_break( func_get_args() );
	}
}

function bit_error_email ( $pSubject, $pMessage, $pGlobalVars=array() ) {
	// You can prevent sending of error emails by adding define('ERROR_EMAIL', ''); in your config/kernel/config_inc.php
	$errorEmail = defined( 'ERROR_EMAIL' ) ? ERROR_EMAIL : (!empty( $_SERVER['SERVER_ADMIN'] ) ? $_SERVER['SERVER_ADMIN'] : NULL);

	$separator = "\n";
	$indent = "  ";
	$parameters = '';
	if( empty( $pGlobalVars ) ) {
		$pGlobalVars = array(
			'$_POST'   => $_POST,
			'$_GET'    => $_GET,
			'$_FILES'  => $_FILES,
			'$_COOKIE' => $_COOKIE,
		);
	}
	foreach( $pGlobalVars as $global => $hash ) {
		$parameters .= $separator.$global.': '.$separator.var_export( $hash, TRUE ).$separator;
	}
	$parameters = preg_replace( "/\n/", $separator.$indent, $parameters );

	mail( $errorEmail, $pSubject, $pMessage.$parameters.$separator.$separator.'$_SERVER: '.var_export( $_SERVER, TRUE ) );
}

function bit_error_handler ( $errno, $errstr, $errfile, $errline, $errcontext=NULL ) {
    // error_reporting() === 0 if code was prepended with @
	$reportingLevel = error_reporting();
    if( $reportingLevel !== 0 && !strpos( $errfile, 'templates_c' ) ) {
		$errType = 'Error';
		$isReported = TRUE;
        switch ($errno) {
			case E_ERROR: $errType = 'FATAL ERROR'; break;
			case E_WARNING: $isReported = $reportingLevel & E_WARNING; $errType = 'WARNING'; break;
			case E_PARSE: $isReported = $reportingLevel & E_PARSE; $errType = 'PARSE'; break;
			case E_NOTICE: $isReported = $reportingLevel & E_NOTICE; $errType = 'NOTICE'; break;
			case E_CORE_ERROR: $isReported = $reportingLevel & E_CORE_ERROR; $errType = 'CORE_ERROR'; break;
			case E_CORE_WARNING: $isReported = $reportingLevel & E_CORE_WARNING; $errType = 'CORE_WARNING'; break;
			case E_COMPILE_ERROR: $isReported = $reportingLevel & E_COMPILE_ERROR; $errType = 'COMPILE_ERROR'; break;
			case E_COMPILE_WARNING: $isReported = $reportingLevel & E_COMPILE_WARNING; $errType = 'COMPILE_WARNING'; break;
			case E_USER_ERROR: $isReported = $reportingLevel & E_USER_ERROR; $errType = 'USER_ERROR'; break;
			case E_USER_WARNING: $isReported = $reportingLevel & E_USER_WARNING; $errType = 'USER_WARNING'; break;
			case E_USER_NOTICE: $isReported = $reportingLevel & E_USER_NOTICE; $errType = 'USER_NOTICE'; break;
			case E_STRICT: $isReported = $reportingLevel & E_STRICT; $errType = 'STRICT'; break;
			case E_RECOVERABLE_ERROR: $isReported = $reportingLevel & E_RECOVERABLE_ERROR; $errType = 'RECOVERABLE_ERROR'; break;
			case E_DEPRECATED: $isReported = $reportingLevel & E_DEPRECATED; $errType = 'DEPRECATED'; break;
			case E_USER_DEPRECATED: $isReported = $reportingLevel & E_USER_DEPRECATED; $errType = 'USER_DEPRECATED'; break;
            default: $errType = 'Error '.$errno; break;

        }

		if( $isReported ) {
//eb( $isReported, $errType, $errno, $reportingLevel, $errfile );
			$errorSubject = 'PHP '.$errType.' on '.php_uname( 'n' ).': '.$errstr;
			$errorString = $errType." [#$errno]: $errstr \n in $errfile on line $errline\n\n".bit_error_string( array( 'errno'=>$errno, 'db_msg'=>$errType ) );
			if( defined( 'IS_LIVE' ) && IS_LIVE ) {
				if( defined( 'ERROR_EMAIL' ) ) {
        			// Send an e-mail to the administrator
					bit_error_email( $errorSubject, $errorString );
				} else {
					error_log( $errorString );
				}
				// Admin consoles set display_errors=1; show a compact orange box
				// without falling through to Xdebug 3 (which dumps huge arg trees).
				if( bit_error_display_errors_enabled() ) {
					bit_error_display_compact( $errType, $errno, $errstr, $errfile, $errline );
				}
			} else {
				if( $errType == E_ERROR ) {
					eb( $errorSubject, $errorString );
				} else {
					bt( $errorSubject );
				}
			}
			// Vendor-agnostic reporters (optional packages register callbacks).
			bit_error_notify( bit_error_build_report_hash( $errno, $errType, $errstr, $errfile, $errline, 'php_error' ) );
		}
    }

	// On live: never fall through to PHP/Xdebug. Xdebug 3 develop dumps full
	// call arguments into the error log (sessions, orders, card data). Compact
	// on-page display is handled above when display_errors is on.
	// On non-live: fall through so Xdebug develop can show its HTML error box.
	if( defined( 'IS_LIVE' ) && IS_LIVE ) {
		return TRUE;
	}
	return FALSE;
}

/**
 * Whether PHP will display errors to the client (admin consoles often force this on).
 */
function bit_error_display_errors_enabled() {
	$v = ini_get( 'display_errors' );
	if( $v === false || $v === '' || $v === '0' ) {
		return FALSE;
	}
	if( is_numeric( $v ) ) {
		return ( (int) $v ) !== 0;
	}
	$v = strtolower( trim( (string) $v ) );
	return ( $v === 'on' || $v === 'true' || $v === 'yes' || $v === 'stdout' || $v === 'stderr' );
}

/**
 * Small classic-style orange error box (Xdebug 2 / RHEL7 era feel).
 * Type, message, file:line, and a short compact stack — no arg dumps.
 */
function bit_error_display_compact( $pErrType, $pErrno, $pErrstr, $pErrfile, $pErrline ) {
	static $stylePrinted = FALSE;

	if( PHP_SAPI === 'cli' ) {
		fwrite( STDERR, $pErrType.' [#'.$pErrno.']: '.$pErrstr.' in '.$pErrfile.' on line '.$pErrline."\n" );
		return;
	}

	if( !$stylePrinted ) {
		$stylePrinted = TRUE;
		print '<style type="text/css">'
			.'.bit-php-error{margin:8px 0;padding:8px 10px;border:1px solid #f0c040;border-left:6px solid #e68a00;'
			.'background:#fff8e1;color:#333;font:13px/1.35 Menlo,Consolas,monospace;text-align:left;clear:both}'
			.'.bit-php-error b{color:#a65c00}'
			.'.bit-php-error .bit-php-file{color:#555;margin-top:4px}'
			.'.bit-php-error pre{margin:6px 0 0;padding:0;white-space:pre-wrap;color:#444;font-size:12px}'
			.'</style>';
	}

	$file = htmlspecialchars( (string) $pErrfile, ENT_QUOTES, 'UTF-8' );
	$msg = htmlspecialchars( (string) $pErrstr, ENT_QUOTES, 'UTF-8' );
	$type = htmlspecialchars( (string) $pErrType, ENT_QUOTES, 'UTF-8' );
	$stack = htmlspecialchars( trim( bit_stack( 6 ) ), ENT_QUOTES, 'UTF-8' );

	print '<div class="bit-php-error" role="alert">'
		.'<b>'.$type.' [#'.(int) $pErrno.']</b>: '.$msg
		.'<div class="bit-php-file">in '.$file.' on line '.(int) $pErrline.'</div>';
	if( $stack !== '' ) {
		print '<pre>'.$stack.'</pre>';
	}
	print '</div>';
}

/**
 * Register a callback for external error reporting (Sentry, GlitchTip, custom, …).
 * Callbacks receive a scrubbed vendor-agnostic hash from bit_error_build_report_hash().
 * Optional packages should call this from includes/bit_setup_inc.php.
 *
 * @param callable $pCallback
 */
function bit_error_register_reporter( $pCallback ) {
	global $gBitErrorReporters;
	if( !is_array( $gBitErrorReporters ) ) {
		$gBitErrorReporters = array();
	}
	if( is_callable( $pCallback ) ) {
		$gBitErrorReporters[] = $pCallback;
	}
}

/**
 * Build a product-agnostic error report hash (no raw POST/SESSION/payment dumps).
 * Request/account fields mirror the #### headers from bit_error_string() used by
 * bit_error_email(), so external reporters can attribute REMOTE_ADDR and login.
 */
function bit_error_build_report_hash( $pErrno, $pErrType, $pErrstr, $pErrfile, $pErrline, $pChannel = 'php_error' ) {
	global $gBitUser, $gBitDb, $argv;

	$url = '';
	if( !empty( $_SERVER['SCRIPT_URI'] ) ) {
		$url = $_SERVER['SCRIPT_URI'].( !empty( $_SERVER['QUERY_STRING'] ) ? '?'.$_SERVER['QUERY_STRING'] : '' );
	} elseif( !empty( $_SERVER['REQUEST_URI'] ) ) {
		$url = ( !empty( $_SERVER['HTTP_HOST'] ) ? 'https://'.$_SERVER['HTTP_HOST'] : '' ).$_SERVER['REQUEST_URI'];
	} elseif( !empty( $argv ) && is_array( $argv ) ) {
		$url = implode( ' ', $argv );
	}

	$user = array(
		'id'    => NULL,
		'login' => NULL,
		'email' => NULL,
	);
	if( is_object( $gBitUser ) && !empty( $gBitUser->mInfo ) && is_array( $gBitUser->mInfo ) ) {
		$user['id'] = isset( $gBitUser->mInfo['user_id'] ) ? $gBitUser->mInfo['user_id'] : NULL;
		$user['login'] = isset( $gBitUser->mInfo['login'] ) ? $gBitUser->mInfo['login'] : NULL;
		$user['email'] = isset( $gBitUser->mInfo['email'] ) ? $gBitUser->mInfo['email'] : NULL;
	}

	$db = NULL;
	if( is_object( $gBitDb ) && !empty( $gBitDb->mDb ) ) {
		// Same shape as bit_error_string DB line, without a password.
		$db = $gBitDb->mDb->databaseType.'://'.$gBitDb->mDb->user.'@'.$gBitDb->mDb->host.'/'.$gBitDb->mDb->database;
	}

	return array(
		'errno'      => (int) $pErrno,
		'errtype'    => (string) $pErrType,
		'message'    => (string) $pErrstr,
		'file'       => (string) $pErrfile,
		'line'       => (int) $pErrline,
		'stack'      => trim( bit_stack( 8 ) ),
		'sapi'       => PHP_SAPI,
		'host'       => BitBase::getParameter( $_SERVER, 'HTTP_HOST', php_uname( 'n' ) ),
		'script'     => BitBase::getParameter( $_SERVER, 'SCRIPT_NAME', '' ),
		'uri'        => BitBase::getParameter( $_SERVER, 'REQUEST_URI', '' ),
		'url'            => $url,
		'referrer'       => BitBase::getParameter( $_SERVER, 'HTTP_REFERER', '' ),
		'user_agent'     => BitBase::getParameter( $_SERVER, 'HTTP_USER_AGENT', '' ),
		'ip'             => BitBase::getParameter( $_SERVER, 'REMOTE_ADDR', '' ),
		'request_method' => BitBase::getParameter( $_SERVER, 'REQUEST_METHOD', '' ),
		'user'           => $user,
		'db'             => $db,
		'is_live'    => ( defined( 'IS_LIVE' ) && IS_LIVE ),
		'channel'    => (string) $pChannel,
	);
}

/**
 * Notify all registered error reporters. Never throws; guards against recursion.
 *
 * @param array $pHash from bit_error_build_report_hash()
 */
function bit_error_notify( $pHash ) {
	global $gBitErrorReporters;
	static $inNotify = FALSE;

	if( $inNotify || empty( $gBitErrorReporters ) || !is_array( $gBitErrorReporters ) ) {
		return;
	}

	$inNotify = TRUE;
	foreach( $gBitErrorReporters as $callback ) {
		try {
			if( is_callable( $callback ) ) {
				call_user_func( $callback, $pHash );
			}
		} catch( Exception $e ) {
			error_log( 'bit_error_notify reporter failed: '.$e->getMessage() );
		} catch( Throwable $e ) {
			error_log( 'bit_error_notify reporter failed: '.$e->getMessage() );
		}
	}
	$inNotify = FALSE;
}


function bit_shutdown_handler() {
	$isError = false;
	$error = error_get_last();

	if( $error && $error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_USER_ERROR) ){
		header( "HTTP/1.0 500 Internal Server Error" );
		print "Internal Server Error";
		bit_error_handler( $error['type'], $error['message'], $error['file'], $error['line'] );
	}
}

register_shutdown_function('bit_shutdown_handler');


function bit_display_error( $pLogMessage, $pSubject, $pFatal = TRUE ) {
	global $gBitSystem;

	if( $pFatal ) {
		header( $_SERVER["SERVER_PROTOCOL"].' '.HttpStatusCodes::getMessageForCode( HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR ) );
	}

	error_log( $pLogMessage );

	if( ( !defined( 'IS_LIVE' ) || !IS_LIVE ) ) {
		print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
			<head>
				<title>bitweaver - White Screen of Death</title>
			</head>
			<body style="background:#fff; font-family:monospace;">';
// 		print "<h1>Upgrade Beta 1 to Beta 2</h1>If you are getting this error because you just upgraded your bitweaver from Beta 1 to Beta 2, please follow this link to the installer, which will guide you through the upgrade process: <a href='".BIT_ROOT_URL."install/install.php?step=4'>Upgrade Beta 1 to Beta 2</a>";
		print "<h1 style=\"color:#900; font-weight:bold;\">You are running bitweaver in TEST mode</h1>\n";
		print "
			<ul>
				<li><a href='http://sourceforge.net/tracker/?func=add&amp;group_id=141358&amp;atid=749176'>Click here to log a bug</a>, if this appears to be an error with the application.</li>
				<li><a href='".BIT_ROOT_URL."install/install.php'>Go here to begin the installation process</a>, if you haven't done so already.</li>
				<li>To hide this message, please <strong>set the IS_LIVE constant to TRUE</strong> in your config/kernel/config_inc.php file.</li>
			</ul>
			<hr />
		";
		print "<pre>".$pLogMessage."</pre>";
		print "<hr />";
		print "</body></html>";
	} else {
		bit_error_email ( $pSubject, $pLogMessage );
		if( defined( 'AUTO_BUG_SUBMIT' ) && AUTO_BUG_SUBMIT && !empty( $gBitSystem ) && $gBitSystem->isDatabaseValid() ) {
			mail( 'bugs@bitweaver.org',"$pSubject",$pLogMessage );
		}
	}

	if( $pFatal ) {
		die();
	}
}

function bit_error_string( $iDBParms = array() ) {
	global $gBitDb;
	global $gBitUser;
	global $argv;

	$separator = "\n";
	$indent = "  ";

	$date = date("D M d H:i:s Y"); // [Tue Sep 24 12:19:20 2002] [error]

	if( is_a( $gBitUser, 'BitUser' ) ) {
		$acctStr = "ID: ".$gBitUser->mInfo['user_id']." - Login: ".$gBitUser->mInfo['login']." - e-mail: ".$gBitUser->mInfo['email'];
	} else {
		$acctStr = "User unknown";
	}

	$info  = $indent."[ - ".BIT_MAJOR_VERSION.".".BIT_MINOR_VERSION.".".BIT_SUB_VERSION." ".BIT_LEVEL." - ] [ $date ]".$separator;
	$info .= $indent."-----------------------------------------------------------------------------------------------".$separator;
	$info .= $indent."#### USER AGENT: ".$_SERVER['HTTP_USER_AGENT'].$separator;
	$info .= $indent."#### ACCT: ".$acctStr.$separator;
	$uri = '';
	if( !empty( $_SERVER['SCRIPT_URI'] ) ) {
		$uri = $_SERVER['SCRIPT_URI'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:'').$separator;
	} elseif( !empty( $_SERVER['REQUEST_URI'] ) ) {
		$uri =  $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	} elseif( !empty( $argv ) ) {
		$uri = implode( ' ', $argv );
	}

	$info .= $indent."#### URL: ".$uri.$separator;
	if( isset($_SERVER['HTTP_REFERER'] ) ) {
		$info .= $indent."#### REFERRER: $_SERVER[HTTP_REFERER]".$separator;
	}
	$info .= $indent."#### HOST: $_SERVER[HTTP_HOST]".$separator;
	$info .= $indent."#### IP: $_SERVER[REMOTE_ADDR]".$separator;
	if( !empty( $gBitDb ) ) {
		$info .= $indent."#### DB: ".$gBitDb->mDb->databaseType.'://'.$gBitDb->mDb->user.'@'.$gBitDb->mDb->host.'/'.$gBitDb->mDb->database.$separator;
	}

	if( $gBitDb && isset( $php_errormsg ) ) {
		$info .= $indent."#### PHP: ".$php_errormsg.$separator;
	}

	if ( !empty( $iDBParms['sql'] ) ) {
		$badSpace = array("\n", "\t");
		$info .= $indent."#### SQL: ".str_replace($badSpace, ' ', $iDBParms['sql']).$separator;
		if( is_array( $iDBParms['p2'] ) ) {
			$info .= $indent.'['.implode( ', ', $iDBParms['p2'] ).']'.$separator;
		}
	}

	$errno = (!empty( $iDBParms['errno'] ) ? 'Errno: '.$iDBParms['errno'] : '');
	if( !empty( $iDBParms['db_msg'] ) ) {
		$info .= $indent."#### ERROR CODE: ".$errno."  Message: ".$iDBParms['db_msg'];
	}

	$stackTrace = bit_stack();

	//multiline expressions matched
	if( preg_match_all( "/.*adodb_error_handler\([^\}]*\)(.+\}.+)/ms", $stackTrace, $match )) {
		$stackTrace = $match[1][0];
	}

	$ret = $info.$separator.$separator.$stackTrace.$separator.$separator;

	return $ret;
}

if (!function_exists('bt')) {	// Make sure another backtrace function does not exist
function bt() {
	vvd( func_get_args() );
	print '<pre>'."\t".date( "Y-m-d H:i:s" )."\n".bit_stack()."</pre>\n";
}
}	// End if function_exists('bt')

function bit_stack( $pDepth = 999 ) {
	$s = '';

	if (PHPVERSION() >= 4.3) {

		$MAXSTRLEN = 128;

		$traceArr = debug_backtrace();
		array_shift($traceArr);
		$tabs = sizeof($traceArr)-1;
		$indent = '';
		$sClass = '';

		$levels = $pDepth;
		foreach ($traceArr as $arr) {
			$levels -= 1;
			if( $levels < 0 ) {
				break;
			}

			$args = array();
			for ($i=0; $i <= $tabs; $i++) {
				$indent .= '}';
			}
			$tabs -= 1;
			if ( isset($arr['class']) ) {
				$sClass .= $arr['class'].'::';
			}
			if ( isset($arr['args']) ) {
				foreach( $arr['args'] as $v ) {
					if (is_null($v) ) {
						$args[] = 'null';
					} elseif (is_array($v)) { $args[] = 'Array['.sizeof($v).']';
					} elseif (is_object($v)) { $args[] = 'Object:'.get_class($v);
					} elseif (is_bool($v)) { $args[] = $v ? 'true' : 'false';
					} else {
						$v = (string) @$v;
						$str = htmlspecialchars(substr($v,0,$MAXSTRLEN));
						if (strlen($v) > $MAXSTRLEN) $str .= '...';
						$args[] = $str;
					}
				}
			}
			if( !preg_match( "*include*", $arr['function'] ) && !preg_match( "*silentlog*", strtolower( $arr['function'] ) ) ) {
				$s .= "\n    ".$indent.'    -> ';
				$s .= $sClass.$arr['function'].'('.implode(', ',$args).')';
			}
			$s .= "\n    ".$indent;
			$s .= @sprintf(" LINE: %4d, %s", $arr['line'],$arr['file']);
			$indent = '';
		}
		$s .= "\n";
	}
	return $s;
}

// variable argument var dump
function vvd() {
	for( $i = 0; $i < func_num_args(); $i++ ) { 
    	vd( func_get_arg( $i ) );
	} 
}

// var dump variable in something nicely readable in web browser
function vd( $pVar, $pGlobals=FALSE, $pDelay=FALSE ) {
	global $gBitSystem;

	ob_start();
	if( $pGlobals ) {
		print '<h2>$pVar</h2>';
	}
	print vc( $pVar );
	if( $pGlobals ) {
		if( !empty( $_GET )) {
			print '<h2>$_GET</h2>';
			print vc( $_GET );
		}
		if( !empty( $_POST )) {
			print '<h2>$_POST</h2>';
			print vc( $_POST );
		}
		if( !empty( $_FILES )) {
			print '<h2>$_FILES</h2>';
			print vc( $_FILES );
		}
		if( !empty( $_COOKIE )) {
			print '<h2>$_COOKIE</h2>';
			print vc( $_COOKIE );
		}
	}
	if($pDelay) {
		$gBitSystem->mDebugHtml .= ob_get_contents();
		ob_end_clean();
	} else {
		ob_end_flush();
	}
	flush();
}

// variable argument var dump
function vvc() {
	$ret = '';
	for( $i = 0; $i < func_num_args(); $i++ ) { 
    	$ret .= vc( func_get_arg( $i ), FALSE );
	} 
	return $ret;
}

// var capture variable in something nicely readable in web browser
function vc( $iVar, $pHtml=TRUE ) {
	ob_start();
	if( is_object( $iVar ) ) {
		if( isset( $iVar->mDb ) ) {
			unset( $iVar->mDb );
		}
	}

	// xdebug rocks!
	if( extension_loaded( 'xdebug' ) ) {
		if( empty( $pHtml ) ) {
			print_r( $iVar );
		} else {
			var_dump( $iVar );
		}
	} elseif( $pHtml && !empty( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] != 'cron' && ((is_object( $iVar ) && !empty( $iVar )) || is_array( $iVar )) ) {
		include_once( UTIL_PKG_INCLUDE_PATH.'dBug/dBug.php' );
		new dBug( $iVar, "", FALSE );
	} else {
		print '<pre>';
		if( is_object( $iVar ) ) {
			var_dump( $iVar );
		} elseif( is_string( $iVar ) && !empty( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] != 'cron' ) {
			var_dump( htmlentities( $iVar ) );
		} else {
			var_dump( $iVar );
		}
		print "</pre>\n";
	}
	$ret = ob_get_contents();
	ob_end_clean();
	return $ret."\n";
}


function va( $iVar ) {
	$dbg = var_export($iVar, 1);
	$dbg = highlight_string("<?\n". $dbg."\n?>", 1);
	echo "<div><span style='background-color:black;color:white;padding:.5ex;font-weight:bold;'>Var Anatomy</div>";
	echo "<div style='border:1px solid black;padding:2ex;background-color:#efe6d6;'>$dbg</div>";
}

/**
 * bitdebug display an debug output when $gDebug is set to TRUE
 *
 * @param array $pMessage Message to display
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function bitdebug( $pMessage ) {
	global $gDebug;
	if( !empty( $gDebug )) {
		echo "<pre>$pMessage</pre>";
	}
}
