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

function bit_error_log( $pLogMessage ) {
	if( !empty( $_SERVER['SCRIPT_URI'] )) {
		error_log( "OUTPUT in {$_SERVER['SCRIPT_URI']}" );
	}

	$errlines = explode( "\n", (is_array( $pLogMessage ) || is_object( $pLogMessage ) ? vc( $pLogMessage ) : $pLogMessage) );
	foreach ($errlines as $txt) { error_log($txt); }
}

function bit_error_handler ( $errno, $errstr, $errfile, $errline, $errcontext=NULL ) {
    // error_reporting() === 0 if code was prepended with @
    if( ($errno == 1 || $reportingLevel = error_reporting()) && !strpos( $errfile, 'templates_c' ) ) {
		$errType = FALSE;
        switch ($errno) {
			case E_ERROR: $errType = 'FATAL ERROR'; break;
			case E_WARNING: if( $reportingLevel & E_WARNING ) { $errType = 'WARNING'; } break;
			case E_PARSE: if( $reportingLevel & E_PARSE ) { $errType = 'PARSE'; } break;
			case E_NOTICE: if( $reportingLevel & E_NOTICE ) { $errType = 'NOTICE'; } break;
			case E_CORE_ERROR: if( $reportingLevel & E_CORE_ERROR ) { $errType = 'CORE_ERROR'; } break;
			case E_CORE_WARNING: if( $reportingLevel & E_CORE_WARNING ) { $errType = 'CORE_WARNING'; } break;
			case E_COMPILE_ERROR: if( $reportingLevel & E_COMPILE_ERROR ) { $errType = 'COMPILE_ERROR'; } break;
			case E_COMPILE_WARNING: if( $reportingLevel & E_COMPILE_WARNING ) { $errType = 'COMPILE_WARNING'; } break;
			case E_USER_ERROR: if( $reportingLevel & E_USER_ERROR ) { $errType = 'USER_ERROR'; } break;
			case E_USER_WARNING: if( $reportingLevel & E_USER_WARNING ) { $errType = 'USER_WARNING'; } break;
			case E_USER_NOTICE: if( $reportingLevel & E_USER_NOTICE ) { $errType = 'USER_NOTICE'; } break;
			case E_STRICT: if( $reportingLevel & E_STRICT ) { $errType = 'STRICT'; } break;
			case E_RECOVERABLE_ERROR: if( $reportingLevel & E_RECOVERABLE_ERROR ) { $errType = 'RECOVERABLE_ERROR'; } break;
			case E_DEPRECATED: if( $reportingLevel & E_DEPRECATED ) { $errType = 'DEPRECATED'; } break;
			case E_USER_DEPRECATED: if( $reportingLevel & E_USER_DEPRECATED ) { $errType = 'USER_DEPRECATED'; } break;
            default: $errType = 'Error '.$errno; break;

        }
        // Send an e-mail to the administrator
		if( $errType && defined( 'ERROR_EMAIL' ) ) {
			global $gBitDb;
			$messageBody = $errType." [#$errno]: $errstr \n in $errfile on line $errline\n\n".bit_error_string( array( 'errno'=>$errno, 'db_msg'=>$errType ) ).vc( $_SERVER, FALSE );
			mail( ERROR_EMAIL, 'PHP '.$errType.' on '.php_uname( 'n' ).': '.$errstr, $messageBody );
		}
    }

    // Execute PHP's internal error handler
    return FALSE;
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

	// You can prevent sending of error emails by adding define('ERROR_EMAIL', ''); in your config/kernel/config_inc.php
	$errorEmail = defined( 'ERROR_EMAIL' ) ? ERROR_EMAIL : (!empty( $_SERVER['SERVER_ADMIN'] ) ? $_SERVER['SERVER_ADMIN'] : NULL);

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
	} elseif( $errorEmail ) {
		mail( $errorEmail,  "$pSubject", $pLogMessage );
		if( defined( 'AUTO_BUG_SUBMIT' ) && AUTO_BUG_SUBMIT && !empty( $gBitSystem ) && $gBitSystem->isDatabaseValid() ) {
			mail( 'bugs@bitweaver.org',"$pSubject",$pLogMessage );
		}
	}

	if( $pFatal ) {
		die();
	}
}

function bit_error_string( $iDBParms ) {
	global $gBitDb;
	global $gBitUser;
	global $_SERVER;
	global $argv;

	$separator = "\n";
	$indent = "  ";

	$date = date("D M d H:i:s Y"); // [Tue Sep 24 12:19:20 2002] [error]

	if( isset( $gBitUser->mInfo ) ) {
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
	} elseif( !empty( $argv ) ) {
		$uri = implode( ' ', $argv );
	}
	$info .= $indent."#### URL: ".$uri;
	if( isset($_SERVER['HTTP_REFERER'] ) ) {
		$info .= $indent."#### REFERRER: $_SERVER[HTTP_REFERER]".$separator;
	}
	$info .= $indent."#### HOST: $_SERVER[HTTP_HOST]".$separator;
	$info .= $indent."#### IP: $_SERVER[REMOTE_ADDR]".$separator;
	$info .= $indent."#### DB: ".$gBitDb->mDb->databaseType.'://'.$gBitDb->mDb->user.'@'.$gBitDb->mDb->host.'/'.$gBitDb->mDb->database.$separator;

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

	$stackTrace = bt( 9999, FALSE );

	//multiline expressions matched
	if( preg_match_all( "/.*adodb_error_handler\([^\}]*\)(.+\}.+)/ms", $stackTrace, $match )) {
		$stackTrace = $match[1][0];
	}

	$globalVars = array(
		'$_POST'   => $_POST,
		'$_GET'    => $_GET,
		'$_FILES'  => $_FILES,
		'$_COOKIE' => $_COOKIE,
	);

	$parameters = '';
	foreach( $globalVars as $global => $hash ) {
		if( !empty( $hash )) {
			$parameters .= $separator.$separator.$global.': '.$separator.var_export( $hash, TRUE );
		}
	}
	$parameters = preg_replace( "/\n/", $separator.$indent, $parameters );

	$ret = $info.$separator.$separator.$stackTrace.$parameters;

	return $ret;
}

if (!function_exists('bt')) {	// Make sure another backtrace function does not exist
function bt( $levels=9999, $iPrint=TRUE ) {
	$s = '';
	if (PHPVERSION() >= 4.3) {

		$MAXSTRLEN = 64;

		$traceArr = debug_backtrace();
		array_shift($traceArr);
		$tabs = sizeof($traceArr)-1;
		$indent = '';
		$sClass = '';

		foreach ($traceArr as $arr) {
			$levels -= 1;
			if ($levels < 0) break;

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
		if( $iPrint ) {
			print '<pre>'.$s."</pre>\n";
		}
	}
	return $s;
}
}	// End if function_exists('bt')

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
			ini_set( 'xdebug.overload_var_dump', FALSE );
		}
		var_dump( $iVar );
	} elseif( $pHtml && !empty( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] != 'cron' && ((is_object( $iVar ) && !empty( $iVar )) || is_array( $iVar )) ) {
		include_once( UTIL_PKG_PATH.'dBug/dBug.php' );
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
	return $ret;
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
