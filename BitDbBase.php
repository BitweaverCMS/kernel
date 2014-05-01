<?php
/**
 * ADOdb Library interface Class
 *
 * @package kernel
 * @version $Header$
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @author spider <spider@steelsun.com>
 */

/**
 * ensure your AdoDB install is a subdirectory off your include path
 */

define( 'BIT_QUERY_DEFAULT', -1 ); // deprecated constant for no cache time
define( 'BIT_QUERY_CACHE_DISABLE', -1 );
define( 'BIT_MAX_RECORDS', -1 );

// num queries has to be global
global $gNumQueries;
$gNumQueries = 0;


/**
 * This class is used for database access and provides a number of functions to help
 * with database portability.
 *
 * Currently used as a base class, this class should be optional to ensure bitweaver
 * continues to function correctly, without a valid database connection.
 *
 * @package kernel
 */
class BitDb {
	/**
	* Used to store the ADODB db object used to access the database.
	* This is just a pointer to a single global variable used by all classes.
	* This limits database connections to just one per request.
	* @private
	*/
	var $mDb;
	/**
	* Used to identify the ADODB db object
	* @private
	*/
	var $mName;
	/**
	* Used to store the ADODB db object type
	* @private
	*/
	var $mType;
	/**
	* Used to store failed commands
	* @private
	*/
	var $mFailed = array();
	/**
	* Used to store the number of queries executed.
	* @private
	*/
	var $mNumQueries = 0;
	/**
	* Used to store the total query time for this request.
	* @private
	*/
	var $mQueryTime = 0;
	/**
	* Case sensitivity flag used in convertQuery
	* @private
	*/
	var $mCaseSensitive = TRUE;
	/**
	* Used to enable AdoDB caching
	* @private
	*/
	var $mCacheFlag;
	/**
	* Used to determine SQL debug output. BitDbAdodb overrides associated methods to use the debugging mechanisms built into ADODB
	* @private
	*/
	var $mDebug;
	/**
	* Determines if fatal query functions should terminate script execution. Defaults to TRUE. Can be deactived for things like expected duplicate inserts
	* @private
	*/
	var $mFatalActive;
	/**
	* During initialisation, database parameters are passed to the class.
	* If these parameters are not valid, class will not be initialised.
	*/
	function BitDb() {
		global $gDebug;
		$this->mDebug = $gDebug;
		$this->mCacheFlag = TRUE;
		$this->mNumQueries = 0;
		$this->mQueryTime = 0;
		$this->setFatalActive();
		global $gBitDbCaseSensitivity;
		$this->setCaseSensitivity( $gBitDbCaseSensitivity );
	}
	/**
	* This function contains any pre-connection work
	* @private
	* @todo investigate if this is the correct way to do it.
	*/
	function preDBConnection() {
		// Pre connection setup
		if(isset($this->mType)) {
			// we have a db we're gonna try to load
			switch ($this->mType) {
				case "sybase":
				// avoid database change messages
				ini_set("sybct.min_server_severity", "11");
				break;
			}
		} else {
			die("No database type specified");
		}
	}
	/**
	* This function contains any post-connection work
	* @private
	* @todo investigate if this is the correct way to do it.
	* @todo remove the BIT_DB_PREFIX, change to a member variable
	* @todo get spiderr to explain the schema line
	*/
	function postDBConnection() {
		// Post connection setup
		switch ($this->mType) {
			case "sybase":
			case "mssql":
				$this->mDb->Execute("set quoted_identifier on");
				break;
			case "mysql":
				$version = $this->getDatabaseVersion();
				if( ($version['major'] >= 4 && $version['minor'] >=1) || ($version['major'] >= 5) ) {
					$this->mDb->Execute("set session sql_mode='PIPES_AS_CONCAT'");
				}
				break;
			case "postgres":
				// Do a little prep work for postgres, no break, cause we want default case too
				if (defined("BIT_DB_PREFIX") && preg_match( "/\./", BIT_DB_PREFIX) ) {
					$schema = preg_replace("/[`\.]/", "", BIT_DB_PREFIX);
					// Assume we want to dump in a schema, so set the search path and nuke the prefix here.
					// $result = $this->mDb->Execute( "SET search_path TO $schema,public" );
				}
				break;
		}
	}

	/**
	* Determines if the database connection is valid
	* @return true if DB connection is valid, false if not
	*/
	function isValid() {
		return( !empty( $this->mDb ) );
	}
	/**
	* Determines if the database connection is valid
	* @return true if DB connection is valid, false if not
	*/
	function isFatalActive() {
		return( $this->mFatalActive );
	}
	/**
	* Determines if the database connection is valid
	* @return true if DB connection is valid, false if not
	*/
	function setFatalActive( $pActive=TRUE ) {
		$this->mFatalActive = $pActive;
	}
	/**
	* Used to start query timer if in debug mode
	*/
	function queryStart() {
		global $gBitTimer;
		$this->mQueryLap = $gBitTimer->elapsed();
	}
	/** will activate ADODB like native debugging output
	* @param pLevel debugging level - FALSE is off, TRUE is on, 99 is verbose
	**/
	function debug( $pLevel=99 ) {
		$this->mDebug = $pLevel;
	}

	/** returns the level of query debugging output
	* @return pLevel debugging level - FALSE is off, TRUE is on, 99 is verbose
	**/
	function getDebugLevel() {
		return( $this->mDebug );
	}
	/**
	* Sets the case sensitivity mode which is used in convertQuery
	* @return true if DB connection is valid, false if not
	*/
	function setCaseSensitivity( $pSensitivity=TRUE ) {
		$this->mCaseSensitive = $pSensitivity;
	}
	/**
	* Sets the case sensitivity mode which is used in convertQuery
	* @return true if DB connection is valid, false if not
	*/
	function getCaseSensitivity( $pSensitivity=TRUE ) {
		switch ($this->mType) {
			case "firebird":
			case "oci8":
			case "oci8po":
				// Force Oracle to always be insensitive
				$ret = FALSE;
				break;
			default:
				$ret = $this->mCaseSensitive;
				break;
		}

		return( $ret );
	}
	/**
	* Used to stop query tracking and output results if in debug mode
	*/
	function queryComplete() {
		global $gNumQueries;
		//count the number of queries made
		$gNumQueries++;
		$this->mNumQueries++;
		global $gBitTimer;
		$interval = $gBitTimer->elapsed() - $this->mQueryLap;
		$this->mQueryTime += $interval;
		if( $this->getDebugLevel() ) {
			$style = ( $interval > .5 ) ? 'color:red;' : (( $interval > .15 ) ? 'color:orange;' : '');
			$querySpeed = ( $interval > .5 ) ? tra( 'VERY SLOW' ): (( $interval > .15 ) ? tra( 'SLOW' ) : '');
			print '<p style="'.$style.'">### Query '.$querySpeed.': '.$gNumQueries.' Start time: '.$this->mQueryLap.' ### Query run time: '.$interval.'</p>';
			flush();
		}
		$this->mQueryLap = 0;
	}

	/**
	* Used to create tables - most commonly from package/schema_inc.php files
	* @todo remove references to BIT_DB_PREFIX, us a member function
	* @param pTables an array of tables and creation information in DataDict
	* style
	* @param pOptions an array of options used while creating the tables
	* @return true|false
	* true if created with no errors | false if errors are stored in $this->mFailed
	*/
	function createTables($pTables, $pOptions = array()) {
		// PURE VIRTUAL
	}

	/**
	* Used to check if tables already exists.
	* @todo should be used to confirm tables are already created
	* @param pTable the table name
	* @return true if table already exists
	*/
	function tableExists($pTable) {
		// PURE VIRTUAL
	}

	/**
	* Used to drop tables
	* @todo remove references to BIT_DB_PREFIX, us a member function
	* @param pTables an array of table names to drop
	* @return true | false
	* true if dropped with no errors |
	* false if errors are stored in $this->mFailed
	*/
	function dropTables($pTables) {
		// PURE VIRTUAL
	}

	/**
	* Function to set ADODB query caching member variable
	* @param pCacheExecute flag to enable or disable ADODB query caching
	* @return nothing
	*/
	function setCaching( $pCacheFlag=TRUE ) {
		$this->mCacheFlag = $pCacheFlag;
	}

	/**
	* Function to set ADODB query caching member variable
	* @param pCacheExecute flag to enable or disable ADODB query caching
	* @return nothing
	*/
	function isCachingActive() {
		return( $this->mCacheFlag );
	}

	/**
	* Quotes a string to be sent to the database
	* @param pStr string to be quotes
	* @return quoted string using AdoDB->qstr()
	*/
	function qstr($pStr) {
		// PURE VIRTUAL
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
		// PURE VIRTUAL
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
	function query($query, $values = null, $numrows = BIT_QUERY_DEFAULT, $offset = BIT_QUERY_DEFAULT, $pCacheTime=BIT_QUERY_DEFAULT ) {
		// PURE VIRTUAL
	}

	/**
	* ADODB compatibility functions for bitcommerce
	*/
	function Execute($pQuery, $pNumRows = false, $zf_cache = false, $pCacheTime=BIT_QUERY_DEFAULT) {
		if ( $this->mType == "firebird") {
			$pQuery = preg_replace("/\\\'/", "''", $pQuery);
			$pQuery = preg_replace("/ NOW/", " 'NOW'", $pQuery);
			$pQuery = preg_replace("/now\(\)/", "'NOW'", $pQuery);
		}
		return $this->query( $pQuery, NULL, $pNumRows, NULL, $pCacheTime );
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
		// PURE VIRTUAL
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
	function MetaColumns($table,$normalize=true, $schema=false) {
		// PURE VIRTUAL
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
	function MetaIndexes($table,$primary=false, $owner=false) {
		// PURE VIRTUAL
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

	function getCol( $pQuery, $pValues=FALSE, $pTrim=FALSE ) {
		// PURE VIRTUAL
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
	function getArray( $pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		// PURE VIRTUAL
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
	function getAssoc( $pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		// PURE VIRTUAL
	}

	/** Executes the SQL and returns the first row as an array. The recordset and remaining rows are discarded for you automatically. If an error occurs, false is returned.
	* See AdoDB GetRow() function for more detail.
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pValues an array of values used in a parameterised query
	* @return returns the first row as an array, or false if an error occurs
	*/
	function getRow( $pQuery, $pValues=FALSE, $pCacheTime=BIT_QUERY_DEFAULT ) {
		// PURE VIRTUAL
	}

	/** Returns a single column value from the database.
	* @param pQuery the SQL query. Use backticks (`) to quote all table
	* and attribute names for AdoDB to quote appropriately.
	* @param pValues an array of values used in a parameterised query
	* @param pReportErrors report errors to STDOUT
	* @param pOffset the row number to begin returning rows from.
	* @return the associative array, or false if an error occurs
	*/
	function getOne($pQuery, $pValues=NULL, $pNumRows=NULL, $pOffset=NULL, $pCacheTime = BIT_QUERY_DEFAULT ) {
		// PURE VIRTUAL
	}

	/**
	* This function will take a set of fields identified by an associative array - $insertData
	* generate a suitable SQL script
	* and insert the data into the specified table - $insertTable
	* @param insertTable Name of the table to be inserted into
	* @param insertData Array of data to be inserted. Array keys provide the field names
	* @return Error status of the insert
	*/
	function associateInsert( $insertTable, $insertData ) {
		$setSql = ( '`'.implode( array_keys( $insertData ), '`, `' ).'`' );
		//stupid little loop to generate question marks. Start at one, and tack at the end to ease dealing with comma
		$valueSql = '';
		for( $i = 1; $i < count( $insertData ); $i++ ) {
			$valueSql .= '?, ';
		}
		$valueSql .= '?';

		if( $insertTable[0] != '`' ) {
			$insertTable = '`'.$insertTable.'`';
		}

		$query = "INSERT INTO $insertTable ( $setSql ) VALUES ( $valueSql )";

		$result = $this->query( $query, array_values( $insertData ) );

		return( $result );
	}

	/**
	* This function will take a set of fields identified by an associative array - $updateData
	* generate a suitable SQL script
	* update the data into the specified table
	* at the location identified in updateId which holds a name and value entry
	* @param updateTable Name of the table to be updated
	* @param updateData Array of data to be changed. Array keys provide the field names
    * If an array key contains an '=' it will assumed to already be properly quoted.
    * This allows use of keys like this: `column_name` = `column_name` + ?
	* @param updateId Array identifying the record to update.
	*		Array key 'name' provide the field name, and 'value' the record key
	* @return Error status of the insert
	*/
	function associateUpdate( $updateTable, $updateData, $updateId ) {
		$setSql = '';
		foreach( $updateData as $key=>$value ) {
			if (strpos($key,'=') === false) {
				$setSql .= ", `$key` = ?";
			}
			else
				$setSql .= ', ' . $key;
			}
		$setSql = 	substr($setSql,1);
		$bindVars = array_values( $updateData );
		$keyNames = ( '`'.implode( array_keys( $updateId ), '`=? AND `' ).'`=?' );
		$keyVars = array_values( $updateId );
		$bindVars = array_merge( $bindVars, $keyVars );
		if( $updateTable[0] != '`' ) {
			$updateTable = '`'.$updateTable.'`';
		}

		$query = "UPDATE $updateTable SET $setSql WHERE $keyNames";
		$result = $this->query( $query, $bindVars );

		return( $result );
	}

	/**
	* A database portable Sequence management function.
	*
	* @param pSequenceName Name of the sequence to be used
	*		It will be created if it does not already exist
	* @return		0 if not supported, otherwise a sequence id
	*/
	function GenID( $pSequenceName, $pUseDbPrefix = true ) {
		// PURE VIRTUAL
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
	function CreateSequence($seqname='adodbseq',$startID=1) {
		// PURE VIRTUAL
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
	function ifNull($pField, $pNullRepl) {
		// PURE VIRTUAL
	}

	/**
	 * A database portable RANDOM() function.
	 * Adodb overrides it anyway with it's $rand property.
	 *
	 * @return string with RANDOM() function.
	 */
	function random() {

		switch( $this->mType ) {
			case "postgres":
			case "pgsql":
				return "RANDOM()";

			case "mssql":
				return "NEWID()";

			default:
				return "RAND()";

		}
	}

	/** Format the timestamp in the format the database accepts.
	* @param pDate a Unix integer timestamp or an ISO format Y-m-d H:i:s
	* @return the timestamp as a quoted string.
	* @todo could be used to later convert all int timestamps into db
	* timestamps. Currently not used anywhere.
	*/
	function ls($pDate) {
		// PURE VIRTUAL
	}

	/**
	 * Return the current timestamp literal relevent to the database type
	 * @todo This needs extending to allow the use of GMT timestamp
	 *		rather then the current server time
	 */
	function NOW() {
		global $gBitDbType, $gBitSystem;
		switch( $gBitDbType ) {
			case "firebird":
				$ret = $gBitSystem->getUTCTimestamp(); // UTC time to get round server offsets
				break;
			default:
				$ret = 'now()';
		}
		return $ret;
	}

	/**
	 * Return the current timestamp literal relevent to the database type
	 * @todo This needs extending to allow the use of GMT timestamp
	 *		rather then the current server time
	 */
	function qtNOW() {
		global $gBitDbType, $gBitSystem;
		switch( $gBitDbType ) {
			case "firebird":
				$ret = "'".$gBitSystem->getUTCTimestamp()."'"; // UTC time to get round server offsets
				break;
			default:
				$ret = 'now()';
		}
		return $ret;
	}

	/** Return the sql to cast the given column from a time stamp to a Unix epoch
	* this is most useful for the many places bitweaver stores time as epoch integers
	* ADODB has no native support for this, see http://phplens.com/lens/lensforum/msgs.php?id=13661&x=1
	* @param pColumn name of an integer, or long integer column
	* @return the timestamp as a quoted string.
	* @todo could be used to later convert all int timestamps into db
	* timestamps. Currently not used anywhere.
	*/
	function SQLTimestampToInt( $pColumn ) {
		global $gBitDbType;
		switch( $gBitDbType ) {
			case "firebird":
				$ret = "CAST `$pColumn` AS TIMESTAMP";
				break;
			case "mysql":
			case "mysqli":
				$ret = "UNIX_TIMESTAMP( `$pColumn` )";
				break;
			case "pgsql":
			case "postgres":
			case "postgres7":
				$ret = $pColumn.'::abstime::integer';
				break;
			default:
				$ret = $pColumn;
		}
		return $ret;
	}

	/** Return the sql to cast the given column from an long integer to a time stamp.
	* this is most useful for the many places bitweaver stores time as epoch integers
	* ADODB has no native support for this, see http://phplens.com/lens/lensforum/msgs.php?id=13661&x=1
	* @param pColumn name of an integer, or long integer column
	* @return the timestamp as a quoted string.
	* @todo could be used to later convert all int timestamps into db
	* timestamps. Currently not used anywhere.
	*/
	function SQLIntToTimestamp( $pColumn ) {
		global $gBitDbType;
		switch( $gBitDbType ) {
			case "firebird":
				$ret = "(`$pColumn` / 86400.000000) + CAST ( '01/01/1970' AS TIMESTAMP )";
				break;
			case "mysql":
			case "mysqli":
				$ret = "CAST( `$pColumn` AS DATETIME )";
				break;
			case "pgsql":
			case "postgres":
			case "postgres7":
				$ret = $pColumn.'::integer::abstime::timestamptz';
				break;
			default:
				$ret = $pColumn;
		}
		return $ret;
	}

	public static function getPeriodFormat( $pPeriod ) {
		switch( $pPeriod ) {
			case 'year':
				$format = 'Y';
				break;
			case 'quarter':
				$format = 'Y-\QQ';
				break;
			case 'day':
				$format = 'Y-m-d';
				break;
			case 'week':
				$format = 'Y \Week W';
				break;
			case 'month':
			default:
				$format = 'Y-m';
				break;
		}
		return $format;
	}

	/** Return the sql to lock selected rows for updating.
	* ADODB has no native support for this, see http://phplens.com/lens/lensforum/msgs.php?id=13661&x=1
	* @param pColumn name of an integer, or long integer column
	* @return the timestamp as a quoted string.
	* @todo could be used to later convert all int timestamps into db
	* timestamps. Currently not used anywhere.
	*/
	function SQLForUpdate() {
		global $gBitDbType;
		switch( $gBitDbType ) {
			case "firebird":
			case "pgsql":
			case "postgres":
			case "postgres7":
				$ret = ' FOR UPDATE ';
				break;
			default:
				$ret = '';
		}
		return $ret;
	}

	/**
	 * Format date column in sql string given an input format that understands Y M D
	 */
	function SQLDate($pDateFormat, $pBaseDate=false) {
		// PURE VIRTUAL
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
		// PURE VIRTUAL
	}

	/** Converts backtick (`) quotes to the appropriate quote for the
	* database.
	* @private
	* @param pQuery the SQL query using backticks (`)
	* @return the correctly quoted SQL statement
	* @todo investigate replacement by AdoDB NameQuote() function
	*/
	function convertQuery( &$pQuery ) {
		$pQuery = preg_replace( "!(^\s+)|(\s+$)!s", "", $pQuery );
		if( !empty( $this->mType ) ) {
			switch( $this->mType ) {
				case "oci8":
				case "oci8po":
					// Force Oracle to always be insensitive
					$pQuery = str_replace( '`', '', $pQuery );
					break;
				case "pgsql":
				case "postgres":	// For PEAR
				case "postgres7":	// Deprecated ADODB
				case "mssql":
				case "sybase":
				case "firebird":
					if( $this->getCaseSensitivity() ) {
						$pQuery = str_replace( '`', '"', $pQuery );
					} else {
						$pQuery = str_replace( '`', '', $pQuery );
					}
					break;
				case "sqlite":
					$pQuery = str_replace( '`', '', $pQuery );
					break;
			}
		}
	}

	/**
	 * Converts field sorting abbreviation to SQL - you can pass in a single string or an entire array of sortmodes
	 *
	 * @param string or array $pSortMode fieldname and sort order string (eg name_asc)
	 * @access public
	 * @return the correctly quoted SQL ORDER statement
	 */
	function convertSortmode( $pSortMode ) {
		if( is_array( $pSortMode ) ) {
			$sql = '';
			foreach( $pSortMode as $sortMode ) {
				if( !empty( $sql ) ) {
					$sql .= ',';
				}
				$sql .= $this->convertSortmodeOneItem( $sortMode );
			}
			return $sql;
		} else {
			return $this->convertSortmodeOneItem( $pSortMode );
		}
	}

	/**
	 * Converts field sorting abbreviation to SQL and it also allows us to do things like sort by random rows.
	 *
	 * @param array $pSortMode If pSortMode is 'random' it will insert the properly named db-specific function to achieve this.
	 * @access public
	 * @return valid, database-specific sortmode - if sortmode is not valid, NULL is returned
	 */
	function convertSortmodeOneItem( $pSortMode ) {
		// check $sort_mode for evil stuff
		if( $pSortMode = preg_replace('/[^.0-9A-Za-z_,]/', '', $pSortMode) ) {
			if( $sep = strrpos( $pSortMode, '_' ) ) {
				$order = substr( $pSortMode, $sep );
				// force ending to neither _asc or _desc
				if ( $order !='_asc' && $order != '_desc' ) {
					$pSortMode = substr( $pSortMode, 0, $sep ) . '_desc';
				}
			} elseif( $pSortMode != 'random' ) {
				$pSortMode .= '_desc';
			}

			$pSortMode = preg_replace( '/lastModif/', 'last_modified', $pSortMode );
			$pSortMode = preg_replace( '/pageName/', 'title', $pSortMode );
			$pSortMode = preg_replace( '/^user_(asc|desc)/', 'login_\1', $pSortMode );

			$bIsFunction = FALSE;

			//Use random() of BitDbBase. BitDbAdodb will override it with its implementation.
			if( $pSortMode == "random" ) {
				$pSortMode = $this->random ();
				$bIsFunction = TRUE;
			}

			if( !$bIsFunction ) {
				switch( $this->mType ) {
					case "oci8po":
						$pSortMode = preg_replace( "/_asc$/", "` ASC NULLS LAST", $pSortMode );
						$pSortMode = preg_replace( "/_desc$/", "` DESC NULLS LAST", $pSortMode );
						break;
					case "firebird":
						// Use of alias in order by is not supported because of optimizer processing
						if ( $pSortMode == 'page_name_asc' )           $pSortMode = 'title_asc';
						if ( $pSortMode == 'page_name_desc' )          $pSortMode = 'title_desc';
						if ( $pSortMode == 'content_id_asc' )          $pSortMode = 'lc.content_id_asc';
						if ( $pSortMode == 'content_id_desc' )         $pSortMode = 'lc.content_id_desc';
						if ( $pSortMode == 'creator_user_asc' )        $pSortMode = 'uuc.login_asc';
						if ( $pSortMode == 'creator_user_desc' )       $pSortMode = 'uuc.login_desc';
						if ( $pSortMode == 'creator_real_name_asc' )   $pSortMode = 'uuc.real_name_asc';
						if ( $pSortMode == 'creator_real_name_desc' )  $pSortMode = 'uuc.real_name_desc';
						if ( $pSortMode == 'modifier_user_asc' )       $pSortMode = 'uue.login_asc';
						if ( $pSortMode == 'modifier_user_desc' )      $pSortMode = 'uue.login_desc';
						if ( $pSortMode == 'modifier_real_name_asc' )  $pSortMode = 'uue.real_name_asc';
						if ( $pSortMode == 'modifier_real_name_desc' ) $pSortMode = 'uue.real_name_desc';
					case "oci8":
					case "sybase":
					case "mssql":
					case "sqlite":
					case "mysql3":
					case "postgres":
					case "mysql":
					default:
						$pSortMode = preg_replace( "/_asc$/", "` ASC", $pSortMode );
						$pSortMode = preg_replace( "/_desc$/", "` DESC", $pSortMode );
						break;
				}
				$pSortMode = str_replace( ",", "`,`",$pSortMode );
				if( strpos( $pSortMode, '.' ) ) {
					$pSortMode = str_replace( ".", ".`",$pSortMode );
				} else {
					$pSortMode = "`" . $pSortMode;
				}
			}
		} else {
			$pSortMode = NULL;
		}
		return $pSortMode;
	}

	/** Returns the keyword to force a column comparison to be case sensitive
	* for none case-sensitive databases (eg MySQL)
	* @return the SQL keyword
	* @todo only used in gBitSystem and users_lib to compare login names
	*/
	function convertBinary() {
		switch ($this->mType) {
			case "oci8":
			case "firebird":
			case "sqlite":
			break;
			case "mysql3":
			case "mysql":
			return "BINARY";
			break;
		}
	}

	/** Used to cast variable types for certain databases (ie SyBase & MSSQL)
	* @param pVar the variable value to cast
	* @param pType the current variable type
	* @return the SQL casting statement
	*/
	function sqlCast($pVar,$pType) {
		switch ($this->mType) {
			case "sybase":
			case "mssql":
			switch ($pType) {
				case "int":
				return " CONVERT(numeric(14,0),$pVar) ";
				break;
				case "string":
				return " CONVERT(varchar(255),$pVar) ";
				break;
				case "float":
				return " CONVERT(numeric(10,5),$pVar) ";
				break;
			}
			break;
			default:
			return($pVar);
			break;
		}
	}
	/**
	* Used to encode blob data (eg PostgreSQL). Can be called statically
	* @todo had a lot of trouble with AdoDB BlobEncode and BlobDecode
	* the code works but will need work for dbs other than PgSQL
	* @param pData a string of raw blob data
	* @return escaped blob data
	*/
	function dbByteEncode( &$pData ) {
		// need to use this global so as not to break static calls
		global $gBitDbType;
		switch ( $gBitDbType ) {
			case "postgres":
				$search = array(chr(92), chr(0), chr(39));
				$replace = array('\\\134', '\\\000', '\\\047');
				$ret = str_replace($search, $replace, $pData);
				break;
			default:
				$ret = &$pData;
				break;
		}
		return $ret;
	}

	/**
	* Used to decode blob data (eg PostgreSQL)
	* @todo had a lot of trouble with AdoDB BlobEncode and BlobDecode
	* the code works but will need work for dbs other than PgSQL
	* @param pData escaped blob data
	* @return a string of raw blob data
	*/
	function dbByteDecode( &$pData ) {
		switch ($this->mDb->mType) {
			case "postgres":
				$ret = stripcslashes( $pData );
				break;
			default:
				$ret = &$pData;
				break;
		}
		return $ret;
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
		// PURE VIRTUAL
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
		// PURE VIRTUAL
	}

	/**
	 * If database does not support transactions, rollbacks always fail, so return false
	 * otherwise returns true if the Rollback was successful
	 *
	 * @return true/false.
	 */
	function RollbackTrans() {
		// PURE VIRTUAL
	}

	/**
	* @return # rows affected by UPDATE/DELETE
	*/
	function Affected_Rows() {
		// PURE VIRTUAL
	}

	/**
	 * Check for Postgres specific extensions
	 */
	function isAdvancedPostgresEnabled() {
		// This code makes use of the badass /usr/share/pgsql/contrib/tablefunc.sql
		// contribution that you have to install like: psql foo < /usr/share/pgsql/contrib/tablefunc.sql
		return defined( 'ADVANCED_PGSQL' );
	}


	/**
	 * determine current version of the databse
	 * @return # hash including 'description', 'version' full string, 'major', 'minor', and 'revsion'
	 */
	function getDatabaseVersion() {
		$ret = $this->mDb->ServerInfo();
		$versionHash = explode( '.', $ret['version'] );
		$ret['major'] = !empty( $versionHash[0] ) ? $versionHash[0] : 0;
		$ret['minor'] = !empty( $versionHash[1] ) ? $versionHash[1] : 0;
		$ret['revision'] = !empty( $versionHash[2] ) ? $versionHash[2] : 0;
		return $ret;
	}


	/**
	 * Compatibility function for DBs with case insensitive searches
	 * (like MySQL, see: http://dev.mysql.com/doc/refman/5.1/en/case-sensitivity.html)
	 * How to use:
     * 	AND ".$this->mDb->getCaseLessColumn('lc.title')." = 'page title'
     * The reason all this matters is that huge performane difference between:
	 *   where title = 'PAGE TITLE'
	 * and
	 *   where UPPER(tittle) = 'PAGE TITTLE'
     * The latter version will not make use of the index on page title (at least for MySQl)
     * while the first vesion will use the index.  In a case insensitive search DB (MySQL) both
     * forms of the query will give the same results, the only difference being the preformance.
	 * Spiderr suggested this solution and suppled the code below
	 */
	function getCaselessColumn( $pColumn ) {
		global $gBitDbType;
		switch( $gBitDbType ) {
			case "mysql":
			case "mysqli":
				$ret = $pColumn;
				break;
			default:
				$ret = " UPPER($pColumn) ";
				break;
		}
		return $ret;
	}

	/**
	 * Renamed a few functions - these are the temporary backward compatability calls with the deprecated note
	 * These funcitons will be removed in due course
	 */
	/**
	 * @deprecated deprecated since version 2.0.0
	 */
	function convert_sortmode( $pSortMode ) {
		deprecated( $this->depText( 'convert_sortmode', 'convertSortmode' ) );
		return $this->convertSortmode( $pSortMode );
	}
	/**
	 * @deprecated deprecated since version 2.0.0
	 */
	function convert_sortmode_one_item( $pSortMode ) {
		deprecated( $this->depText( 'convert_sortmode_one_item', 'convertSortmodeOneItem' ) );
		return $this->convertSortmode( $pSortMode );
	}
	/**
	 * @deprecated deprecated since version 2.0.0
	 */
	function convert_binary() {
		deprecated( $this->depText( 'convert_binary', 'convertBinary' ) );
		return $this->convertBinary();
	}
	/**
	 * @deprecated deprecated since version 2.0.0
	 */
	function sql_cast( $pVar, $pType ) {
		deprecated( $this->depText( 'sql_cast', 'sqlCast' ) );
		return $this->sqlCast( $pVar, $pType );
	}
	/**
	 * @deprecated deprecated since version 2.0.0
	 */
	function db_byte_encode( &$pData ) {
		deprecated( $this->depText( 'db_byte_encode', 'dbByteEncode' ) );
		return $this->dbByteEncode( $pData );
	}
	/**
	 * @deprecated deprecated since version 2.0.0
	 */
	function db_byte_decode( &$pData ) {
		deprecated( $this->depText( 'db_byte_decode', 'dbByteDecode' ) );
		return $this->dbByteDecode( $pData );
	}
	function depText( $pFrom, $pTo ) {
		return "We have changed this method to BitDbBase::{$pTo}().
	Please update your code accordingly - you can try using the following (please back up your code before applying this):
	find <your package>/ -name \"*.php\" -exec perl -i -wpe 's/\b{$pFrom}\b/{$pTo}/g' {} \;";
	}
}
?>
