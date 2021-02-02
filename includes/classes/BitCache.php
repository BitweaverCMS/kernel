<?php
/**
 * @package kernel
 * @author spiderr <spiderr@bitweaver.org>
 * Copyright (c) 2020 bitweaver.org, All Rights Reserved
 * This source file is subject to the 2.0 GNU GENERAL PUBLIC LICENSE. 
 *
 * A basic library to handle caching of various data
 */

class BitCache {
	/**
	 * Used to store the directory used to store the cache files.
	 * @private
	 */
	public $mFolder;
	/**
	 * Will check the temp cache folder for existence and create it if necessary.
	 *
	 * @param string $pSubdir use a specifed subdirectory
	 * @param boolean $pUseStorage use the storage directory instead of the temp dir. only makes sense if you need direct webaccess to stored cachefiles
	 * @access public
	 * @return void
	 */
	function __construct( $pSubdir = 'cache', $pUseStorage = FALSE ) {
		if( $pUseStorage and defined(STORAGE_PKG_PATH) ) {
			$this->mFolder = STORAGE_PKG_PATH.$pSubdir;
			$this->mUrl = STORAGE_PKG_URL.$pSubdir;
		} elseif( defined( "TEMP_PKG_PATH" )) {
			$this->mFolder = TEMP_PKG_PATH.$pSubdir;
		} elseif( getenv( "TMP" )) {
			$this->mFolder = getenv( "TMP" )."/".$pSubdir;
		} else {
			$this->mFolder = "/tmp/".$pSubdir;
		}

		if( !is_dir( $this->mFolder ) && !mkdir_p( $this->mFolder )) {
			error_log( 'Can not create the cache directory: '.$this->mFolder );
		}
	}

	/**
	 * getCacheFile
	 *
	 * @param string $pFile
	 * @access public
	 * @return filepath on success, FALSE on failure
	 */
	function getCacheFile( $pFile ) {
		if( !empty( $pFile )) {
			return $this->mFolder."/".$pFile;
		} else {
			return FALSE;
		}
	}

	/**
	 * getCacheUrl will get the URL to the cache file - only works when you're using BitCache with the UseStorage option
	 *
	 * @param string $pFile
	 * @access public
	 * @return fileurl on success, FALSE on failure
	 */
	function getCacheUrl( $pFile ) {
		if( !empty( $this->mUrl ) && !empty( $pFile )) {
			return $this->mUrl.'/'.$pFile;
		}
	}

	/**
	 * Used to check if an object is cached.
	 *
	 * @param string $pFile name of the file we want to check for
	 * @param numeric $pModTime Pass in the modification time you wish to check against
	 * @access public
	 * @return true if cached object exists
	 */
	function isCached( $pFile, $pModTime = FALSE ) {
		if( !empty( $pFile ) && is_readable( $this->getCacheFile( $pFile ))) {
			// compare the cache filemtime to the desired file
			if( is_numeric( $pModTime )) {
				$isModified = ( filemtime( $this->getCacheFile( $pFile )) < $pModTime );
			}
			return( empty( $isModified ));
		} else {
			return FALSE;
		}
	}

	/**
	 * Used to retrieve an object if cached.
	 *
	 * @param pKey the unique identifier used to retrieve the cached item
	 * @return object if cached object exists
	 */
	function readCacheFile( $pFile ) {
		if( $this->isCached( $pFile )) {
			$cacheFile = $this->getCacheFile( $pFile );
			if( $h = fopen( $cacheFile, 'r' )) {
				$ret = fread( $h, filesize( $cacheFile ) );
				fclose( $h );
			}
		}

		return( !empty( $ret ) ? $ret : NULL );
	}

	/**
	 * Used to remove a cached object.
	 *
	 * @param pKey the unique identifier used to retrieve the cached item
	 */
	function expungeCacheFile( $pFile ) {
		if( $this->isCached( $pFile )) {
			unlink( $this->getCacheFile( $pFile ));
		}
	}

	/**
	 * remove the entire cache in the cache folder
	 *
	 * @access public
	 * @return TRUE on success, FALSE on failure
	 */
	function expungeCache() {
		// the only places we can write to in bitweaver are temp and storage
		$subdir = str_replace( STORAGE_PKG_PATH, "", $this->mFolder );
		if(( strpos( $this->mFolder, STORAGE_PKG_PATH ) === 0 && $subdir != "users" && $subdir != "common" ) || strpos( $this->mFolder, TEMP_PKG_PATH ) === 0 ) {
			$ret = unlink_r( $this->mFolder );
			if( !is_dir( $this->mFolder )) {
				mkdir_p( $this->mFolder );
			}
		}
		return $ret;
	}

	/**
	 * writeCacheFile
	 *
	 * @param string $pFile file to write to
	 * @param string $pData string to write to file
	 * @access public
	 * @return void
	 */
	function writeCacheFile( $pFile, $pData ) {
		if( !empty( $pData ) && !empty( $pFile )) {
			if( $h = fopen( $this->getCacheFile( $pFile ), 'w' )) {
				fwrite( $h, $pData );
				fclose( $h );
			}
		}
	}
}
?>
