<?php
/**
 * ADOdb Library interface Class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitDbAdodb.php,v 1.29 2008/11/18 22:34:44 pppspoonman Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * @author spider <spider@steelsun.com>
 */

/**
 * This code must execute before adodb/adodb.inc.php runs
 * Otherwsie $ADODB_CACHE_DIR ends up being set to '/tmp'
 */
global $ADODB_CACHE_DIR;
if( empty( $ADODB_CACHE_DIR )) {
	$ADODB_CACHE_DIR = get_temp_dir().'/adodb/'.$_SERVER['HTTP_HOST'].'/';
}
mkdir_p( $ADODB_CACHE_DIR );

if( file_exists( UTIL_PKG_PATH.'adodb/adodb.inc.php' )) {
	// this is the adodb that is distributed with bitweaver
	$adodbIncFile = UTIL_PKG_PATH.'adodb/adodb.inc.php';
} else {
	// assume it is in php's global include_path
	$adodbIncFile = 'adodb.inc.php';
}

require_once( $adodbIncFile );
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
class BitDbAdodb extends BitDb {
	function BitDbAdodb( $pConnectionHash = NULL ) {
		global $ADODB_FETCH_MODE;
		if( is_null( $pConnectionHash ) ) {
			global $gBitDbType, $gBitDbHost, $gBitDbUser, $gBitDbPassword, $gBitDbName;
			$pConnectionHash['db_type'] = $gBitDbType;
			$pConnectionHash['db_host'] = $gBitDbHost;
			$pConnectionHash['db_user'] = $gBitDbUser;
			$pConnectionHash['db_password'] = $gBitDbPassword;
			$pConnectionHash['db_name'] = $gBitDbName;
		}

		parent::BitDb();

		// Get all the ADODB stuff included
		if( !defined( "ADODB_ASSOC_CASE" )) {
			define( "ADODB_ASSOC_CASE", 0 );
		}
		if( !defined( "ADODB_FETCH_MODE" )) {
			define( "ADODB_FETCH_MODE", ADODB_ASSOC_CASE );
		}
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

		if( !empty( $pConnectionHash['db_name'] ) && !empty( $pConnectionHash['db_type'] ) ) {
			if( $pConnectionHash['db_type'] == 'oci8' ) {
				$pConnectionHash['db_type'] = 'oci8po';
			}
			$this->mType = $pConnectionHash['db_type'];
			$this->mName = $pConnectionHash['db_name'];
			if( !isset( $this->mName )) {
				die( "No database name specified" );
			}
			$this->preDBConnection();
			$this->mDb = ADONewConnection( $pConnectionHash['db_type'] );
			$this->mDb->Connect( $pConnectionHash['db_host'], $pConnectionHash['db_user'], $pConnectionHash['db_password'], $pConnectionHash['db_name'] );

			if( !$this->mDb ) {
				die( "Unable to login to the database $pConnectionHash[db_type] on $pConnectionHash[db_host] as `user` $pConnectionHash[db_user]<p>".$this->mDb->ErrorMsg() );
			}
			$this->postDBConnection();
			unset( $pDSN );
			if( defined( "DB_PERFORMANCE_STATS" ) && constant( "DB_PERFORMANCE_STATS" )) {
				$this->mDb->LogSQL();
			}
		}

		$this->debug( $this->getDebugLevel() );
	}

	/**
	 * Used to create tables - most commonly from package/schema_inc.php files
	 * @todo remove references to BIT_DB_PREFIX, us a member function
	 * @param pTables an array of tables and creation information in DataDict
	 * style
	 * @param pOptions an array of options used while creating the tables
	 * @return TRUE|FALSE
	 * TRUE if created with no errors | FALSE if errors are stored in $this->mFailed
	 */
	function createTables( $pTables, $pOptions = array() ) {
		// If server support InnoDB for MySql set the selected engine
		if( isset( $_SESSION['use_innodb'] )) {
			if( $_SESSION['use_innodb'] == TRUE ) {
				$pOptions = array_merge( $pOptions, array( 'MYSQL' => 'ENGINE=INNODB' ));
			} else {
				$pOptions = array_merge( $pOptions, array( 'MYSQL' => 'ENGINE=MYISAM' ));
			}
		}
		$dict = NewDataDictionary( $this->mDb );
		$this->mFailed = array();
		$result = TRUE;
		foreach( array_keys( $pTables ) AS $tableName ) {
			$completeTableName = ( defined( "BIT_DB_PREFIX" )) ? BIT_DB_PREFIX.$tableName : $tableName;
			$sql = $dict->CreateTableSQL($completeTableName, $pTables[$tableName], $pOptions);
			if( $sql && ( $dict->ExecuteSQLArray( $sql ) > 0 )) {
				// Success
			} else {
				// Failure
				$result = FALSE;
				array_push( $this->mFailed, $sql.": ".$this->mDb->ErrorMsg() );
			}
		}
		return $result;
	}

	/**
	 * Used to check if tables already exists.
	 * @todo should be used to confirm tables are already created
	 * @param pTable the table name
	 * @return TRUE if table already exists
	 */
	function tableExists( $pTable ) {
		$dict = NewDataDictionary( $this->mDb );
		$pTable = preg_replace( "/`/", "", $pTable );
		$tables = $dict->MetaTables( FALSE, FALSE, $pTable );
		return array_search( $pTable, $tables ) !== FALSE;
	}

	/**
	 * Used to drop tables
	 * @todo remove references to BIT_DB_PREFIX, us a member function
	 * @param pTables an array of table names to drop
	 * @return TRUE | FALSE
	 * TRUE if dropped with no errors |
	 * FALSE if errors are stored in $this->mFailed
	 */
	function dropTables( $pTables ) {
		$dict = NewDataDictionary( $this->mDb );
		$this->mFailed = array();
		$return = TRUE;
		foreach( $pTables AS $tableName ) {
			$completeTableName = ( defined( "BIT_DB_PREFIX" )) ? BIT_DB_PREFIX.$tableName : $tableName;
			$sql = $dict->DropTableSQL( $completeTableName );
			if( $sql && ( $dict->ExecuteSQLArray( $sql ) > 0 )) {
				//echo "Success<br>";
			} else {
				//echo "Failure<br>";
				$return = FALSE;
				array_push($this->mFailed, $sql);
			}
		}
		return $return;
	}

	/**
	 * Quotes a string to be sent to the database which is
	 * passed to function on to AdoDB->qstr().
	 * @todo not sure what its supposed to do
	 * @param pStr string to be quotes
	 * @return quoted string using AdoDB->qstr()
	 */
	function qstr( $pStr ) {
		return $this->mDb->qstr( $pStr );
	}
	
	/**
	 * Returns SUBSTRING function appropiate for database.
	 * @return string using AdoDB->substr property
	 */
	function substr() {
		return $this->mDb->substr;
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
	function queryError( $pQuery, &$pError, $pValues = NULL, $pNumRows = -1, $pOffset = -1 ) {
		$this->convertQuery( $pQuery );
		if( $pNumRows == -1 && $pOffset == -1 ) {
			$result = $this->mDb->Execute($pQuery, $pValues);
		} else {
			$result = $this->mDb->SelectLimit($pQuery, $pNumRows, $pOffset, $pValues);
		}

		if( !$result ) {
			$pError = $this->mDb->ErrorMsg();
			$result=FALSE;
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
	function query( $query, $values = FALSE, $numrows = BIT_QUERY_DEFAULT, $offset = BIT_QUERY_DEFAULT, $pCacheTime=BIT_QUERY_DEFAULT ) {
		$this->convertQuery( $query );
		if( empty( $this->mDb )) {
			return FALSE;
		}

		$this->queryStart();

		if( !is_numeric( $numrows )) {
			$numrows = BIT_QUERY_DEFAULT;
		}

		if( !is_numeric( $offset )) {
			$offset = BIT_QUERY_DEFAULT;
		}

		if( $numrows == BIT_QUERY_DEFAULT && $offset == BIT_QUERY_DEFAULT ) {
			if( !$this->isCachingActive() || $pCacheTime == BIT_QUERY_DEFAULT ) {
				$result = $this->mDb->Execute( $query, $values );
			} else {
				$result = $this->mDb->CacheExecute( $pCacheTime, $query, $values );
			}
		} else {
			if( !$this->isCachingActive() || $pCacheTime == BIT_QUERY_DEFAULT ) {
				$result = $this->mDb->SelectLimit( $query, $numrows, $offset, $values );
			} else {
				$result = $this->mDb->CacheSelectLimit( $pCacheTime, $query, $numrows, $offset, $values );
			}
		}

		$this->queryComplete();
		return $result;
	}

	/**
	 * List columns in a database as an array of ADOFieldObjects.
	 * See top of file for definition of object.
	 *
	 * @param table	table name to query
	 * @param upper	uppercase table name (required by some databases)
	 * @param schema is optional database schema to use - not supported by all databases.
	 *
	 * @return  array of ADOFieldObjects for current table.
	 */
	function MetaColumns( $table,$normalize=TRUE, $schema=FALSE ) {
		return $this->mDb->MetaColumns( $table, $normalize, $schema );
	}

	/**
	 * List indexes in a database as an array of ADOFieldObjects.
	 * See top of file for definition of object.
	 *
	 * @param table	table name to query
	 * @param primary list primary indexes
	 * @param owner list owner of index
	 *
	 * @return  array of ADOFieldObjects for current table.
	 */
	function MetaIndexes( $table,$primary=FALSE, $owner=FALSE ) {
		return $this->mDb->MetaIndexes( $table, $primary, $owner );
	}

	/**
	 * getAll 
	 * 
	 * @param string $pQuery 
	 * @param array $pValues 
	 * @param numeric $pCacheTime 
	 * @access public
	 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
	 */
	function getAll( $pQuery, $pValues=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		if( empty( $this->mDb )) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery( $pQuery );
		if( !$this->isCachingActive() || $pCacheTime == BIT_QUERY_DEFAULT ) {
			$result = $this->mDb->getAll( $pQuery, $pValues );
		} else {
			$result = $this->mDb->CacheGetAll($pCacheTime, $pQuery, $pValues );
		}
		//count the number of queries made
		$this->queryComplete();
		return $result;
	}

	/** Executes the SQL and returns all elements of the first column as a 1-dimensional array. The recordset is discarded for you automatically. If an error occurs, FALSE is returned.
	 * See AdoDB GetCol() function for more detail.
	 * @param pQuery the SQL query. Use backticks (`) to quote all table
	 * and attribute names for AdoDB to quote appropriately.
	 * @param pValues an array of values used in a parameterised query
	 * @param pForceArray if set to TRUE, when an array is created for each value
	 * @param pFirst2Cols if set to TRUE, only returns the first two columns
	 * @return the associative array, or FALSE if an error occurs
	 * @todo not currently used anywhere
	 */
	function getCol( $pQuery, $pValues=FALSE, $pTrim=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		if( empty( $this->mDb )) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery( $pQuery );
		if( !$this->isCachingActive() || $pCacheTime == BIT_QUERY_DEFAULT ) {
			$result = $this->mDb->getCol( $pQuery, $pValues, $pTrim );
		} else {
			$result = $this->mDb->CacheGetCol( $pCacheTime, $pQuery, $pValues, $pTrim );
		}
		//count the number of queries made
		$this->queryComplete();
		return $result;
	}

	/** Returns an associative array for the given query.
	 * See AdoDB GetAssoc() function for more detail.
	 * @param pQuery the SQL query. Use backticks (`) to quote all table
	 * and attribute names for AdoDB to quote appropriately.
	 * @param pValues an array of values used in a parameterised query
	 * @param pForceArray if set to TRUE, when an array is created for each value
	 * @param pFirst2Cols if set to TRUE, only returns the first two columns
	 * @return the associative array, or FALSE if an error occurs
	 */
	function getArray( $pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		if( empty( $this->mDb )) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery( $pQuery );
		if( !$this->isCachingActive() || $pCacheTime == BIT_QUERY_DEFAULT ) {
			$result = $this->mDb->GetArray( $pQuery, $pValues, $pForceArray, $pFirst2Cols );
		} else {
			$result = $this->mDb->CacheGetArray( $pCacheTime, $pQuery, $pValues, $pForceArray, $pFirst2Cols );
		}
		$this->queryComplete();
		return $result;
	}

	/** Returns an associative array for the given query.
	 * See AdoDB GetAssoc() function for more detail.
	 * @param pQuery the SQL query. Use backticks (`) to quote all table
	 * and attribute names for AdoDB to quote appropriately.
	 * @param pValues an array of values used in a parameterised query
	 * @param pForceArray if set to TRUE, when an array is created for each value
	 * @param pFirst2Cols if set to TRUE, only returns the first two columns
	 * @return the associative array, or FALSE if an error occurs
	 */
	function getAssoc( $pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		if( empty( $this->mDb )) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery( $pQuery );
		if( !$this->isCachingActive() || $pCacheTime == BIT_QUERY_DEFAULT ) {
			$result = $this->mDb->GetAssoc( $pQuery, $pValues, $pForceArray, $pFirst2Cols );
		} else {
			$result = $this->mDb->CacheGetAssoc( $pCacheTime, $pQuery, $pValues, $pForceArray, $pFirst2Cols );
		}
		$this->queryComplete();
		return $result;
	}

	/** Executes the SQL and returns the first row as an array. The recordset and remaining rows are discarded for you automatically. If an error occurs, FALSE is returned.
	 * See AdoDB GetRow() function for more detail.
	 * @param pQuery the SQL query. Use backticks (`) to quote all table
	 * and attribute names for AdoDB to quote appropriately.
	 * @param pValues an array of values used in a parameterised query
	 * @return returns the first row as an array, or FALSE if an error occurs
	 */
	function getRow( $pQuery, $pValues=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery($pQuery);
		if( !$this->isCachingActive() || $pCacheTime == BIT_QUERY_DEFAULT ) {
			$result = $this->mDb->GetRow( $pQuery, $pValues );
		} else {
			$result = $this->mDb->CacheGetRow( $pCacheTime, $pQuery, $pValues );
		}
		$this->queryComplete();
		return $result;
	}

	/** Returns a single column value from the database.
	 * @param pQuery the SQL query. Use backticks (`) to quote all table
	 * and attribute names for AdoDB to quote appropriately.
	 * @param pValues an array of values used in a parameterised query
	 * @param pReportErrors report errors to STDOUT
	 * @param pOffset the row number to begin returning rows from.
	 * @return the associative array, or FALSE if an error occurs
	 */
	function getOne( $pQuery, $pValues=NULL, $pNumRows=NULL, $pOffset=NULL, $pCacheTime = BIT_QUERY_DEFAULT ) {
		$result = $this->query($pQuery, $pValues, 1, $pOffset, $pCacheTime );
		$res = ( $result != NULL ) ? $result->fetchRow() : FALSE;
		if( $res === FALSE ) {
			//simulate pears behaviour
			return NULL;
		}
		//$this->debugger_log($pQuery, $pValues);
		list( $key, $value ) = each($res);
		return $value;
	}

	/**
	 * A database portable Sequence management function.
	 *
	 * @param pSequenceName Name of the sequence to be used
	 *		It will be created if it does not already exist
	 * @return		0 if not supported, otherwise a sequence id
	 */
	function GenID( $pSequenceName, $pUseDbPrefix = TRUE ) {
		if( empty( $this->mDb )) {
			return FALSE;
		}
		if( $pUseDbPrefix ) {
			$prefix = str_replace( "`", "", BIT_DB_PREFIX );
		} else {
			$prefix = '';
		}
		return $this->mDb->GenID( $prefix.$pSequenceName );
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
	function CreateSequence( $pSeqname='adodbseq',$startID=1 ) {
		if( empty( $this->mDb->_genSeqSQL )) {
			return FALSE;
		}
		return $this->mDb->CreateSequence( $pSeqname, $startID );
	}

	/**
	 * A database portable Sequence management function.
	 *
	 * @param pSequenceName Name of the sequence to be dropped
	 *
	 * @return	FALSE if not supported
	 */
	function DropSequence( $pSeqname='adodbseq' ) {
		if( empty( $this->mDb->_dropSeqSQL )) {
			return FALSE;
		}
		return $this->mDb->DropSequence( $pSeqname );
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
	function ifNull( $pField, $pNullRepl ) {
		return $this->mDb->ifNull( $pField, $pNullRepl );
	}

	/** Format the timestamp in the format the database accepts.
	 * @param pDate a Unix integer timestamp or an ISO format Y-m-d H:i:s
	 * @return the timestamp as a quoted string.
	 * @todo could be used to later convert all int timestamps into db
	 * timestamps. Currently not used anywhere.
	 */
	function ls( $pDate ) {
		// not sure what this did - maybe someone can comment why its here
		//return preg_replace("/'/","", $this->mDb->DBTimeStamp($pDate));
		return $this->mDb->DBTimeStamp($pDate);
	}

	/**
	 * Format date column in sql string given an input format that understands Y M D
	 */
	function SQLDate( $pDateFormat, $pBaseDate=FALSE ) {
		return $this->mDb->SQLDate( $pDateFormat, $pBaseDate );
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

	/** Converts backtick (`) quotes to the appropriate quote for the
	 * database.
	 * @private
	 * @param pQuery the SQL query using backticks (`)
	 * @return the correctly quoted SQL statement
	 * @todo investigate replacement by AdoDB NameQuote() function
	 */
	function convertQuery( &$pQuery ) {
		if( !empty( $this->mType )) {
			switch( $this->mType ) {
			case "oci8":
				// convert bind variables - adodb does not do that
				$qe = explode( "?", $pQuery );
				$pQuery = "";
				for( $i = 0; $i < sizeof($qe) - 1; $i++ ) {
					$pQuery .= $qe[$i] . ":" . $i;
				}
				$pQuery .= $qe[$i];
			default:
				parent::convertQuery( $pQuery );
				break;
			}
		}
	}

	/** will activate ADODB's native debugging output
	 * @param pLevel debugging level - FALSE is off, TRUE is on, 99 is verbose
	 **/
	function debug( $pLevel=99 ) {
		if( is_object( $this->mDb )) {
			$this->mDb->debug = $pLevel;
		}
	}

	/** returns the level of query debugging output
	 * @return pLevel debugging level - FALSE is off, TRUE is on, 99 is verbose
	 **/
	function getDebugLevel() {
		return( $this->mDebug );
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
		return $this->mDb->StartTrans();
	}

	/**
	 *	Used together with StartTrans() to end a transaction. Monitors connection
	 *	for sql errors, and will commit or rollback as appropriate.
	 *
	 *	autoComplete if TRUE, monitor sql errors and commit and rollback as appropriate,
	 *	and if set to FALSE force rollback even if no SQL error detected.
	 *	@returns TRUE on commit, FALSE on rollback.
	 */
	function CompleteTrans() {
		return $this->mDb->CompleteTrans();
	}

	/**
	 * If database does not support transactions, rollbacks always fail, so return FALSE
	 * otherwise returns TRUE if the Rollback was successful
	 *
	 * @return TRUE/FALSE.
	 */
	function RollbackTrans() {
		$this->mDb->FailTrans();
		return $this->mDb->CompleteTrans( FALSE );
	}

	/**
	 * Create a list of tables available in the current database
	 *
	 * @param ttype can either be 'VIEW' or 'TABLE' or FALSE.
	 * 		If FALSE, both views and tables are returned.
	 *		"VIEW" returns only views
	 *		"TABLE" returns only tables
	 * @param showSchema returns the schema/user with the table name, eg. USER.TABLE
	 * @param mask  is the input mask - only supported by oci8 and postgresql
	 *
	 * @return  array of tables for current database.
	 */
	function MetaTables( $ttype = FALSE, $showSchema = FALSE, $mask=FALSE ) {
		return $this->mDb->MetaTables( $ttype, $showSchema, $mask );
	}

	/**
	 * @return # rows affected by UPDATE/DELETE
	 */
	function Affected_Rows() {
		return $this->mDb->Affected_Rows();
	}
}

/*
 * Custom ADODB Error Handler. This will be called with the following params
 *
 * @param $dbms		the RDBMS you are connecting to
 * @param $fn		the name of the calling function (in uppercase)
 * @param $errno		the native error number from the database
 * @param $errmsg	the native error msg from the database
 * @param $p1		$fn specific parameter - see below
 * @param $P2		$fn specific parameter - see below
 */
function bit_error_handler( $dbms, $fn, $errno, $errmsg, $p1, $p2, &$thisConnection ) {
	global $gBitDb;
	if( ini_get( 'error_reporting' ) == 0 ) {
		return; // obey @ protocol
	}

	$dbParams = array(
		'db_type'=>$dbms,
		'call_func'=>$fn,
		'errno'=>$errno,
		'db_msg'=>$errmsg,
		'sql'=>$p1,
		'p2'=>$p2
	);
	$logString = bit_error_string( $dbParams );

	/*
	 * Log connection error somewhere
	 *	0 message is sent to PHP's system logger, using the Operating System's system
	 *		logging mechanism or a file, depending on what the error_log configuration
	 *		directive is set to.
	 *	1 message is sent by email to the address in the destination parameter.
	 *		This is the only message type where the fourth parameter, extra_headers is used.
	 *		This message type uses the same internal function as mail() does.
	 *	2 message is sent through the PHP debugging connection.
	 *		This option is only available if remote debugging has been enabled.
	 *		In this case, the destination parameter specifies the host name or IP address
	 *		and optionally, port number, of the socket receiving the debug information.
	 *	3 message is appended to the file destination
	 */
	error_log( $logString,0 );
	$subject = isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : 'BITWEAVER';

	$fatal = FALSE;
	if(( $fn == 'EXECUTE' ) && ( $thisConnection->MetaError() != -5 ) && (empty( $gBitDb ) || $gBitDb->isFatalActive()) ) {
		$fatal = TRUE;
	}

	bit_display_error( $logString, $dbParams['db_msg'], $fatal );
}
?>
