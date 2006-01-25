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
	define( 'ADODB_ERROR_HANDLER', 'bit_error_handler' );
}

function bit_log_error( $pLogMessage, $pSubject ) {
	// You can prevent sending of error emails by adding define('ERROR_EMAIL', ''); in your config_inc.php
	$errorEmail = defined('ERROR_EMAIL') ? ERROR_EMAIL : !empty( $_SERVER['SERVER_ADMIN'] ) ? $_SERVER['SERVER_ADMIN'] : NULL;

	if( ( !defined('IS_LIVE') || !IS_LIVE) ) {
		print  '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><title>bitweaver - White Screen of Death</title></head><body>';
		print "\n<p><a href='http://sourceforge.net/tracker/?func=add&amp;group_id=141358&amp;atid=749176'>Click here to log a bug</a>, if this appears to be an error with the application.</p>\n";
		print "<p><a href='".BIT_ROOT_URL."install/install.php'>Go here to begin the installation process</a>, if you haven't done so already.</p>\n";
// 		print "<h1>Upgrade Beta 1 to Beta 2</h1>If you are getting this error because you just upgraded your bitweaver from Beta 1 to Beta 2, please follow this link to the installer, which will guide you through the upgrade process: <a href='".BIT_ROOT_URL."install/install.php?step=4'>Upgrade Beta 1 to Beta 2</a>";
		print "<pre>".$logString."</pre>";
		print "</body></html>";
	} elseif( $errorEmail ) {
		mail( $errorEmail,  "$subject $pSubject", $pLogMessage );
		if( ( defined('AUTO_BUG_SUBMIT') && AUTO_BUG_SUBMIT) ) {
			mail( 'bugs@bitweaver.org',"$subject $pSubject",$pLogMessage );
		}
	}

	if( $fatal ) {
		die();
	} else {
				return $logString;
	}
}

function bit_error_string( $iDBParms ) {
	global $gBitDb;
	global $gBitUser;
	global $_SERVER;

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
    $info .= $indent."#### URL: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$separator;
	if( isset($_SERVER['HTTP_REFERER'] ) ) {
    	$info .= $indent."#### REFERRER: $_SERVER[HTTP_REFERER]".$separator;
	}
    $info .= $indent."#### HOST: $_SERVER[HTTP_HOST]".$separator;
	$info .= $indent."#### IP: $_SERVER[REMOTE_ADDR]".$separator;

    if( $gBitDb && isset( $php_errormsg ) ) {
		$info .= $indent."#### PHP: ".$php_errormsg.$separator;
    }

    if ( $iDBParms['sql'] ) {
		$badSpace = array("\n", "\t");
		$info .= $indent."#### SQL: ".str_replace($badSpace, ' ', $iDBParms['sql']).$separator;
    }

	$errno = ((int)$iDBParms['errno'] ? 'Errno: '.$iDBParms['errno'] : '');

	$info .= $indent."#### ERROR CODE: ".$errno."  Message: ".$iDBParms['db_msg'];

	$stackTrace = bt( 9999, FALSE );

	//multiline expressions matched
	if ( preg_match_all("/.*adodb_error_handler\([^\}]*\)(.+\}.+)/ms", $stackTrace, $match) ) {
		$stackTrace = $match[1][0];
	}

	$ret = $info.$separator.$separator.$stackTrace;

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
function vd( $iVar ) {
	print '<pre>';
	if( is_object( $iVar ) ) {
		if( isset( $iVar->mDb ) ) {
			unset( 	$iVar->mDb );
		}
		var_dump( $iVar );
	} elseif( is_string( $iVar ) && !empty( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] != 'cron' ) {
		var_dump( htmlentities( $iVar ) );
	} else {
		var_dump( $iVar );
	}
	print "</pre>\n";
}


function va( $iVar ) {
	$dbg = var_export($iVar, 1);
	$dbg = highlight_string("<?\n". $dbg."\n?>", 1);
	echo "<div><span style='background-color:black;color:white;padding:.5ex;font-weight:bold;'>Var Anatomy</div>";
	echo "<div style='border:1px solid black;padding:2ex;background-color:#efe6d6;'>$dbg</div>";
}


?>
