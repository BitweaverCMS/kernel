<?php
/**
* $Header: /cvsroot/bitweaver/_bit_kernel/BitCache.php,v 1.3 2005/08/04 12:21:58 lsces Exp $
*
* Copyright (c) 2004 bitweaver.org
* Copyright (c) 2003 tikwiki.org
* Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
* All Rights Reserved. See copyright.txt for details and a complete list of authors.
* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
*
* $Id: BitCache.php,v 1.3 2005/08/04 12:21:58 lsces Exp $
*
* A basic library to handle caching of some Tiki Objects. Usage is simple and feel free to improve it.
*
* Currently used to cache user permissions only. Could be used to store blobs to files and other static
* database intensive queries.
*
* @package kernel
*
* created 2003/11/25
*
* @author lrargerich <lrargerich@yahoo.com>
*
* @version $Revision: 1.3 $ $Date: 2005/08/04 12:21:58 $ $Author: lsces $
*
* @todo Need to implement in more places
*/

/**
 * @package kernel
 * @subpackage BitCache
 */
class BitCache
{
    /**
    * Used to store the directory used to store the cache files.
    * @private
    */
    var $mFolder;
    /**
    * Will check the temp cache folder for existence and create it if necessary.
    * @todo Check that the folder is a directory and throw an error if not so.
    * @todo add $bitdomain prefix and mkdir_p if does not exist.
    */
    function BitCache()
    {
        if (defined("BIT_TEMP_PATH"))
        {
            $this->mFolder = BIT_TEMP_PATH."cache";
        }
	elseif (getenv("TMP"))
	{
            $this->mFolder = getenv("TMP")."/cache";
        }
	else
	{
            $this->mFolder = "/tmp/cache";
        }
        if (!file_exists($this->mFolder))
        {
            // NO, this does _not_ create a world writeable directory. mkdir will '&' current mask (default 0022) with 0777 to give you '0755'. Older versions of php 4.1 (4.1.2 for sure) had the mask as a required paramter.
            mkdir($this->mFolder, 0777);
        }
    }
    /**
    * Used to cache an object to a file.
    * @param pKey the unique identifier used to retrieve the cached item
    * @param pData the object to be cached
    */
    function setCached($pKey,$pData)
    {
        //echo "setCached: $pKey/$pData ";
        if ($pData != NULL)
        {
            $cache_folder = $this->mFolder;
            $pKey = md5($pKey);
            $file = "$cache_folder/$pKey";
            $x = serialize($pData);
            if(!function_exists("file_put_contents"))
            require_once(UTIL_PKG_PATH."PHP_Compat/Compat/Function/file_put_contents.php");
            file_put_contents($file,$x);
        }
        else
        {
            $this->removeCached($pKey);
        }
    }
    /**
    * Used to check if an object is cached.
    * @param pKey the unique identifier used to retrieve the cached item
    * @return true if cached object exists
    */
    function isCached($pKey)
    {
        $cache_folder = $this->mFolder;
        $pKey = md5($pKey);
        $file = "$cache_folder/$pKey";
        return file_exists($file);
    }
    /**
    * Used to retrieve an object if cached.
    * @param pKey the unique identifier used to retrieve the cached item
    * @return object if cached object exists
    */
    function getCached($pKey)
    {
        //echo "getCached: $pKey<br>";
        $pData = NULL;
        $cache_folder = $this->mFolder;
        $pKey = md5($pKey);
        $file = "$cache_folder/$pKey";
        if (file_exists($file))
        {
            $x = file_get_contents($file);
            $pData = unserialize($x);
            if (!$pData)
            $pData = NULL;
        }
        return $pData;
    }
    /**
    * Used to remove a cached object.
    * @param pKey the unique identifier used to retrieve the cached item
    */
    function removeCached($pKey)
    {
        //echo "removeCached: $pKey<br>";
        $cache_folder = $this->mFolder;
        $pKey = md5($pKey);
        $file = "$cache_folder/$pKey";
        if (file_exists($file))
        {
            unlink($file);
        }
    }
}
?>
