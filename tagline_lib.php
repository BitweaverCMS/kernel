<?php
/**
 * Tagline Management Library
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/Attic/tagline_lib.php,v 1.3 2005/08/07 17:38:45 squareing Exp $
 * @author awcolley
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 */

/**
 * A library used to store taglines used for Message of the day and other randomly select texts.
 *
 * Currently used for cookies.
 *
 * created 2003/06/19
 *
 * @package kernel
 * @todo does not need to inherit BitBase class. Should hold a BitDb connection as a
 * global variable. 
 */
class TagLineLib extends BitBase
{
    /**
    * Stores and retrieves taglines.
    */
    function TagLineLib()
    {
        BitBase::BitBase();
    }
    /**
    * Lists stored taglines.
    * @param offset the location to begin listing from
    * @param maxRecords the maximum number of records returned
    * @param sort_mode the method of sorting used in the listing
    * @param find text used to filter listing
    * @return array of taglines
    */
    function list_cookies($offset, $maxRecords, $sort_mode, $find)
    {
        if ($find)
        {
            $mid = " where (UPPER(`cookie`) like ?)";
            $bindvars = array('%' . strtoupper( $find ) . '%');
        }
        else
        {
            $mid = "";
            $bindvars = array();
        }
        $query = "select * from `".BIT_DB_PREFIX."tiki_cookies` $mid order by ".$this->mDb->convert_sortmode($sort_mode);
        $query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_cookies` $mid";
        $result = $this->mDb->query($query,$bindvars,$maxRecords,$offset);
        $cant = $this->mDb->getOne($query_cant,$bindvars);
        $ret = array();
        while ($res = $result->fetchRow())
        {
            $ret[] = $res;
        }
        $retval = array();
        $retval["data"] = $ret;
        $retval["cant"] = $cant;
        return $retval;
    }
    /**
    * Replace a tagline
    * @todo This doesn't look like it works correctly - wolff_borg
    *
    * @param cookieId tagline unqiue identifier
    * @param cookie text of tagline
    */
    function replace_cookie($cookieId, $cookie)
    {
        // $cookie = addslashes($cookie);
        // Check the name if ($cookieId)
        if($cookieId) {
            $query = "update `".BIT_DB_PREFIX."tiki_cookies` set `cookie`=? where `cookieId`=?";
            $bindvars = array($cookie,(int)$cookieId);
        }
        else
        {
            $bindvars = array($cookie);
            $query = "delete from `".BIT_DB_PREFIX."tiki_cookies` where `cookie`=?";
            $result = $this->mDb->query($query,$bindvars);
            $query = "insert into `".BIT_DB_PREFIX."tiki_cookies`(`cookie`) values(?)";
        }
        $result = $this->mDb->query($query,$bindvars);
        return true;
    }
    /**
    * Removes a tagline by unqiue identifier
    * @param cookieId tagline unqiue identifier
    */
    function remove_cookie($cookieId)
    {
        $query = "delete from `".BIT_DB_PREFIX."tiki_cookies` where `cookieId`=?";
        $result = $this->mDb->query($query,array((int)$cookieId));
        return true;
    }
    /**
    * Retrieves the tagline by unique identifier
    * @param cookieId tagline unqiue identifier
    * @return array of tagline row information
    */
    function get_cookie($cookieId)
    {
        $query = "select * from `".BIT_DB_PREFIX."tiki_cookies` where `cookieId`=?";
        $result = $this->mDb->query($query,array((int)$cookieId));
        if (!$result->numRows())   return false;
        $res = $result->fetchRow();
        return $res;
    }
    /**
    * Removes all stored taglines
    */
    function remove_all_cookies()
    {
        $query = "delete from `".BIT_DB_PREFIX."tiki_cookies`";
        $result = $this->mDb->query($query,array());
    }
	/*shared*/
	function pick_cookie() {
		$cant = $this->mDb->getOne("select count(*) from `".BIT_DB_PREFIX."tiki_cookies`",array());
		if (!$cant) return '';

		$bid = rand(0, $cant - 1);
		//$cookie = $this->mDb->getOne("select `cookie`  from `".BIT_DB_PREFIX."tiki_cookies` limit $bid,1"); getOne seems not to work with limit
		$result = $this->mDb->query("select `cookie`  from `".BIT_DB_PREFIX."tiki_cookies`",array(),1,$bid);
		if ($res = $result->fetchRow()) {
		$cookie = str_replace("\n", "", $res['cookie']);
		return '<i>"' . $cookie . '"</i>';
		}
		else
		return "";
	}

}

/**
 * @global TagLineLib Cookie manangement library
 */
global $taglinelib;
$taglinelib = new TagLineLib();
?>
