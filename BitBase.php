<?php
/**
 * Virtual bitweaver base class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitBase.php,v 1.1.1.1.2.6 2005/08/02 08:33:30 lsces Exp $
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
require_once ( KERNEL_PKG_PATH.'BitDb.php' );
include_once ( KERNEL_PKG_PATH.'BitCache.php' );
require_once( BIT_PKG_PATH.'util/pear/Date.php' );

define( 'STORAGE_BINARY', 1 );
define( 'STORAGE_IMAGE', 2 );

/**
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived bitweaver classes that require database access.
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
    }
    /**
    * Sets database mechanism for the instance
    * @param pDB the instance of the database mechanism
    **/
    function setDatabase(&$pDB)
    {
        // set internal db and retrieve values
        $this->mDb = &$pDB;
    }
    /**
    * Determines if there is a valide database connection
    **/
	function isDatabaseValid() {
		return( !empty( $this->mDb->mDb ) && $this->mDb->mDb->_connectionID );
	}

    /**
    * Determines if any given variable exists and is a number
    **/
	function verifyId( $pId ) {
		return( !empty( $pId ) && is_numeric( $pId ) );
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
				$_smarty_tpl_file = 'file:'.BIT_PKG_PATH."$pPackage/templates/$pTemplate";
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


	function getPreference($pName, $pDefault = '') {
		global $gBitSystem;
		return $gBitSystem->getPreference( $pName, $pDefault );
	}

/*
	function get_user_preference($pUserId, $name, $default = '') {
		global $user_preferences;

		if (!$pUserId)
			return NULL;

		/ **** Quick Hack ****
		  We need to convert all calls to get_user_preference so they pass the id and not the username
		  This will handle legacy calls until they are all changed
		********************* /
		if (!is_numeric($pUserId)) {
			$query = "SELECT `user_id` FROM `".BIT_DB_PREFIX."users_users` where `login` = ?";
			$result = $this->query($query, array($pUserId));
			$pUserId = $result->fields['user_id'];
			if (!$pUserId) return NULL;
		}
		/ **** End quick hack *** /

		if (!isset($user_preferences[$pUserId][$name])) {
			$query = "select `value`
					  FROM `".BIT_DB_PREFIX."tiki_user_preferences` tup where `pref_name`=? AND tup.`user_id`= ?";

			$result = $this->query($query, array( "$name", $pUserId));

			if ($result->numRows()) {
				$res = $result->fetchRow();

				$user_preferences[$pUserId][$name] = $res["value"];
			} else {
				$user_preferences[$pUserId][$name] = $default;
			}
		}

		return $user_preferences[$pUserId][$name];
	}
*/
	function debug( $pLevel = 99 ) {
		if( is_object( $this->mDb ) ) {
			$this->mDb->debug( $pLevel );
		}
	}

	function query($pQuery, $pValues = NULL, $pNumRows =BIT_QUERY_DEFAULT, $pOffset=BIT_QUERY_DEFAULT, $pCacheTime=BIT_QUERY_DEFAULT ) {
		return $this->mDb->query($pQuery, $pValues, $pNumRows, $pOffset, $pCacheTime);
	}

	function getAssoc($pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT) {
		return $this->mDb->getAssoc($pQuery, $pValues, $pForceArray, $pFirst2Cols,$pCacheTime);
	}

	function getOne($pQuery, $pValues = NULL, $pNumRows = 0, $pOffset = 0, $pCacheTime=BIT_QUERY_DEFAULT) {
		return $this->mDb->getOne($pQuery, $pValues, $pNumRows, $pOffset,$pCacheTime);
	}

	function convert_binary() {
		return $this->mDb->convert_binary();
	}

	function convert_sortmode($pSortMode) {
		return $this->mDb->convert_sortmode($pSortMode);

	}
    function sql_cast($pVar,$pType) {
		return $this->mDb->sql_cast($pVar,$pType);

	}

	function associateInsert($insertTable, $insertData) {
		return $this->mDb->associateInsert($insertTable, $insertData);
	}

	function associateUpdate($updateTable, $updateData, $updateId) {
		return $this->mDb->associateUpdate($updateTable, $updateData, $updateId);
	}

    function GenID( $seqTitle ) {
		return $this->mDb->GenID( $seqTitle );
    }

	/** Calls ADODB method to begin a transaction, calls can be nested
	*/
	function StartTrans() {
		 return $this->mDb->StartTrans();
	}

	/** Calls ADODB method to finalize a transaction, calls can be nested
	*/
	function CompleteTrans() {
		 return $this->mDb->CompleteTrans();
	}

	function RollbackTrans() {
		 return $this->mDb->RollbackTrans();
	}

	function MetaTables( $ttype = false, $showSchema = false, $mask=false ) {
		 return $this->mDb->MetaTables( $ttype, $showSchema, $mask );
	}

	// =-=-=-=-=-=-=-=-=-=-=- Non-DB related functions =-=-=-=-=-=-=-=-=-=-=-=-=

    /**
    * Prepares parameters with default values for any getList function
    * @param pParamHash hash of parameters for any getList() function
    * @return the link to display the page.
    */
	function prepGetList( &$pListHash ) {
		global $gBitSmarty;

		// If offset is set use it if not then use offset =0
		// use the maxRecords php variable to set the limit
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
			$pListHash['max_records'] = $gBitSystem->getPreference( "maxRecords", 10 );
		}

		if( empty( $pListHash['offset'] ) ) {
			if (isset($pListHash['page'])) {
				$pListHash['offset'] = ($page = $pListHash['page'] - 1) * $pListHash['max_records'];
			} else {
				if ( isset( $_REQUEST["offset"] ) ) {
					$pListHash['offset'] = $_REQUEST['offset'];
				} else {
					$pListHash['offset'] = 0;
				}
			}
		}

		if( isset( $_REQUEST["find"] ) ) {
			$pListHash['find']= $_REQUEST["find"];
		} else {
			$pListHash['find'] = NULL;
		}
		$gBitSmarty->assign( 'find', $pListHash['find'] );

		if( isset( $_REQUEST['date'] ) ) {
			$pListHash['date']= $_REQUEST['date'];
		} else {
			$pListHash['date'] = $now = date("U");
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


function unlink_r($path,$followLinks=false) {

   $dir = opendir($path) ;
   while ( $entry = readdir($dir) ) {

       if ( is_file( "$path/$entry" ) || ((!$followLinks) && is_link("$path/$entry")) ) {
			unlink( "$path/$entry" );
       } elseif ( is_dir( "$path/$entry" ) && $entry!='.' && $entry!='..' ) {
           unlink_r( "$path/$entry" ) ;
       }
   }
   closedir($dir) ;
   return rmdir($path);
}

function tp_http_request($url, $reqmethod = NULL ) {
	require_once( UTIL_PKG_PATH . 'pear/HTTP/Request.php' );
	if( empty( $reqmethod ) ) {
		$reqmethod = HTTP_REQUEST_METHOD_GET;
	}
	global $use_proxy,$proxy_host,$proxy_port;

	// test url :
	$url = trim( $url );
	if (!preg_match("/^[-_a-zA-Z0-9:\/\.\?&;=\+]*$/",$url)) {
		return false;
	}
	// rewrite url if sloppy # added a case for https urls
	if ( (substr($url,0,7) <> "http://") && (substr($url,0,8) <> "https://") ) {
		$url = "http://" . $url;
	}
	// (cdx) params for HTTP_Request.
	// The timeout may be defined by a DEFINE("HTTP_TIMEOUT",5) in some file...
	$aSettingsRequest=array("method"=>$reqmethod,"timeout"=>5);

	if (substr_count($url, "/") < 3) {
		$url .= "/";
	}
	// Proxy settings
	if ($use_proxy == 'y') {
		$aSettingsRequest["proxy_host"]=$proxy_host;
		$aSettingsRequest["proxy_port"]=$proxy_port;
	}
	$req = &new HTTP_Request($url, $aSettingsRequest);
	// (cdx) return false when can't connect
	// I prefer throw a PEAR_Error. You decide ;)
	if (PEAR::isError($oError=$req->sendRequest())) {
		return false;
	}
	$data = $req->getResponseBody();

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
 	$path = (strpos($_SERVER["SERVER_SOFTWARE"],"IIS") ? str_replace( '\/', '\\', $path) : $path);
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
