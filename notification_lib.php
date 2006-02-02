<?php
/**
 * eMail Notification Library
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/notification_lib.php,v 1.5 2006/02/02 08:59:46 squareing Exp $
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
        $query = "select * from `".BIT_DB_PREFIX."mail_notifications` $mid order by ".$this->mDb->convert_sortmode($sort_mode);
        $query_cant = "select count(*) from `".BIT_DB_PREFIX."mail_notifications` $mid";
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
    * Adds an email address for a specified event notification
    * @param event the specified event
    * @param object the specified object
    * @param email the email to remove
    * @return array of the notification record
    */
    function add_mail_event($event, $object, $email)
    {
        $query = "insert into `".BIT_DB_PREFIX."mail_notifications`(`event`,`object`,`email`) values(?,?,?)";
        $result = $this->mDb->query($query, array($event,$object,$email) );
    }
    /**
    * Removes an email address for a specified event notification
    * @param event the specified event
    * @param object the specified object
    * @param email the email to remove
    */
    function remove_mail_event($event, $object, $email)
    {
        $query = "delete from `".BIT_DB_PREFIX."mail_notifications` where `event`=? and `object`=? and `email`=?";
        $result = $this->mDb->query($query,array($event,$object,$email));
    }

    /**
    * Retrieves the email addresses for a specific event
    * @param event the specified event
    * @param object the specified object
    * @return array of email addresses
    */
    function get_mail_events($event, $object)
    {
        $query = "select `email` from `".BIT_DB_PREFIX."mail_notifications` where `event`=? and (`object`=? or `object`='*')";
        $result = $this->mDb->query($query, array($event,$object) );
        $ret = array();
        while ($res = $result->fetchRow())
        {
            $ret[] = $res["email"];
        }
        return $ret;
    }

    /**
    * Post changes to registered email addresses related to a change event
    * @param object number of the content item being updated
    * @param object content_type of the item
    * @param object the package that is being updated
    * @param object the name of the object
    * @param object the name of user making the change
    * @param object any comment added to the change
    * @param object the content of the change
    *
    * @todo Improve the generic handling of the messages
    * Param information probably needs to be passed as an array, or accessed from Content directly
    */
    function post_content_event($contentid, $type, $package, $name, $user, $comment, $data)
    { global $gBitSystem;
			
		$emails = $this->get_mail_events($package.'_page_changes', $type . $contentid);

		foreach ($emails as $email) {
			global $gBitSmarty;
			$gBitSmarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
			$gBitSmarty->assign('mail_page', $name );
			$gBitSmarty->assign('mail_date', $gBitSystem->getUTCTime());
			$gBitSmarty->assign('mail_user', $user );
			$gBitSmarty->assign('mail_comment', $comment );
			$gBitSmarty->assign('mail_last_version', 1);
			$gBitSmarty->assign('mail_data', $data );
			$gBitSmarty->assign('mail_machine', httpPrefix());
			$gBitSmarty->assign('mail_pagedata', $data );
			$mail_data = $gBitSmarty->fetch('bitpackage:'.$package.'/'.$package.'_change_notification.tpl');

			@mail($email, $package . tra(' page'). ' ' . $name . ' ' . tra('changed'), $mail_data, "From: ".$gBitSystem->getPreference( 'sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n" );
		}
	}

    /**
    * Notifies registered list of eMail recipients of new user registrations
    * @param object name of the new user
    */
    function post_new_user_event( $user )
	{ global $gBitSystem, $gBitSmarty;
		$emails = $this->get_mail_events('user_registers','*');
		foreach($emails as $email) {
			$gBitSmarty->assign('mail_user',$user);
			$gBitSmarty->assign('mail_date',$gBitSystem->getUTCTime());
			$gBitSmarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
			$mail_data = $gBitSmarty->fetch('bitpackage:users/new_user_notification.tpl');

			mail( $email, tra('New user registration'),$mail_data,"From: ".$gBitSystem->getPreference('sender_email')."\r\nContent-type: text/plain;charset=utf-8\r\n");
		}
	}

}

/**
 * @global NotificationLib Notification library
 */
global $notificationlib;
$notificationlib = new NotificationLib(); 
?>
