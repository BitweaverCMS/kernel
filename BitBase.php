<?php
/**
 * Virtual bitweaver base class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitBase.php,v 1.39 2007/06/17 08:19:31 squareing Exp $
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

define( 'STORAGE_BINARY', 1 );
define( 'STORAGE_IMAGE', 2 );

/**
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived bitweaver classes that require database access.
 *
 * @package kernel
 */
class BitBase {
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

	/**
	 * During initialisation, we assign a name which is used by the class.
	 * @param pName a unique identified used in caching and database
	 * mechanisms
	 **/
	function BitBase( $pName = '' ) {
		global $gBitDb;
		$this->mName = $pName;
		$this->mCacheTime = BIT_QUERY_CACHE_TIME;
		if( is_object( $gBitDb ) ) {
			$this->setDatabase($gBitDb);
		}
		$this->mErrors = array();
		$this->mInfo = array();
	}

	/**
	 * Sets database mechanism for the instance
	 * @param pDB the instance of the database mechanism
	 **/
	function setDatabase( &$pDB ) {
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

		// if sort_mode is not set then use last_modified_desc
		if( empty( $pListHash['sort_mode'] )) {
			if( empty( $_REQUEST["sort_mode"] )) {
				$pListHash['sort_mode'] = 'last_modified_desc';
			} else {
				$pListHash['sort_mode'] = $_REQUEST['sort_mode'];
			}
		}

		// sort_modes are set, we check them against our selected sort_mode
		if( !empty( $pListHash['sort_modes'] ) && is_array( $pListHash['sort_modes'] )) {
			$is_valid = FALSE;
			foreach( $pListHash['sort_modes'] as $mode ) {
				if( preg_match( "/^{$mode}_(desc|asc)$/", $pListHash['sort_mode'] )) {
					$is_valid = TRUE;
				}
			}

			if( !$is_valid ) {
				$pListHash['sort_mode'] = '';
			}
		}

		if( empty( $pListHash['max_records'] )) {
			global $gBitSystem;
			$pListHash['max_records'] = $gBitSystem->getConfig( "max_records", 10 );
		}

		if( !isset( $pListHash['offset'] )) {
			if( isset($pListHash['page'] )) {
				$pListHash['offset'] = ($pListHash['page'] - 1) * $pListHash['max_records'];
			} else {
				if( isset( $_REQUEST["offset"] )) {
					$pListHash['offset'] = $_REQUEST['offset'];
				} elseif( isset( $_REQUEST['page'] ) && is_numeric( $_REQUEST['page'] )) {
					$pListHash['offset'] = ($_REQUEST['page'] - 1) * $pListHash['max_records'];
				} elseif( isset( $_REQUEST['list_page'] ) && is_numeric( $_REQUEST['list_page'] )) {
					$pListHash['offset'] = ( $_REQUEST['list_page'] - 1 ) * $pListHash['max_records'];
				} else {
					$pListHash['offset'] = 0;
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
}
?>
