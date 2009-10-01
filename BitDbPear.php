<?php
/**
 * ADOdb Library interface Class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitDbPear.php,v 1.20 2009/10/01 13:45:42 wjames5 Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @author spider <spider@steelsun.com>
 */

/**
 * ensure your AdoDB install is a subdirectory off your include path
 */
require_once 'DB.php';

require_once( KERNEL_PKG_PATH.'BitDbBase.php' );

/**
 * This class is used for database access and provides a number of functions to help
 * with database portability.
 *
 * Currently used as a base class, this class should be optional to ensure bitweaver
 * continues to function correctly, without a valid database connection.
 *
 * @package kernel
 */
class BitDbPear extends BitDb
{
	function BitDbPear( $pPearDsn=NULL, $pPearOptions = NULL )
	{
		global $gDebug;
		parent::BitDb();

		if( empty( $pPearDsn ) ) {
			global $gBitDbType, $gBitDbUser, $gBitDbPassword, $gBitDbHost, $gBitDbName;

			$pPearDsn = array(
				'phptype'  => $gBitDbType,
				'username' => $gBitDbUser,
				'password' => $gBitDbPassword ,
				'database' => $gBitDbHost.'/'.$gBitDbName,
			);
		}

		if( empty( $pPearOptions ) ) {
			$pPearOptions = array(
				'debug'       => 2,
				'persistent'  => false,
				'portability' => DB_PORTABILITY_ALL,
			);
		}

		PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'bit_pear_login_error' );

		$this->mDb = DB::connect($pPearDsn, $pPearOptions);

		if( PEAR::isError( $this->mDb ) ) {
			$this->mErrors['db_connect'] = $this->mDb->getDebugInfo();
		} else {
		
			$this->mDb->setFetchMode( DB_FETCHMODE_ASSOC );
			// Default to autocommit unless StartTrans is called
			$this->mDb->autoCommit( TRUE );
	
			$this->mType = $pPearDsn['phptype'];
			$this->mName = $pPearDsn['database'];
		}

		PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'bit_pear_error_handler');
	}
	/**
	* Quotes a string to be sent to the database which is
	* passed to function on to AdoDB->qstr().
	* @todo not sure what its supposed to do
	* @param pStr string to be quotes
	* @return quoted string using AdoDB->qstr()
	*/
	function qstr($pStr)
	{
		return $this->mDb->quote($pStr);
	}

	/** Queries the database, returning an error if one occurs, rather
	* than exiting while printing the error. -rlpowell
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pError the error string to modify and return
	* @param pValues an array of values used in a parameterised query
	* @param pNumRows the number of rows (LIMIT) to return in this query
	* @param pOffset the row number to begin returning rows from. Used in
	* @return an AdoDB RecordSet object
	* conjunction with $pNumRows
	* @todo currently not used anywhere.
	*/
	function queryError( $pQuery, &$pError, $pValues = NULL, $pNumRows = -1, $pOffset = -1 )
	{
		$this->convertQuery($pQuery);
		if ($pNumRows == -1 && $pOffset == -1)
		$result = $this->mDb->Execute($pQuery, $pValues);
		else
		$result = $this->mDb->SelectLimit($pQuery, $pNumRows, $pOffset, $pValues);
		if (!$result)
		{
			$pError = $this->mDb->ErrorMsg();
			$result=false;
		}
		//count the number of queries made
		$this->mNumQueries++;
		//$this->debugger_log($pQuery, $pValues);
		return $result;
	}

	/** Queries the database reporting an error if detected
	* than exiting while printing the error. -rlpowell
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pValues an array of values used in a parameterised query
	* @param pNumRows the number of rows (LIMIT) to return in this query
	* @param pOffset the row number to begin returning rows from. Used in
	* conjunction with $pNumRows
	* @return an AdoDB RecordSet object
	*/
	function query($query, $values = null, $numrows = BIT_QUERY_DEFAULT, $offset = BIT_QUERY_DEFAULT, $pCacheTime=BIT_QUERY_DEFAULT )
	{
		$this->convertQuery($query);
		if( empty( $this->mDb ) ) {
			return FALSE;
		}

		$this->queryStart();

		if( !is_numeric( $numrows ) ) {
			$numrows = BIT_QUERY_DEFAULT;
		}

		if( !is_numeric( $offset ) ) {
			$offset = BIT_QUERY_DEFAULT;
		}

		if( $numrows == BIT_QUERY_DEFAULT && $offset == BIT_QUERY_DEFAULT ) {
			$result = $this->mDb->query( $query, $values );
		} else {
			$result = $this->mDb->limitQuery( $query, $offset, $numrows, $values );
		}

		$this->queryComplete();

		return $result;
	}

	function queryComplete() {
		if( !empty( $this->mDebug ) ) {
			print( "<tt>\n".vsprintf( str_replace( '?', "'%s'", $this->mDb->last_query ), $this->mDb->last_parameters )."\n</tt><br/>" );
			if( $this->mDebug == 99 ) {
				bt();
			}
		}
		parent::queryComplete();
	}

	/**
	* compatibility function
	*/
	function Execute($pQuery, $pNumRows = false, $zf_cache = false, $pCacheTime=BIT_QUERY_DEFAULT) {
		return $this->query( $pQuery, NULL, $pNumRows, BIT_QUERY_DEFAULT, $pCacheTime );
	}

	/** Executes the SQL and returns all elements of the first column as a 1-dimensional array. The recordset is discarded for you automatically. If an error occurs, false is returned.
	* See AdoDB GetCol() function for more detail.
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pValues an array of values used in a parameterised query
	* @param pForceArray if set to true, when an array is created for each value
	* @param pFirst2Cols if set to true, only returns the first two columns
	* @return the associative array, or false if an error occurs
	* @todo not currently used anywhere
	*/

	function getCol( $pQuery, $pValues=array(), $pTrim=FALSE )
	{
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery($pQuery);
		$execFunction = ( !defined( 'IS_LIVE' ) || $pCacheTime == BIT_QUERY_DEFAULT ? 'GetAssoc' : 'CacheGetAssoc' );
		$result = $this->mDb->getCol( $pQuery, 0, $pValues );
		//count the number of queries made
		$this->queryComplete();
		return $result;
	}

	/** Returns an associative array for the given query.
	* See AdoDB GetAssoc() function for more detail.
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pValues an array of values used in a parameterised query
	* @param pForceArray if set to true, when an array is created for each value
	* @param pFirst2Cols if set to true, only returns the first two columns
	* @return the associative array, or false if an error occurs
	*/
	function getArray( $pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT )
	{
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery($pQuery);
		$execFunction = ( !defined( 'IS_LIVE' ) || $pCacheTime == BIT_QUERY_DEFAULT ? 'GetArray' : 'CacheGetArray' );
		$result = $this->mDb->GetArray( $pQuery, $pValues, $pForceArray, $pFirst2Cols );
		$this->queryComplete();
		return $result;
	}

	function getAll( $pQuery, $pValues=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery($pQuery);
		$result = $this->mDb->GetAll( $pQuery, $pValues );
		$this->queryComplete();
		return $result;
	}

	/** Returns an associative array for the given query.
	* See AdoDB GetAssoc() function for more detail.
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pValues an array of values used in a parameterised query
	* @param pForceArray if set to true, when an array is created for each value
	* @param pFirst2Cols if set to true, only returns the first two columns
	* @return the associative array, or false if an error occurs
	*/
	function getAssoc( $pQuery, $pValues=array(), $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT )
	{
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery($pQuery);
		$result = $this->mDb->getAssoc( $pQuery, $pForceArray, $pValues, $pFirst2Cols );
		$this->queryComplete();
		return $result;
	}

	/** Executes the SQL and returns the first row as an array. The recordset and remaining rows are discarded for you automatically. If an error occurs, false is returned.
	* See AdoDB GetRow() function for more detail.
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pValues an array of values used in a parameterised query
	* @return returns the first row as an array, or false if an error occurs
	*/
	function getRow( $pQuery, $pValues=FALSE, $pCacheTime=BIT_QUERY_DEFAULT )
	{
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery($pQuery);
		$result = $this->mDb->GetRow( $pQuery, $pValues );
		$this->queryComplete();
		return $result;
	}

	/** Returns a single column value from the database.
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pValues an array of values used in a parameterised query
	* @param pReportErrors report errors to STDOUT
	* @param pOffset the row number to begin returning rows from.
	* @return the associative array, or false if an error occurs
	*/
	function getOne($pQuery, $pValues=NULL, $pNumRows=NULL, $pOffset=NULL, $pCacheTime = BIT_QUERY_DEFAULT )
	{
		$value = NULL;
		if( $result = $this->query($pQuery, $pValues, 1, 0, $pCacheTime ) ) {
			if( $res = $result->fetchRow() ) {
				reset( $res );
				$value = current($res);
			}
		}
		return $value;
	}

	/**
	* A database portable Sequence management function.
	*
	* @param pSequenceName Name of the sequence to be used
	*		It will be created if it does not already exist
	* @return		0 if not supported, otherwise a sequence id
	*/
	function GenID( $pSequenceName, $pUseDbPrefix = true ) {
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		// Pear appends _seq just to be a pain
		if ($pUseDbPrefix) {
			$prefix = str_replace("`","",BIT_DB_PREFIX);
		}
		else {
			$prefix = '';
		}
		$seqName  = str_replace( '_seq', '', $prefix.$pSequenceName );
		return $this->mDb->nextId( $seqName );
	}

	/**
	* A database portable Sequence management function.
	*
	* @param pSequenceName Name of the sequence to be used
	*		It will be created if it does not already exist
	* @param pStartID Allows setting the initial value of the sequence
	* @return		0 if not supported, otherwise a sequence id
	* @todo	To be combined with GenID
	*/
	function CreateSequence($seqname='adodbseq',$startID=1)
	{
		if (empty($this->mDb->_genSeqSQL)) return FALSE;
		return $this->mDb->CreateSequence($seqname, $startID);
	}

	/**
	* A database portable IFNULL function.
	*
	* @param pField argument to compare to NULL
	* @param pNullRepl the NULL replacement value
	* @return a string that represents the function that checks whether
	* $pField is NULL for the given database, and if NULL, change the
	* value returned to $pNullRepl.
	*/
	function ifNull($pField, $pNullRepl)
	{
		return $this->mDb->ifNull($pField, $pNullRepl);
	}

	/** Format the timestamp in the format the database accepts.
	* @param pDate a Unix integer timestamp or an ISO format Y-m-d H:i:s
	* @return the timestamp as a quoted string.
	* @todo could be used to later convert all int timestamps into db
	* timestamps. Currently not used anywhere.
	*/
	function ls($pDate)
	{
		// not sure what this did - maybe someone can comment why its here
		//return preg_replace("/'/","", $this->mDb->DBTimeStamp($pDate));
		return $this->mDb->DBTimeStamp($pDate);
	}

	/**
	 * Format date column in sql string given an input format that understands Y M D
	 */
	function SQLDate($pDateFormat, $pBaseDate=false) {
		return $this->mDb->SQLDate($pDateFormat, $pBaseDate) ;
	}

	/**
	 * Calculate the offset of a date for a particular database and generate
	 * appropriate SQL. Useful for calculating future/past dates and storing
	 * in a database.
	 * @param pDays Number of days to offset by
	 *		If dayFraction=1.5 means 1.5 days from now, 1.0/24 for 1 hour.
	 * @param pColumn Value to be offset
	 *		If NULL an offset from the current time is supplied
	 * @return New number of days
	 *
	 * @todo Not currently used - this is database specific and uses TIMESTAMP
	 * rather than unix seconds
	 */
	function OffsetDate( $pDays, $pColumn=NULL ) {
		return $this->mDb->OffsetDate( $pDays, $pColumn );
	}

	/**
	 *	Improved method of initiating a transaction. Used together with CompleteTrans().
	 *	Advantages include:
	 *
	 *	a. StartTrans/CompleteTrans is nestable, unlike BeginTrans/CommitTrans/RollbackTrans.
	 *	   Only the outermost block is treated as a transaction.<br>
	 *	b. CompleteTrans auto-detects SQL errors, and will rollback on errors, commit otherwise.<br>
	 *	c. All BeginTrans/CommitTrans/RollbackTrans inside a StartTrans/CompleteTrans block
	 *	   are disabled, making it backward compatible.
	 */
	function StartTrans() {
		 return( $this->mDb->autoCommit( FALSE ) == DB_OK);
	}

	/**
	 *	Used together with StartTrans() to end a transaction. Monitors connection
	 *	for sql errors, and will commit or rollback as appropriate.
	 *
	 *	autoComplete if true, monitor sql errors and commit and rollback as appropriate,
	 *	and if set to false force rollback even if no SQL error detected.
	 *	@returns true on commit, false on rollback.
	 */
	function CompleteTrans() {
		$ret = $this->mDb->commit( FALSE ) == DB_OK;
		$this->mDb->autoCommit( TRUE );
		return( $ret );
	}

	/**
	 * If database does not support transactions, rollbacks always fail, so return false
	 * otherwise returns true if the Rollback was successful
	 *
	 * @return true/false.
	 */
	function RollbackTrans() {
		$ret = $this->mDb->rollback( FALSE ) == DB_OK;
		$this->mDb->autoCommit( TRUE );
		return( $ret );
	}

	/**
	 * Create a list of tables available in the current database
	 *
	 * @param ttype can either be 'VIEW' or 'TABLE' or false.
	 * 		If false, both views and tables are returned.
	 *		"VIEW" returns only views
	 *		"TABLE" returns only tables
	 * @param showSchema returns the schema/user with the table name, eg. USER.TABLE
	 * @param mask  is the input mask - only supported by oci8 and postgresql
	 *
	 * @return  array of tables for current database.
	 */
	function MetaTables( $ttype = false, $showSchema = false, $mask=false ) {
		error_log( '$gForceAdodb = TRUE; is needed on the page: '.$_SERVER['SCRIPT_FILENAME'] );
		return array();
	}

	/**
	* @return # rows affected by UPDATE/DELETE
	*/
	function Affected_Rows() {
		return $this->mDb->Affected_Rows();
	}
}

// This function will handle all errors
function bit_pear_error_handler( $error_obj ) {
	$bindVars = !empty( $error_obj->backtrace[0]['object']->backtrace[2]['object']->_data ) ? $error_obj->backtrace[0]['object']->backtrace[2]['object']->_data : NULL;
	$dbParams = array(
		'errno' => $error_obj->getCode(),
		'db_msg'=> $error_obj->getMessage(),
		'sql'=> $error_obj->getDebugInfo()." ('".implode( "','", $bindVars )."')",
	);

	$logString = bit_error_string( $dbParams );
	bit_display_error( $logString, $dbParams['db_msg'] );
}

function bit_pear_login_error( $pErrorObj ) {
	return $pErrorObj->getDebugInfo();
}

?>
