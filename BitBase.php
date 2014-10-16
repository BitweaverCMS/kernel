<?php
/**
 * Virtual bitweaver base class
 *
 * @package kernel
 * @version $Header$
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
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

define( 'STORAGE_BINARY', 1 );
define( 'STORAGE_IMAGE', 2 );


interface BitCacheable  {
	public function getCacheKey(); 
}



/**
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived bitweaver classes that require database access.
 *
 * @package kernel
 */
abstract class BitBase {
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
	var $mInfo = array();

	/**
	 * Data hash that contains logging information relevant to database operations
	 **/
	var $mLogs = array();

	var $mPreventCache = FALSE;


	const CACHE_STATE_NONE = 0;
	const CACHE_STATE_DELETE = -1;
	const CACHE_STATE_ADDED = 1;
	const CACHE_STATE_STORED = 2;

	function __construct( $pName = '' ) {
		global $gBitDb;
		$this->mName = $pName;
		$this->mCacheTime = BIT_QUERY_CACHE_TIME;
		if( is_object( $gBitDb ) ) {
			$this->setDatabase($gBitDb);
		}
		$this->mErrors = array();
		$this->mInfo = array();
	}

	function __destruct() {
		unset( $this->mDb );
		$this->storeInCache();
	}

	/**
	 * During initialisation, we assign a name which is used by the class.
	 * @param pName a unique identified used in caching and database
	 * mechanisms
	 **/
	function BitBase( $pName = '' ) {
		self::__construct( $pName );
	}

	/**
	 * Delete content object and all related records
	 *
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	protected function expunge() {
		$this->clearFromCache();
	}

	public function clearFromCache( &$pParamHash=NULL ) {
		$this->mCacheTime = BIT_QUERY_CACHE_DISABLE;
		if( $this->isCacheableObject() && static::isCacheActive() && ($cacheKey = $this->getCacheUuid()) ) {
			$ret = apc_delete( $cacheKey );
		}
	}

	/**
	 * storeInCache
	 *
	 * @param string $pCacheKey unique identifier for object in cache store
	 * @access public
	 * @return TRUE on success, FALSE on failure
	 */
	private function storeInCache() {
		$ret = FALSE;
		if( $this->isCacheableObject() && static::isCacheActive() && ($cacheKey = $this->getCacheUuid()) ) {
			$ret = apc_store( $cacheKey, $this, 3600 );
		}
		return $ret;
	}

	public function __wakeup() {
		global $gBitDb;
		$this->setDatabase( $gBitDb );
	}

	public static function loadFromCache( $pCacheKey ) {
		$ret = NULL;
		if( static::isCacheActive() && static::isCacheableClass() && !empty( $pCacheKey ) ) {
			if( $ret = apc_fetch( static::getCacheUuidFromKey( $pCacheKey ) ) ) {
			}
		}
		return $ret;
	}

	public function getCacheUuid() {
		return static::getCacheUuidFromKey( $this->getCacheKey() );
	}

	public static function getCacheUuidFromKey( $pCacheUuid = '' ) {
		global $gBitDbName, $gBitDbHost;
		$ret = $_SERVER['HTTP_HOST'].':'.$gBitDbName.'@'.$gBitDbHost.':'.get_called_class().'#'.$pCacheUuid;
		return $ret;
	}

	public static function isCacheActive() {
		// only apc is supported for now.
		return (function_exists( 'apc_add' ) && defined( 'BIT_CACHE_OBJECTS' ) && BIT_CACHE_OBJECTS );
	}

	public function isCacheableObject() {
		return method_exists( $this, 'getCacheKey' ) && empty( $this->mPreventCache );
	}

	public static function isCacheableClass() {
		return false;
	}

	public function isCached() {
		return apc_exists( $this->getCacheUuid() );
	}

	public function setCacheableObject( $pCacheState = TRUE ) {
		$this->mPreventCache = !empty( $pCacheState );
	}

    final public static function getClass() {
        return get_called_class();
    }

	public function getConfig( $pName, $pDefault = NULL ) {
		global $gBitSystem;
		return $gBitSystem->getConfig( $pName, $pDefault );
	}

	public function isFeatureActive( $pFeatureName ) {
		global $gBitSystem;
		return $gBitSystem->isFeatureActive( $pFeatureName );
	}

	/**
	 * Sets database mechanism for the instance
	 * @param pDB the instance of the database mechanism
	 **/
	public function setDatabase( &$pDB ) {
		// set internal db and retrieve values
		$this->mDb = &$pDB;
		$this->dType = $this->mDb->mType;
	}

	/**
	 * Determines if there is a valide database connection
	 **/
	public function isDatabaseValid() {
		return( !empty( $this->mDb ) && $this->mDb->isValid() );
	}

	/**
	 * Return pointer to current Database
	 **/
	public function getDb() {
		return ( !empty( $this->mDb ) ? $this->mDb : NULL  );
	}

	function debugMarkTime() {
		$this->mDebugMicrotime = microtime(1);
	}

	function debugOutput( $pString ) {
		global $gDebug;
		if( $gDebug || !defined( 'IS_LIVE' ) || !IS_LIVE ) {
			if( empty(  $this->mLastOutputTime ) ) {
				$elapsed = (float)0.000;
			} else {
				$elapsed = (microtime(1) - (float)$this->mLastOutputTime);
			}
			if( !empty( $this->mDebugMicrotime ) ) {
				$pString = "ELAPSED TIME: ".round( (float)((microtime(1) - $this->mDebugMicrotime)), 3).' sec, +'.round( $elapsed, 3 ).' '.$pString;
			}
			bit_error_log( $pString );
			$this->mLastOutputTime = microtime(1);
		}
	}
	// =-=-=-=-=-=-=-=-=-=-=- Non-DB related functions =-=-=-=-=-=-=-=-=-=-=-=-=

	/**
	 * verifyIdParamter Determines if any given variable exists and is a number
	 *
	 * @param mixed $pId this can be a string, number or array. if it's an array, all values in the array will be checked to see if they are numeric
	 * @access public
	 * @return TRUE if the input was numeric, FALSE if it wasn't
	 */
	public static function verifyIdParamter( &$pParamHash, $pKey ) {
		// check all possibilities as quickly as possible as this function is called frequently
		return !empty( $pParamHash[$pKey] ) && (is_int( $pParamHash[$pKey] ) || ctype_digit( $pParamHash[$pKey] ) || (is_numeric($pParamHash[$pKey]) ? intval( $pParamHash[$pKey] ) == $pParamHash[$pKey] : false));
	}

	/**
	 * verifyId Determines if any given variable exists and is a number
	 *
	 * @param mixed $pId this can be a string, number or array. if it's an array, all values in the array will be checked to see if they are numeric
	 * @access public
	 * @return TRUE if the input was numeric, FALSE if it wasn't
	 */
	public static function verifyId( $pId ) {
		if( empty( $pId )) {
			return FALSE;
		}
		if( is_array( $pId )) {
			foreach( $pId as $id ) {
				if( (is_int( $id ) || ctype_digit( $id ) || (is_numeric( $id ) ? intval( $id ) == $id : false)) ) {
					return FALSE;
				}
			}
			return TRUE;
		}
		return( !empty( $pId ) && (is_int( $pId ) || ctype_digit( $pId ) || (is_numeric( $pId ) ? intval( $pId ) == $pId : false)) );
;
	}

	/**
	 * getParameter Gets a hash value it exists, or returns an optional default
	 *
	 * @param associativearray $pParamHash Hash of key=>value pairs
	 * @param string $pHashKey Key used to search for value
	 * @param string $pDefault Default value to return if not found. NULL if nothing is passed in.
	 * @access public
	 * @return TRUE if the input was numeric, FALSE if it wasn't
	 */
	public static function getParameter( &$pParamHash, $pKey, $pDefaultValue=NULL ) {
		if( isset( $pParamHash[$pKey] ) ) {
			$ret = $pParamHash[$pKey];
		} else {
			$ret = $pDefaultValue;
		}

		return $ret;
	}

	/**
	 * This method should be THE method used to display a template. php files should not
	 * access $gBitSmarty directly.
	 *
	 * @param string pMsg error message to be displayed
	 * @return none this function will DIE DIE DIE!!!
	 * @access public
	 **/
	public function display( $pPackage, $pTemplate ) {
		global $gBitSmarty, $gBitLanguage, $style, $style_base;
		if( !empty( $style ) && !empty( $style_base )) {
			if (file_exists(BIT_THEMES_PATH."styles/$style_base/$pTemplate")) {
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

	/**
	 * Assign an entry to the mInfo hash if the current object is valid
	 * @param pFieldName the hash key to retrieve the value
	 * @param pValue the value of the hash key
	 **/
	public function setField( $pFieldName, $pValue ) {
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mInfo[$pFieldName] = $pValue;
			$ret = TRUE;
		}
		return $ret;
	}

	/**
	 * Returns entry from the mInfo hash if field exists
	 * @param pFieldName the hash key to retrieve the value
	 * @param pDefault the value to return of there is now hash value present
	 **/
	public function getField( $pFieldName, $pDefault = NULL ) {
		return( !empty( $this->mInfo[$pFieldName] ) ? $this->mInfo[$pFieldName] : $pDefault );
	}

	/**
	 * Prepares parameters with default values for any getList function
	 * @param pParamHash hash of parameters for any getList() function
	 * @return the link to display the page.
	 */
	public static function prepGetList( &$pListHash ) {
		global $gBitSmarty, $gBitSystem;

		// valid_sort_modes are set, we check them against our selected sort_mode
		if( !empty( $pListHash['sort_mode'] ) && !empty( $pListHash['valid_sort_modes'] ) && is_array( $pListHash['valid_sort_modes'] )) {
			if( is_string( $pListHash['sort_mode'] )) {
				if( !static::verifySortMode( $pListHash['sort_mode'], $pListHash['valid_sort_modes'] )) {
					$pListHash['sort_mode'] = '';
				}
			} elseif( is_array( $pListHash['sort_mode'] )) {
				// make sure all values of the sort_mode array match something in the valid valid_sort_modes hash
				foreach( $pListHash['sort_mode'] as $key => $mode ) {
					if( !static::verifySortMode( $mode, $pListHash['valid_sort_modes'] )) {
						unset( $pListHash['sort_mode'][$key] );
					}
				}
			}
		}

		if( empty( $pListHash['max_records'] ) || !is_numeric( $pListHash['max_records'] ) ) {
			global $gBitSystem;
			$pListHash['max_records'] = $gBitSystem->getConfig( "max_records", 10 );
		}
		$pListHash['max_records'] = (int)$pListHash['max_records'];

		if( !isset( $pListHash['offset'] ) || !is_numeric( $pListHash['offset'] ) ) {
			$pListHash['offset'] = 0;
			if( isset($pListHash['page'] )) {
				$pListHash['offset'] = ($pListHash['page'] - 1) * $pListHash['max_records'];
			} else {
				if( !empty( $_REQUEST["offset"] )) {
					$pListHash['offset'] = $_REQUEST['offset'];
				} elseif( isset( $_REQUEST['page'] ) && is_numeric( $_REQUEST['page'] ) && $_REQUEST['page'] > 0 ) {
					$pListHash['offset'] = ($_REQUEST['page'] - 1) * $pListHash['max_records'];
				} elseif( isset( $_REQUEST['list_page'] ) && is_numeric( $_REQUEST['list_page'] ) && $_REQUEST['list_page'] > 0 ) {
					$pListHash['offset'] = ( $_REQUEST['list_page'] - 1 ) * $pListHash['max_records'];
				}
			}
		}

		// migrate towards a safer hash key
		if( empty( $pListHash['user_id'] ) && !empty( $pListHash['lookup_user_id'] ) ) {
			$pListHash['user_id'] = $pListHash['lookup_user_id'];
		}

		// Don't use  $_REQUEST["find"] as it can really screw with modules on search pages
		if( !empty( $pListHash["find"] )) {
			$pListHash['find']= $pListHash["find"];
		} else {
			$pListHash['find'] = NULL;
		}
		$gBitSmarty->assign( 'find', $pListHash['find'] );

		if( isset( $_REQUEST['date'] )) {
			$pListHash['date']= $_REQUEST['date'];
		} else {
			$pListHash['date'] = $gBitSystem->getUTCTime();
		}

		if( empty( $pListHash['load_comments'] )) {
			$pListHash['load_comments'] = FALSE;
		}
		if( empty( $pListHash['load_num_comments'] )) {
			$pListHash['load_num_comments'] = FALSE;
		}
		if( empty( $pListHash['parse_data'] )) {
			$pListHash['parse_data'] = FALSE;
		}
	}

	/**
	 * verifySortMode is used to validate a given sort_mode agains an array of valid sort modes
	 *
	 * @param string $pSortMode sort mode to check
	 * @param array $pValidSortModes array of available sort modes
	 * @access public
	 * @return TRUE on success, FALSE on failure
	 */
	public static function verifySortMode( $pSortMode, $pValidSortModes ) {
		if( !empty( $pSortMode ) && is_string( $pSortMode ) && !empty( $pValidSortModes ) && is_array( $pValidSortModes )) {
			foreach( $pValidSortModes as $mode ) {
				// we will not check the table - that would just be too complicated...
				if( preg_match( "/^(\w+\.)?{$mode}_(desc|asc)$/", $pSortMode )) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}


	/**
	* Updates results from any getList function to provide the control set
	* displaying in the smarty template
	* @param array hash of parameters returned by any getList() function
	* @return - none the hash is updated via the reference
	*/
	public static function postGetList( &$pListHash ) {
		global $gBitSystem;
		$pListHash['listInfo']['page_records'] = (!empty( $pListHash['page_records'] ) ? $pListHash['page_records'] : $pListHash['max_records'] );
		if( !isset( $pListHash['cant'] ) ) {
			$pListHash['cant'] = $pListHash['max_records'];
		}

		if( !isset( $pListHash['offset'] ) || !is_numeric( $pListHash['offset'] ) ) {
			$pListHash['offset'] = 0;
		}

		$pListHash['listInfo']['total_records'] = $pListHash['cant'];
		$pListHash['listInfo']['total_pages'] = ceil( $pListHash['cant'] / $pListHash['max_records'] );
		$pListHash['listInfo']['current_page'] = 1 + ( $pListHash['offset'] / $pListHash['max_records'] );

		if( isset( $pListHash["cant"] ) && $pListHash["cant"] > ( $pListHash['offset'] + $pListHash['max_records'] ) ) {
			$pListHash['listInfo']['next_offset'] = $pListHash['offset'] + $pListHash['max_records'];
		} else {
			$pListHash['listInfo']['next_offset'] = -1;
		}

		// If offset is > 0 then prev_offset
		if( $pListHash['offset'] > 0 ) {
			$pListHash['listInfo']['prev_offset'] = $pListHash['offset'] - $pListHash['max_records'];
		} else {
			$pListHash['listInfo']['prev_offset'] = -1;
		}

		$pListHash['listInfo']['offset'] = $pListHash['offset'];
		$pListHash['listInfo']['find'] = $pListHash['find'];
		if( !empty( $pListHash['sort_mode'] ) ) {
			$pListHash['listInfo']['sort_mode'] = $pListHash['sort_mode'];
		}
		$pListHash['listInfo']['max_records'] = $pListHash['max_records'];

		$pListHash['listInfo']['block_pages'] = 3;
		$pListHash['listInfo']['start_block'] = floor( $pListHash['offset'] / $pListHash['max_records'] ) * $pListHash['max_records'] + 1;

		// calculate what links to show
		if( $gBitSystem->isFeatureActive( 'site_direct_pagination' ) ) {
			// number of continuous links to display on either side
			$continuous = 5;
			// number of skipping links to display on either side
			$skipping = 5;

			// size of steps to take when skipping
			// if you have more than 1000 pages, you should consider not using the pagination form
			if( $pListHash['listInfo']['total_pages'] < 50 ) {
				$step = 5;
			} elseif( $pListHash['listInfo']['total_pages'] < 100 ) {
				$step = 10;
			} elseif( $pListHash['listInfo']['total_pages'] < 250 ) {
				$step = 25;
			} elseif( $pListHash['listInfo']['total_pages'] < 500 ) {
				$step = 50;
			} else {
				$step = 100;
			}

			$prev  = ( $pListHash['listInfo']['current_page'] - $continuous > 0 ) ? $pListHash['listInfo']['current_page'] - $continuous : 1;
			$next  = ( $pListHash['listInfo']['current_page'] + $continuous < $pListHash['listInfo']['total_pages'] ) ? $pListHash['listInfo']['current_page'] + $continuous : $pListHash['listInfo']['total_pages'];
			for( $i = $pListHash['listInfo']['current_page'] - 1; $i >= $prev; $i -= 1 ) {
				$pListHash['listInfo']['block']['prev'][$i] = $i;
			}
			if( $prev != 1 ) {
				// replace the last of the continuous links with a ...
				$pListHash['listInfo']['block']['prev'][$i + 1] = "&hellip;";
				// add $skipping links to pages separated by $step pages
				if( ( $min = $pListHash['listInfo']['current_page'] - $continuous - ( $step * $skipping ) ) < 0 ) {
					$min = 0;
				}
				for( $j = ( floor( $i / $step ) * $step ); $j > $min; $j -= $step ) {
					$pListHash['listInfo']['block']['prev'][$j] = $j;
				}
				$pListHash['listInfo']['block']['prev'][1] = 1;
			}
			// reverse array that links are in the correct order
			if( !empty( $pListHash['listInfo']['block']['prev'] ) ) {
				$pListHash['listInfo']['block']['prev'] = array_reverse( $pListHash['listInfo']['block']['prev'], TRUE );
			}

			// here we start adding next links
			for( $i = $pListHash['listInfo']['current_page'] + 1; $i <= $next; $i += 1 ) {
				$pListHash['listInfo']['block']['next'][$i] = $i;
			}
			if( $next != $pListHash['listInfo']['total_pages'] ) {
				// replace the last of the continuous links with a ...
				$pListHash['listInfo']['block']['next'][$i - 1] = "&hellip;";
				// add $skipping links to pages separated by $step pages
				if( ( $max = $pListHash['listInfo']['current_page'] + $continuous + ( $step * $skipping ) ) > $pListHash['listInfo']['total_pages'] ) {
					$max = $pListHash['listInfo']['total_pages'];
				}
				for( $j = ( ceil( $i / $step ) * $step ); $j < $max; $j += $step ) {
					$pListHash['listInfo']['block']['next'][$j] = $j;
				}
				$pListHash['listInfo']['block']['next'][$pListHash['listInfo']['total_pages']] = $pListHash['listInfo']['total_pages'];
			}
		}
	}

}

