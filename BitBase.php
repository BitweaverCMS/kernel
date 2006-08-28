<?php
/**
 * Virtual bitweaver base class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitBase.php,v 1.24 2006/08/28 07:54:33 jht001 Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 *
 * created 2004/8/15
 *
 * @author spider <spider@steelsun.com>
 */

/**
 * required setup
 */
require_once ( KERNEL_PKG_PATH.'BitDbBase.php' );
include_once ( KERNEL_PKG_PATH.'BitCache.php' );

define( 'STORAGE_BINARY', 1 );
define( 'STORAGE_IMAGE', 2 );

/**
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived bitweaver classes that require database access.
 *
 * @package kernel
 */
class BitBase
{
    /**
    * Error hash that will contain an error codes we encounter along
    * the way this hash can be used by presentation layer ti give feedback
    * to the user.
    * @todo not used yet
    * @private
    */
    var $mErrors;

	/**
	* Same idea as the error hash but this is for successful operations
	* @private
	*/
	var $mSuccess;

    /**
    * String used to refer to preference caching and database table
    * @private
    */
    var $mName;
    /**
    * Used to store database mechanism
    * @private
    */
    var $mDb;
    /**
    * Used to store database type
    * @private
    */
    var $dType;
    /**
    * Standard Query Cache Time. Variable can be set to 0 to flush particular queries
    * @private
    */
    var $mCacheTime;
    /**
    * Data hash that represents this classes row(s) in the db
    **/
    var $mInfo;

    /**
    * During initialisation, we assign a name which is used by the class.
    * @param pName a unique identified used in caching and database
    * mechanisms
    **/
    function BitBase($pName = '')
    {
		global $gBitDb;
		$this->mName = $pName;
		$this->mCacheTime = BIT_QUERY_CACHE_TIME;
		if(is_object($gBitDb)) {
			$this->setDatabase($gBitDb);
		}
 		$this->mErrors = array();
 		$this->mInfo = array();
    }

    /**
    * Sets database mechanism for the instance
    * @param pDB the instance of the database mechanism
    **/
    function setDatabase(&$pDB)
    {
        // set internal db and retrieve values
        $this->mDb = &$pDB;
		$this->dType = $this->mDb->mType;
    }

    /**
     * Determines if there is a valide database connection
     **/
	function isDatabaseValid() {
		return( !empty( $this->mDb ) && $this->mDb->isValid() );
	}

    /**
     * Return pointer to current Database
     **/
	function getDb() {
		return ( !empty( $this->mDb ) ? $this->mDb : NULL  );
	}

    /**
     * Switch debug level in database
	 *
    **/
	function debug( $pLevel = 99 ) {
		if( is_object( $this->mDb ) ) {
			$this->mDb->debug( $pLevel );
		}
	}

	// =-=-=-=-=-=-=-=-=-=-=- Non-DB related functions =-=-=-=-=-=-=-=-=-=-=-=-=

    /**
    * Determines if any given variable exists and is a number
    **/
	function verifyId( $pId ) {
		if ( empty( $pId ) ) {
			return false;
		}
		if ( is_array( $pId ) ) {
			foreach ($pId as $id) {
				if ( !is_numeric( $id ) )
					return false;
			}
			return true;
		}
		return( is_numeric( $pId ) );
	}

    // {{{ display
    /**
	* This method should be THE method used to display a template. php files should not
	* access $gBitSmarty directly.
	*
	* @param string pMsg error message to be displayed
	* @return none this function will DIE DIE DIE!!!
	* @access public
	**/
	function display( $pPackage, $pTemplate ) {
		global $gBitSmarty, $gBitLanguage, $style, $style_base;
		if (isset($style) && isset($style_base)) {
			if (file_exists(BIT_THEME_PATH."styles/$style_base/$pTemplate")) {
				// Theme has overriden template
				$_smarty_tpl_file = 'file:'.BIT_STYLES_PATH."/$style_base/$pTemplate";
			} else {
				// Use default
				$_smarty_tpl_file = 'file:'.BIT_ROOT_PATH."$pPackage/templates/$pTemplate";
			}
		}
/*
		global $gBitLanguage, $style, $style_base;
		if (isset($style) && isset($style_base)) {
			if (file_exists(BIT_STYLES_PATH."/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = BIT_STYLES_PATH."/$style_base/$_smarty_tpl_file";
			}
		}
*/
		$gBitSmarty->display( $_smarty_tpl_file );
//		$gBitSmarty->display( 'bitpackage:'.$pPackage.$pTemplate );
	}
    // }}}

    /**
    * Returns entry from the mInfo hash if field exists
    * @param pFieldName the instance of the database mechanism
    **/
    function getField( $pFieldName, $pDefault = NULL ) {
    	return( !empty( $this->mInfo[$pFieldName] ) ? $this->mInfo[$pFieldName] : $pDefault );
    }



    /**
    * Prepares parameters with default values for any getList function
    * @param pParamHash hash of parameters for any getList() function
    * @return the link to display the page.
    */
	function prepGetList( &$pListHash ) {
		global $gBitSmarty, $gBitSystem;

		// If offset is set use it if not then use offset =0
		// use the max_records php variable to set the limit
		// if sortMode is not set then use last_modified_desc
		if ( empty( $pListHash['sort_mode'] ) ) {
			if ( empty( $_REQUEST["sort_mode"] ) ) {
				$pListHash['sort_mode'] = 'last_modified_desc';
			} else {
				$pListHash['sort_mode'] = $_REQUEST['sort_mode'];
			}
		}

		if( empty( $pListHash['max_records'] ) ) {
			global $gBitSystem;
			$pListHash['max_records'] = $gBitSystem->getConfig( "max_records", 10 );
		}

		if( !isset( $pListHash['offset'] ) ) {
			if (isset($pListHash['page'])) {
				$pListHash['offset'] = ($pListHash['page'] - 1) * $pListHash['max_records'];
			} else {
				if ( isset( $_REQUEST["offset"] ) ) {
					$pListHash['offset'] = $_REQUEST['offset'];
				} elseif( isset( $_REQUEST['page'] ) && is_numeric( $_REQUEST['page'] ) ) {
					$pListHash['offset'] = ($_REQUEST['page'] - 1) * $pListHash['max_records'];
				} elseif( isset($_REQUEST['list_page']) && is_numeric( $_REQUEST['list_page'] ) ) {
					$pListHash['offset'] = ($_REQUEST['list_page'] - 1) * $pListHash['max_records'];
				} else {
					$pListHash['offset'] = 0;
				}
			}
		}

		if( !empty( $pListHash["find"] ) ) {
			$pListHash['find']= $pListHash["find"];
		} elseif( isset( $_REQUEST["find"] ) ) {
			$pListHash['find']= $_REQUEST["find"];
		} else {
			$pListHash['find'] = NULL;
		}
		$gBitSmarty->assign( 'find', $pListHash['find'] );

		if( isset( $_REQUEST['date'] ) ) {
			$pListHash['date']= $_REQUEST['date'];
		} else {
			$pListHash['date'] = $gBitSystem->getUTCTime();
		}

		if( empty( $pListHash['load_comments'] ) ) {
			$pListHash['load_comments'] = FALSE;
		}
		if( empty( $pListHash['load_num_comments'] ) ) {
			$pListHash['load_num_comments'] = FALSE;
		}
		if( empty( $pListHash['parse_data'] ) ) {
			$pListHash['parse_data'] = FALSE;
		}

	}

}

// translate a string
function tra($content) {
	global $gBitLanguage;
	return( $gBitLanguage->translate( $content ) );
}

// recursively remove files and directories
function unlink_r( $path,$followLinks = FALSE ) {
	if( is_dir( $path ) ) {
		$dir = opendir( $path ) ;
		while( FALSE !== ( $entry = readdir( $dir ) ) ) {
			if( is_file( "$path/$entry" ) || ( !$followLinks && is_link( "$path/$entry" ) ) ) {
				unlink( "$path/$entry" );
			} elseif( is_dir( "$path/$entry" ) && $entry != '.' && $entry != '..' ) {
				unlink_r( "$path/$entry" ) ;
			}
		}
		closedir( $dir ) ;
		return rmdir( $path );
	}
}

function tp_http_request($url) {

	global $site_use_proxy,$site_proxy_host,$site_proxy_port;

	// test url :
	$url = trim( $url );
	if (!preg_match("/^[-_a-zA-Z0-9:\/\.\?&;=\+]*$/",$url)) {
		return false;
	}
	// rewrite url if sloppy # added a case for https urls
	if ( (substr($url,0,7) <> "http://") && (substr($url,0,8) <> "https://") ) {
		$url = "http://" . $url;
	}
	if (substr_count($url, "/") < 3) {
		$url .= "/";
	}

	$curl_obj = curl_init();
	curl_setopt($curl_obj, CURLOPT_URL, $url);
	curl_setopt($curl_obj, CURLOPT_HEADER, 0);
    curl_setopt($curl_obj, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl_obj, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl_obj, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_obj, CURLOPT_TIMEOUT, 5);

 	// Proxy settings
	if ($site_use_proxy == 'y') {
        curl_setopt($curl_obj, CURLOPT_PROXY, $site_proxy_host);
        curl_setopt($curl_obj, CURLOPT_PROXYPORT, $site_proxy_port);
        curl_setopt($curl_obj, CURLOPT_HTTPPROXYTUNNEL, 1);
	}

    $data = curl_exec($curl_obj);
    curl_close($curl_obj);

	return $data;
}


// this function has a whopper of a RegEx.
// I nabbed it from http://www.phpbuilder.com/annotate/message.php3?id=1000234 - XOXO spiderr
function parse_xml_attributes($str) {
	$parameters = array('', '');
	$regexp_str = "/([A-Za-z0-9_-]+)(?:\\s*=\\s*(?:(\"|\')((?:[\\\\].|[^\\\\]?)*?)(?:\\2)|([^=\\s]*)))?/";
	preg_match_all($regexp_str,$str,$matches,PREG_SET_ORDER);
	while (list($key,$match) = each($matches)) {
		$attrib = $match[1];
		$value = $match[sizeof($match)-1]; // The value can be at different indexes because of optional quotes, but we know it's always at the end.
		$value = preg_replace("/\\\\(.)/","\\1",$value);
		$parameters[$attrib] = trim( $value, '\"' );
	}
	return $parameters;
}


// XML Entity Mandatory Escape Characters
function xmlentities($string, $quote_style=ENT_QUOTES)
{
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
	while (strstr($pUrl, '&&')) {
		 $pUrl = str_replace('&&', '&', $pUrl);
	}
	while (strstr($pUrl, '&amp;&amp;')) {
		$pUrl = str_replace('&amp;&amp;', '&amp;', $pUrl);
	}
	// header locates should not have the &amp; in the address it breaks things
	while (strstr($pUrl, '&amp;')) {
		$pUrl = str_replace('&amp;', '&', $pUrl);
	}
	header('Location: ' . $pUrl);
	session_close();
	exit();
}

function array_diff_keys()
{
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

function trim_array( &$pArray ) {
	if( is_array( $pArray ) ) {
		foreach( array_keys( $pArray ) as $key ) {
			if( is_string( $pArray[$key] ) ) {
				$pArray[$key] = trim( $pArray[$key] );
			}
		}
	}
}

function clean_file_path($path) {
 	$path = (!empty($_SERVER["SERVER_SOFTWARE"]) && strpos($_SERVER["SERVER_SOFTWARE"],"IIS") ? str_replace( '\/', '\\', $path) : $path);
	return $path;
}


function compare_links($ar1, $ar2) {
    return $ar1["links"] - $ar2["links"];
}

function compare_backlinks($ar1, $ar2) {
    return $ar1["backlinks"] - $ar2["backlinks"];
}

function r_compare_links($ar1, $ar2) {
    return $ar2["links"] - $ar1["links"];
}

function r_compare_backlinks($ar1, $ar2) {
    return $ar2["backlinks"] - $ar1["backlinks"];
}

function compare_images($ar1, $ar2) {
    return $ar1["images"] - $ar2["images"];
}

function r_compare_images($ar1, $ar2) {
    return $ar2["images"] - $ar1["images"];
}

function compare_files($ar1, $ar2) {
    return $ar1["files"] - $ar2["files"];
}

function r_compare_files($ar1, $ar2) {
    return $ar2["files"] - $ar1["files"];
}

function compare_versions($ar1, $ar2) {
    return $ar1["versions"] - $ar2["versions"];
}

function r_compare_versions($ar1, $ar2) {
    return $ar2["versions"] - $ar1["versions"];
}

function compare_changed($ar1, $ar2) {
    return $ar1["lastChanged"] - $ar2["lastChanged"];
}

function r_compare_changed($ar1, $ar2) {
    return $ar2["lastChanged"] - $ar1["lastChanged"];
}

function chkgd2() {
    if (!isset($_SESSION['havegd2'])) {
#   TODO test this logic in PHP 4.3
#   if (version_compare(phpversion(), "4.3.0") >= 0) {
#	 $_SESSION['havegd2'] = true;
#   } else {
    ob_start();

    phpinfo (INFO_MODULES);
    $_SESSION['havegd2'] = preg_match('/GD Version.*2.0/', ob_get_contents());
    ob_end_clean();
#	}
    }

    return $_SESSION['havegd2'];
}

function httpScheme() {
    return 'http' . ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 's' : '');
}

function httpPrefix() {
    /*
       if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
       $rv = 'https://' . $_SERVER['HTTP_HOST'];

       if ($_SERVER['SERVER_PORT'] != 443)
       $rv .= ':' . $_SERVER['SERVER_PORT'];
       } else {
       $rv = 'http://' . $_SERVER['HTTP_HOST'];

       if ($_SERVER['SERVER_PORT'] != 80)
       $rv .= ':' . $_SERVER['SERVER_PORT'];
       }

       return $rv;
     */
    /* Warning by zaufi: as far as I saw in my apache 1.3.27
     * there is no need to add port if it is non default --
     * $_SERVER['HTTP_HOST'] already contain it ...
     */
    return 'http'.((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 's' : '').'://'.$_SERVER['HTTP_HOST'];
}

if (!function_exists('file_get_contents')) {
    function file_get_contents($f) {
	ob_start();

	$retval = @readfile($f);

	if (false !== $retval) { // no readfile error
	    $retval = ob_get_contents();
	}

	ob_end_clean();
	return $retval;
    }

}


?>
