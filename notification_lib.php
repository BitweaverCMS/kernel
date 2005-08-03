<?php
/**
 * eMail Notification Library
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/notification_lib.php,v 1.1.1.1.2.5 2005/08/03 16:53:47 lsces Exp $
 * @author awcolley
 *
 * created 2003/06/03
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 */

/**
 * A library use to store email addresses registered for specific notification events.
 *
 * Currently used in articles, trackers, users register and wiki.
 *
 * @package kernel
 * @todo does not need to inherit BitBase class. Should hold a BitDb connection as a
 * global variable.
 */
class NotificationLib extends BitBase
{
    /**
    * Notifies via email triggered events.
    */
    function NotificationLib()
    {
        BitBase::BitBase();
    }
    /**
    * Lists registered notification events
    * @param offset the location to begin listing from
    * @param maxRecords the maximum number of records returned
    * @param sort_mode the method of sorting used in the listing
    * @param find text used to filter listing
    * @return array of registered notification events
    */
    function list_mail_events($offset, $maxRecords, $sort_mode, $find)
    {
        if ($find)
        {
            $findesc = '%' . strtoupper( $find ) . '%';
            $mid = " where (UPPER(`event`) like ? or UPPER(`email`) like ?)";
            $bindvars=array($findesc,$findesc);
        }
        else
        {
            $mid = " ";
            $bindvars=array();
        }
        $query = "select * from `".BIT_DB_PREFIX."tiki_mail_events` $mid order by ".$this->convert_sortmode($sort_mode);
        $query_cant = "select count(*) from `".BIT_DB_PREFIX."tiki_mail_events` $mid";
        $result = $this->query($query,$bindvars,$maxRecords,$offset);
        $cant = $this->getOne($query_cant,$bindvars);
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
    * Adds an email address for a specified event notification
    * @param event the specified event
    * @param object the specified object
    * @param email the email to remove
    * @return array of the notification record
    */
    function add_mail_event($event, $object, $email)
    {
        $query = "insert into `".BIT_DB_PREFIX."tiki_mail_events`(`event`,`object`,`email`) values(?,?,?)";
        $result = $this->query($query, array($event,$object,$email) );
    }
    /**
    * Removes an email address for a specified event notification
    * @param event the specified event
    * @param object the specified object
    * @param email the email to remove
    */
    function remove_mail_event($event, $object, $email)
    {
        $query = "delete from `".BIT_DB_PREFIX."tiki_mail_events` where `event`=? and `object`=? and `email`=?";
        $result = $this->query($query,array($event,$object,$email));
    }
    /**
    * Retrieves the email addresses for a specific event
    * @param event the specified event
    * @param object the specified object
    * @return array of email addresses
    */
    function get_mail_events($event, $object)
    {
        $query = "select `email` from `".BIT_DB_PREFIX."tiki_mail_events` where `event`=? and (`object`=? or `object`='*')";
        $result = $this->query($query, array($event,$object) );
        $ret = array();
        while ($res = $result->fetchRow())
        {
            $ret[] = $res["email"];
        }
        return $ret;
    }
}

/**
 * @global NotificationLib Notification library
 */
global $notificationlib;
$notificationlib = new NotificationLib(); 
?>
