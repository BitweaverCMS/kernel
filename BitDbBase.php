<?php
/**
 * ADOdb Library interface Class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitDbBase.php,v 1.14 2006/02/17 23:03:11 spiderr Exp $
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
 * ensure your AdoDB install is a subdirectory off your include path
 */
require_once( KERNEL_PKG_PATH.'bit_error_inc.php' );

define( 'BIT_QUERY_DEFAULT', -1 );


/**
 * This class is used for database access and provides a number of functions to help
 * with database portability.
 *
 * Currently used as a base class, this class should be optional to ensure bitweaver
 * continues to function correctly, without a valid database connection.
 *
 * @package kernel
 */
class BitDb
{
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
	* Determines if fatal query functions should terminate script execution. Defaults to TRUE. Can be deactived for things like expected duplicate inserts
	* @private
	*/
	var $mFatalActive;
	/**
	* During initialisation, database parameters are passed to the class.
	* If these parameters are not valid, class will not be initialised.
	*/
	function BitDb()
	{
		$this->mCacheFlag = FALSE;
		$this->mNumQueries = 0;
		$this->setFatalActive();
		global $gDbCaseSensitivity;
		$this->setCaseSensitivity( $gDbCaseSensitivity );
	}
	/**
	* This function contains any pre-connection work
	* @private
	* @todo investigate if this is the correct way to do it.
	*/
	function preDBConnection()
	{
		// Pre connection setup
		if(isset($this->mType)) {
			// we have a db we're gonna try to load
			switch ($this->mType)
			{
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
	function postDBConnection()
	{
		// Post connection setup
		switch ($this->mType)
		{
			case "sybase":
			case "mssql":
			$this->mDb->Execute("set quoted_identifier on");
			break;
			case "postgres":
			// Do a little prep work for postgres, no break, cause we want default case too
			if (defined("BIT_DB_PREFIX") && preg_match( "/\./", BIT_DB_PREFIX) )
			{
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
		global $gDebug;
		if( $gDebug ) {
			global $gBitSystem;
			$this->mQueryLap = $gBitSystem->mTimer->elapsed();
			$this->mDb->debug = $gDebug;
			flush();
		}
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
		return( $this->mCaseSensitive );
	}
	/**
	* Used to stop query tracking and output results if in debug mode
	*/
	function queryComplete() {
		global $num_queries;
		//count the number of queries made
		$num_queries++;
		$this->mNumQueries++;
		global $gDebug;
		if( $gDebug ) {
			global $gBitSystem;
			$interval = $gBitSystem->mTimer->elapsed() - $this->mQueryLap;
			$style = ( $interval > .5 ) ? 'color:red;' : (( $interval > .15 ) ? 'color:orange;' : '');
			print '<p style="'.$style.'">### Query: '.$num_queries.' Start time: '.$this->mQueryLap.' ### Query run time: '.$interval.'</p>';
			$this->mQueryLap = 0;
			flush();
		}
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
	function createTables($pTables, $pOptions = array())
	{
		// PURE VIRTUAL
	}

	/**
	* Used to check if tables already exists.
	* @todo should be used to confirm tables are already created
	* @param pTable the table name
	* @return true if table already exists
	*/
	function tableExists($pTable)
	{
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
	function dropTables($pTables)
	{
		// PURE VIRTUAL
	}

	/**
	* Function to set ADODB query caching member variable
	* @param pCacheExecute flag to enable or disable ADODB query caching
	* @return nothing
	*/
	function setCacheState( $pCacheFlag=TRUE ) {
		$this->mCacheFlag = $pCacheFlag;
	}

	/**
	* Function to set ADODB query caching member variable
	* @param pCacheExecute flag to enable or disable ADODB query caching
	* @return nothing
	*/
	function getCacheState() {
		return( $this->mCacheFlag );
	}

	/**
	* Quotes a string to be sent to the database
	* @param pStr string to be quotes
	* @return quoted string using AdoDB->qstr()
	*/
	function qstr($pStr)
	{
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
	function queryError( $pQuery, &$pError, $pValues = NULL, $pNumRows = -1, $pOffset = -1 )
	{
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
	function query($query, $values = null, $numrows = BIT_QUERY_DEFAULT, $offset = BIT_QUERY_DEFAULT, $pCacheTime=BIT_QUERY_DEFAULT )
	{
		// PURE VIRTUAL
	}

	/**
	* ADODB compatibility functions for bitcommerce
	*/
	function Execute($pQuery, $pNumRows = false, $zf_cache = false, $pCacheTime=BIT_QUERY_DEFAULT) {
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

	function getCol( $pQuery, $pValues=FALSE, $pTrim=FALSE )
	{
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
	function getArray( $pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT )
	{
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
	function getAssoc( $pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT )
	{
		// PURE VIRTUAL
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
	function getOne($pQuery, $pValues=NULL, $pNumRows=NULL, $pOffset=NULL, $pCacheTime = BIT_QUERY_DEFAULT )
	{
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
	}

	/**
	* This function will take a set of fields identified by an associative array - $updateData
	* generate a suitable SQL script
	* update the data into the specified table
	* at the location identified in updateId which holds a name and value entry
	* @param updateTable Name of the table to be updated
	* @param updateData Array of data to be changed. Array keys provide the field names
	* @param updateId Array identifying the record to update.
	*		Array key 'name' provide the field name, and 'value' the record key
	* @return Error status of the insert
	*/
	function associateUpdate( $updateTable, $updateData, $updateId ) {
		$setSql = ( '`'.implode( array_keys( $updateData ), '`=?, `' ).'`=?' );
		$bindVars = array_values( $updateData );
		$keyNames = ( '`'.implode( array_keys( $updateId ), '`=? AND `' ).'`=?' );
		$keyVars = array_values( $updateId );
		$bindVars = array_merge( $bindVars, $keyVars );
		if( $updateTable[0] != '`' ) {
			$updateTable = '`'.$updateTable.'`';
		}

		$query = "UPDATE $updateTable SET $setSql WHERE $keyNames";
		$result = $this->query( $query, $bindVars );
	}

	/**
	* A database portable Sequence management function.
	*
	* @param pSequenceName Name of the sequence to be used
	*		It will be created if it does not already exist
	* @return		0 if not supported, otherwise a sequence id
	*/
	function GenID( $pSequenceName ) {
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
	function CreateSequence($seqname='adodbseq',$startID=1)
	{
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
	function ifNull($pField, $pNullRepl)
	{
		// PURE VIRTUAL
	}

	/** Format the timestamp in the format the database accepts.
	* @param pDate a Unix integer timestamp or an ISO format Y-m-d H:i:s
	* @return the timestamp as a quoted string.
	* @todo could be used to later convert all int timestamps into db
	* timestamps. Currently not used anywhere.
	*/
	function ls($pDate)
	{
		// PURE VIRTUAL
	}

	/**
	 * Return the current timestamp literal relevent to the database type
	 * @todo This needs extending to allow the use of GMT timestamp
	 *		rather then the current server time
	 */
	function NOW() {
		global $gBitDbType;
		switch( $gBitDbType ) {
			case "firebird":
				$ret = "'NOW'"; // SQL standard Literal
				break;
			default:
				$ret = 'now()';
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
	function convertQuery(&$pQuery)
	{
		if( !empty( $this->mType ) ) {
			switch ($this->mType) {
				case "oci8":
				case "oci8po":
				case "pgsql":
				case "postgres":	// For PEAR
				case "postgres7":	// Deprecated ADODB
				case "mssql":
				case "sybase":
					if( $this->getCaseSensitivity() ) {
						$pQuery = preg_replace("/`/", "\"", $pQuery);
					} else {
						$pQuery = preg_replace("/`/", "", $pQuery);
					}
					break;
				case "firebird":
					$pQuery = preg_replace("/`/", "\"", $pQuery);
					break;
				case "sqlite":
					$pQuery = preg_replace("/`/", "", $pQuery);
					break;
			}
		}
	}

	/** Converts field sorting abbreviation to SQL
	* @param pSortMode fieldname and sort order string (eg name_asc)
	* @return the correctly quoted SQL ORDER statement
	*/
	function convert_sortmode($pSortMode) {
		/* functionMap allows us to things like sort by random rows. If the
			sortMode requested is 'random' it will insert the properly named
			db-specific function to achieve this. - ATS
		*/

		// parse $sort_mode for evil stuff
		$pSortMode = preg_replace('/[^.A-Za-z_,]/', '', $pSortMode);

		if( $sep = strrpos($pSortMode, '_') ) {
			$order = substr($pSortMode, $sep);
			// force ending to either _asc or _desc
			if ( $order !='_asc' && $order != '_desc' ) {
				$pSortMode = substr($pSortMode, 0, $sep) . '_desc';
			}
		} elseif( $pSortMode != 'random' ) {
			$pSortMode .= '_desc';
		}

		$pSortMode = preg_replace( '/lastModif/', 'last_modified', $pSortMode );
		$pSortMode = preg_replace( '/pageName/', 'title', $pSortMode );
		$pSortMode = preg_replace( '/^user_/', 'login_', $pSortMode );

		$bIsFunction = FALSE;
		$functionMap = array( 'random' => array("postgres" => "RANDOM()",
												"pgsql" => "RANDOM()",
												"mysql3" => "RAND()",
												"mysql" => "RAND()",
												"mssql" => "NEWID()",
												"firebird" => "1"));
												//"oci8" => "" STILL NEEDED
												//"sqlite" => "" STILL NEEDED
												//"sybase" => "" STILL NEEDED
		foreach ($functionMap as $funcName=>$funcMethods) {
			if ($pSortMode == $funcName) {
				if	(in_array($this->mType, array_keys($funcMethods))) {
					$pSortMode = $funcMethods[$this->mType];
					$bIsFunction = TRUE;
				}
			}
		}

		if (!$bIsFunction) {
			switch ($this->mType)
			{
				case "firebird":
					// Use of alias in order by is not supported because of optimizer processing
					if ( $pSortMode == 'page_name_asc' ) $pSortMode = 'title_asc';
					if ( $pSortMode == 'page_name_desc' ) $pSortMode = 'title_desc';
					if ( $pSortMode == 'content_id_asc' ) $pSortMode = 'lc.content_id_asc';
					if ( $pSortMode == 'content_id_desc' ) $pSortMode = 'lc.content_id_desc';
					if ( $pSortMode == 'creator_user_asc' ) $pSortMode = 'uuc.login_asc';
					if ( $pSortMode == 'creator_user_desc' ) $pSortMode = 'uuc.login_desc';
					if ( $pSortMode == 'creator_real_name_asc' ) $pSortMode = 'uuc.real_name_asc';
					if ( $pSortMode == 'creator_real_name_desc' ) $pSortMode = 'uuc.real_name_desc';
					if ( $pSortMode == 'modifier_user_asc' ) $pSortMode = 'uue.login_asc';
					if ( $pSortMode == 'modifier_user_desc' ) $pSortMode = 'uue.login_desc';
					if ( $pSortMode == 'modifier_real_name_asc' ) $pSortMode = 'uue.real_name_asc';
					if ( $pSortMode == 'modifier_real_name_desc' ) $pSortMode = 'uue.real_name_desc';
				case "oci8":
				case "sybase":
				case "mssql":
				case "sqlite":
				case "mysql3":
				case "postgres":
				case "mysql":
				default:
					$pSortMode = preg_replace("/_asc$/", "` ASC", $pSortMode);
					$pSortMode = preg_replace("/_desc$/", "` DESC", $pSortMode);
					$pSortMode = str_replace(",", "`,`",$pSortMode);
					if( strpos( $pSortMode, '.' ) ) {
						$pSortMode = str_replace(".", ".`",$pSortMode);
					} else {
						$pSortMode = "`" . $pSortMode;
					}
				break;
			}
		}
		return $pSortMode;
	}

	/** Returns the keyword to force a column comparison to be case sensitive
	* for none case-sensitive databases (eg MySQL)
	* @return the SQL keyword
	* @todo only used in gBitSystem and users_lib to compare login names
	*/
	function convert_binary()
	{
		switch ($this->mType)
		{
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
	function sql_cast($pVar,$pType)
	{
		switch ($this->mType)
		{
			case "sybase":
			case "mssql":
			switch ($pType)
			{
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
	/** will activate ADODB's native debugging output
	* @param pLevel debugging level - FALSE is off, TRUE is on, 99 is verbose
	**/
	function debug( $pLevel=99 ) {
		// PURE VIRTUAL
	}

	/**
	* Used to encode blob data (eg PostgreSQL). Can be called statically
	* @todo had a lot of trouble with AdoDB BlobEncode and BlobDecode
	* the code works but will need work for dbs other than PgSQL
	* @param pData a string of raw blob data
	* @return escaped blob data
	*/
	function db_byte_encode( &$pData ) {
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
	function db_byte_decode( &$pData ) {
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
}
?>
