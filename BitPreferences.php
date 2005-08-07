<?php
/**
 * Preferences Management Class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/BitPreferences.php,v 1.3 2005/08/07 17:38:44 squareing Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * A class used to store and retrieve preferences. Defaults are set
 * programmatically. Storage can be with or without a database.
 *
 * Currently use to store sitewide preferences
 *
 * created 2004/8/15
 *
 * @author wolff_borg <wolff_borg@yahoo.com.au>
 *
 * @todo Could be subclassed to store user preferences.
 */

/**
 * A class used to store and retrieve preferences. Defaults are set
 * programmatically. Storage can be with or without a database.
 *
 * @package kernel
 */
class BitPreferences
{
    /**
    * Debug flag
    * @private
    */
    var $mDebug;
    /**
    * String used to refer to preference caching and database table
    * @private
    */
    var $mName;
    /**
    * Used to store preferences that are different from the defaults.
    * @private
    */
    var $mPrefs = array();
    /**
    * Used to store defaults for preferences
    * @private
    */
    var $mDefaultPrefs = array();
    /**
    * Used to caching mechanism
    * @private
    */
    var $mCache;
    /**
    * Used to store database mechanism
    * @private
    */
    var $mDB;
    /**
    * During initialisation, we assign a caching and database
    * mechanism which are used by the class.
    * @param pName a unique identified used in caching and database
    * mechanisms
    * @param pCache the instance of the caching mechanism
    * @param pDB the instance of the database mechanism
    **/
    function BitPreferences($pName, $pCache = NULL, $pDB = NULL)
    {
        $this->mName = $pName;
        $this->setCache($pCache);
        $this->setDatabase($pDB);
    }
    /**
    * Sets caching mechanism for the instance
    * @param pCache the instance of the caching mechanism
    **/
    function setCache(&$pCache)
    {
        // set internal cache and retrieve it if it exists
        $this->mCache = &$pCache;
        if (isset($this->mCache) && $this->mCache->isCached($this->mName))
        {
            $this->mPrefs = $this->mCache->getCached($this->mName);
        }
    }
    /**
    * Sets database mechanism for the instance
    * @param pDB the instance of the database mechanism
    **/
    function setDatabase(&$pDB)
    {
        // set internal db and retrieve values
        $this->mDB = &$pDB;
        if (isset($this->mDB))
        {
            $query = "SELECT * FROM `".$this->mName."`";
            $result = $this->mDB->query($query);
            while (is_object($result) && $res = $result->fetchRow())
            {
                $this->mPrefs[$res["name"]] = $res["value"];
            }
        }
        // update cache
        if (isset($this->mCache))
        {
            $this->mCache->setCached($this->mName, $this->mPrefs);
        }
    }
    /**
    * Defaults are checked to see if new value is default.
    * If value is the same, we don't store it. This means that only
    * changes in settings are stored.
    * @param pName the name of the preference
    * @param pValue the value of the preference
    **/
    function setPreference($pName, $pValue)
    {
        $change = false;
        if ($this->mDebug) echo "setPreference Name: $pName, Value: $pValue<br>";
        // is value not set or same as default, remove from custom preferences
        if ($pValue == NULL || (isset($this->mDefaultPrefs[$pName]) &&
        $this->mDefaultPrefs[$pName] == $pValue))
        {
            unset($this->mPrefs[$pName]);
            if (isset($this->mDB))
            {
                if ($this->mDebug) echo "setPreference db delete $pName<br>";
                $query = "DELETE FROM `".$this->mName."` WHERE `name`=?";
                $bindvars[] = $pName;
                $this->mDB->query($query, $bindvars);
            }
            $change = true;
            // is new value same as old value,
        }
        elseif (!isset($this->mPrefs[$pName]) || $this->mPrefs[$pName] != $pValue)
        {
            $this->mPrefs[$pName] = $pValue;
            if (isset($this->mDB))
            {
                $query = "SELECT * FROM `".$this->mName."` WHERE `name`=?";
                $bindvars[] = $pName;
                $result = $this->mDB->getOne($query, $bindvars);
                if (!empty($result))
                {
                    if ($this->mDebug) echo "setPreference db update $pName with $pValue<br>";
                    $query = "UPDATE `".$this->mName."` SET `value`=? WHERE `name`=?";
                    $bindvars = array($pValue, $pName);
                    $this->mDB->query($query, $bindvars);
                }
                else
                {
                    if ($this->mDebug) echo "setPreference db insert $pName with $pValue<br>";
                    $query = "INSERT INTO `".$this->mName."` (`name`,`value`) VALUES (?,?)";
                    $bindvars = array($pName, $pValue);
                    $result = $this->mDB->query($query, $bindvars);
                }
            }
            $change = true;
        }
        // update cache
        if ($change && isset($this->mCache))
        {
            $this->mCache->setCached($this->mName, $this->mPrefs);
        }
    }
    /**
    * Defaults are set using this function. All other setPreference()
    * calls are compared with the value and only stored if different.
    * @param pName the name of the preference
    * @param pDefault the default value of the preference
    **/
    function setDefaultPreference($pName, $pDefault)
    {
        if ($this->mDebug) echo "setDefaultPreference Name: $pName, Default: $pDefault<br>";
        $this->mDefaultPrefs[$pName] = $pDefault;
        if ($this->mDebug) echo "setDefaultPreference2 OldValue: ".$this->getPreference($pName)."<br>";
        $this->setPreference($pName, $this->getPreference($pName));
        if ($this->mDebug) echo "setDefaultPreference3 NewValue: ".$this->getPreference($pName)."<br>";
    }
    /**
    * In get_preference, defaults are checked to see if new value is default.
    * If value is the same, we don't store it. This means that changes in
    * defaults will always reflect through.
    * @param pName the name of the preference
    * @return the associated value of the preference name
    **/
    function getPreference($pName)
    {
        $value = NULL;
        if( isset($this->mPrefs[$pName]) )
        {
            $value = $this->mPrefs[$pName];
        }
        elseif( isset($this->mDefaultPrefs[$pName]) )
        {
            $value = $this->mDefaultPrefs[$pName];
        }
        return $value;
    }
}
?>

