<?php
/**
 * ADOdb Library interface Class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/BitDb.php,v 1.4.2.17 2005/08/09 10:28:31 lsces Exp $
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
require_once(UTIL_PKG_PATH."adodb/adodb.inc.php");
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
		global $gBitDbType, $gBitDbHost, $gBitDbUser, $gBitDbPassword, $gBitDbName, $ADODB_FETCH_MODE;
		$this->mCacheFlag = FALSE;
		$this->mNumQueries = 0;
		$this->setFatalActive();

		global $ADODB_CACHE_DIR;
		if( empty( $ADODB_CACHE_DIR ) ) {
			$ADODB_CACHE_DIR = getTempDir().'/adodb/'.$_SERVER['HTTP_HOST'].'/';
		}
		mkdir_p( $ADODB_CACHE_DIR );


		// Get all the ADODB stuff included
		if (!defined("ADODB_FORCE_NULLS"))
			define("ADODB_FORCE_NULLS", 1);
		if (!defined("ADODB_ASSOC_CASE"))
			define("ADODB_ASSOC_CASE", 0);
		if (!defined("ADODB_CASE_ASSOC"))
			define("ADODB_CASE_ASSOC", 0);
		// typo in adodb's driver for sybase?
		if (!defined("ADODB_FETCH_MODE")) {
			define("ADODB_FETCH_MODE", ADODB_CASE_ASSOC);
		}
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

		if( !empty( $gBitDbName ) && !empty( $gBitDbType ) ) {
			$this->mType = $gBitDbType;
			$this->mName = $gBitDbName;
			if(!isset($this->mName)) {
				die("No database name specified");
			}
			$this->preDBConnection();
			$this->mDb = ADONewConnection($gBitDbType);
		$this->mDb->Connect($gBitDbHost, $gBitDbUser, $gBitDbPassword, $gBitDbName);

			if(!$this->mDb)
			{
				die("Unable to login to the database '<?=$gBitDbName?>' on '<?=$gBitDbHost?>' as `user` '<?=$gBitDbUser?>'<p>".$this->mDb->ErrorMsg());
			}
			$this->postDBConnection();
			unset ($pDSN);
			if( defined( "DB_PERFORMANCE_STATS" ) && constant( "DB_PERFORMANCE_STATS" ) )
			{
				$this->mDb->LogSQL();
			}
		}
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
		switch ($this->mType)
		{
			case "mysql":
			// SHOULD HANDLE INNODB so foreign keys are cool - XOXO spiderr
			$pOptions['mysql'] = 'TYPE=INNODB';
			default:
			//$pOptions[] = 'REPLACE';
		}
		$dict = NewDataDictionary($this->mDb);
		$this->mFailed = array();
		$result = true;
		foreach(array_keys($pTables) AS $tableName)
		{
			$completeTableName = (defined("BIT_DB_PREFIX")) ? BIT_DB_PREFIX.$tableName : $tableName;
			$sql = $dict->CreateTableSQL($completeTableName, $pTables[$tableName], $pOptions);
			if ($sql && ($dict->ExecuteSQLArray($sql) > 0))
			{
				// Success
			}
			else
			{
				// Failure
				$result = false;
				array_push($this->mFailed, $sql.": ".$this->mDb->ErrorMsg());
			}
		}
		return $result;
	}
	/**
	* Used to check if tables already exists.
	* @todo should be used to confirm tables are already created
	* @param pTable the table name
	* @return true if table already exists
	*/
	function tableExists($pTable)
	{
		$dict = NewDataDictionary($this->mDb);
	$pTable = preg_replace("/`/", "", $pTable);
		$tables = $dict->MetaTables(false, false, $pTable);
		return array_search($pTable, $tables) !== FALSE;
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
		$dict = NewDataDictionary($this->mDb);
		$this->mFailed = array();
		$return = true;
		foreach($pTables AS $tableName)
		{
			$completeTableName = (defined("BIT_DB_PREFIX")) ? BIT_DB_PREFIX.$tableName : $tableName;
			$sql = $dict->DropTableSQL($completeTableName);
			if ($sql && ($dict->ExecuteSQLArray($sql) > 0))
			{
				//echo "Success<br>";
			}
			else
			{
				//echo "Failure<br>";
				$return = false;
				array_push($this->mFailed, $sql);
			}
		}
		return $return;
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
	* Quotes a string to be sent to the database which is
	* passed to function on to AdoDB->qstr().
	* @todo not sure what its supposed to do
	* @param pStr string to be quotes
	* @return quoted string using AdoDB->qstr()
	*/
	function qstr($pStr)
	{
		return $this->mDb->qstr($pStr);
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
	function queryError( $pQuery, &$pError, $pValues = NULL, $pNumRows = -1,
	$pOffset = -1 )
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
			if( !defined( 'IS_LIVE' ) || $pCacheTime == BIT_QUERY_DEFAULT ) {
				$result = $this->mDb->Execute( $query, $values );
			} else {
				$result = $this->mDb->CacheExecute( $pCacheTime, $query, $values );
			}
		} else {
			if( !defined( 'IS_LIVE' ) || $pCacheTime == BIT_QUERY_DEFAULT ) {
				$result = $this->mDb->SelectLimit( $query, $numrows, $offset, $values );
			} else {
				$result = $this->mDb->CacheSelectLimit( $pCacheTime, $query, $numrows, $offset, $values );
			}
		}

		$this->queryComplete();
		return $result;
	}

	/**
	* ADODB compatibility functions for bitcommerce
	*/
	function Execute($pQuery, $pNumRows = false, $zf_cache = false, $pCacheTime=0) {
		return $this->query( $pQuery, NULL, $pNumRows, BIT_QUERY_DEFAULT, $pCacheTime );
	}

	/**
	 * List columns in a database as an array of ADOFieldObjects. 
	 * See top of file for definition of object.
	 *
	 * @param table	table name to query
	 * @param upper	uppercase table name (required by some databases)
	 * schema is optional database schema to use - not supported by all databases.
	 *
	 * @return  array of ADOFieldObjects for current table.
	 */
	function MetaColumns($table,$normalize=true) {
		return $this->mDb->MetaColumns( $table, $normalize );
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
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery($pQuery);
		$execFunction = ( !defined( 'IS_LIVE' ) || $pCacheTime == BIT_QUERY_DEFAULT ? 'GetAssoc' : 'CacheGetAssoc' );
		$result = $this->mDb->getCol( $pQuery, $pValues, $pTrim );
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
	function getAssoc( $pQuery, $pValues=FALSE, $pForceArray=FALSE, $pFirst2Cols=FALSE, $pCacheTime=BIT_QUERY_DEFAULT )
	{
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		$this->queryStart();
		$this->convertQuery($pQuery);
		$execFunction = ( !defined( 'IS_LIVE' ) || $pCacheTime == BIT_QUERY_DEFAULT ? 'GetAssoc' : 'CacheGetAssoc' );
		$result = $this->mDb->GetAssoc( $pQuery, $pValues, $pForceArray, $pFirst2Cols );
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
		if( !defined( 'IS_LIVE' ) || $pCacheTime == BIT_QUERY_DEFAULT ) {
			$result = $this->mDb->GetRow( $pQuery, $pValues );
		} else {
			$result = $this->mDb->CacheGetRow( $pCacheTime, $pQuery, $pValues );
		}
		$this->queryComplete();
		return $result;
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
		}
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
		}
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
		$result = $this->query($pQuery, $pValues, 1, $pOffset, $pCacheTime );
		$res = ($result != NULL) ? $result->fetchRow() : false;
		if ($res === false)
		{
			return (NULL);
			//simulate pears behaviour
		}
		//$this->debugger_log($pQuery, $pValues);
		list($key, $value) = each($res);
		return $value;
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
		array_push( $bindVars, $updateId["value"] );
		if( $updateTable[0] != '`' ) {
			$updateTable = '`'.$updateTable.'`';
		}

		$query = "UPDATE $updateTable SET $setSql WHERE `".$updateId["name"]."`=?";
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
		if( empty( $this->mDb ) ) {
			return FALSE;
		}
		return $this->mDb->GenID( str_replace("`","",BIT_DB_PREFIX).$pSequenceName );
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
		if (empty($this->_genSeqSQL)) return FALSE;
		return $this->mDb->Execute(sprintf($this->_genSeqSQL,$seqname,$startID));
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
	function ts($pDate)
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
					$pQuery = preg_replace("/`/", "\"", $pQuery);
					// convert bind variables - adodb does not do that
					$qe = explode("?", $pQuery);
					$pQuery = "";
					for ($i = 0;
					$i < sizeof($qe) - 1;
					$i++)
					{
						$pQuery .= $qe[$i] . ":" . $i;
					}
					$pQuery .= $qe[$i];
					break;
				case "pgsql":
				case "postgres7":
					print '<div class="error">You must update your kernel/config_inc.php so that $gBitDbType="postgres"</div>';
				case "firebird":
				case "mssql":
				case "postgres":
				case "sybase":
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
		$pSortMode = preg_replace( '/lastModif/', 'last_modified', $pSortMode );
		$pSortMode = preg_replace( '/pageName/', 'title', $pSortMode );
		$pSortMode = preg_replace( '/^user_/', 'login_', $pSortMode );

		$bIsFunction = FALSE;
		$functionMap = array( 'random' => array("postgres" => "RANDOM()",
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
		if( is_object( $this->mDb ) ) {
			$this->mDb->debug = $pLevel;
		}
	}

	/**
	 *
	 */
	function debugger_log($query, $values)
	{
		// Will spam only if debug parameter present in URL
		// \todo DON'T FORGET TO REMOVE THIS BEFORE 1.8 RELEASE
		if (!isset($_REQUEST["debug"])) return;
		// spam to debugger log
		include_once( DEBUG_PKG_PATH.'debugger.php' );
		global $debugger;
		if (is_array($values) && strpos($query, '?'))
			foreach ($values as $v)
			{
				$q = strpos($query, '?');
				if ($q)
				{
					$tmp = substr($query, 0, $q)."'".$v."'".substr($query, $q + 1);
					$query = $tmp;
				}
			}
		$debugger->msg($this->num_queries.': '.$query);
	}

	/**
	* Used to encode blob data (eg PostgreSQL)
	* @todo had a lot of trouble with AdoDB BlobEncode and BlobDecode
	* the code works but will need work for dbs other than PgSQL
	* @param pData a string of raw blob data
	* @return escaped blob data
	*/
	function db_byte_encode( &$pData ) {
		global $ADODB_LASTDB;
		switch ($ADODB_LASTDB) {
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
		global $ADODB_LASTDB;
		switch ($ADODB_LASTDB) {
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
		 return $this->mDb->StartTrans();
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
		 return $this->mDb->CompleteTrans();
	}

	/**
	 * If database does not support transactions, rollbacks always fail, so return false
	 * otherwise returns true if the Rollback was successful
	 *
	 * @return true/false.
	 */
	function RollbackTrans() {
		 return $this->mDb->RollbackTrans();
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
		 return $this->mDb->MetaTables( $ttype, $showSchema, $mask );
	}

	/**
	* @return # rows affected by UPDATE/DELETE
	*/ 
	function Affected_Rows() {
		return $this->mDb->Affected_Rows();
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
