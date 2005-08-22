<?php
/**
 * Date Handling Class
 *
 * @package kernel
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/BitDate.php,v 1.1.1.1.2.4 2005/08/22 11:25:09 lsces Exp $
 *
 * Created by: Jeremy Jongsma (jjongsma@tickchat.com)
 * Created on: Sat Jul 26 11:51:31 CDT 2003
 */

/**
 * BitDate
 *
 * This class takes care of all time/date conversions for
 * storing dates in the DB and displaying dates to the user.
 *
 * The objectives are:
 *  - Dates will always stored in UTC in the database
 *  - Display dates will be computed based on the preferred
 *    display offset specified in the constructor
 *
 * @package kernel
 *
 * @todo As of 1.7, dates are still stored in server local time.
 * This should be changed for 1.7.1 (requires many module changes).
 */
class BitDate {
	/**
	 * UTC offset to use for display
	 */
	var $display_offset;

	/**
	 * Current UTC offset of server
	 */
	var $server_offset;

	/**
	 * Default constructor
	 * $_display_offset: desired offset for date display, in minutes
	 */
	function BitDate($_display_offset = 0) {
		$this->display_offset = $_display_offset;

		$this->server_offset = intval(date("Z"));
	}

	/**
	 * Convert a UTC timestamp to the preferred display offset.
	 * $timestamp: UTC timestamp to convert.
	 * returns: Display-offset timestamp.
	 */
	function getDisplayDateFromUTC($_timestamp) {
		return $this->getTimestampFromISO($_timestamp) + $this->display_offset;
	}

	/**
	 * Convert a display-offset timestamp to UTC.
	 * $timestamp: Display timestamp to convert.
	 * returns: UTC timestamp.
	 */
	function getUTCFromDisplayDate($_timestamp) {
		return $this->getTimestampFromISO($_timestamp) - $this->display_offset;
	}

	/**
	 * Convert a UTC timestamp to the local server time.
	 * $timestamp: UTC timestamp to convert.
	 * returns: Server timestamp.
	 */
	function getServerDateFromUTC($_timestamp) {
		return $this->getTimestampFromISO($_timestamp) + $this->server_offset;
	}

	/**
	 * Convert a local server timestamp to UTC.
	 * $timestamp: Server timestamp to convert.
	 * returns: UTC timestamp.
	 */
	function getUTCFromServerDate($_timestamp) {
		return $this->getTimestampFromISO($_timestamp) - $this->server_offset;
	}

	/**
	 * Convert a display timestamp to the local server time.
	 * $timestamp: Display timestamp to convert.
	 * returns: Server timestamp.
	 */
	function getServerDateFromDisplayDate($_timestamp) {
		return $this->getServerDateFromUTC($this->getUTCFromDisplayDate($this->getTimestampFromISO($_timestamp)));

	}

	/**
	 * Convert a local server timestamp to a display timestamp.
	 * $timestamp: Server timestamp to convert.
	 * returns: Display timestamp.
	 */
	function getDisplayDateFromServerDate($_timestamp) {
		return $this->getDisplayDateFromUTC($this->getUTCFromServerDate($this->getTimestampFromISO($_timestamp)));
	}

	/**
	 * Retrieve a current UTC timestamp.
	 */
	function getUTCTime() {
		return time() - $this->server_offset;
	}

	/**
	 * Get the name of the current timezone.
	 *
	 * Currently, only "UTC" or an empty string (Local).
	 */
	function getTzName() {
		if ($this->display_offset == 0)
			return "UTC";
		else
			return "";
	}

	/**
	 * Convert ISO date to numberic timestamp.
	 *
	 * @param string ISO format date<br>
	 *	yYYY-mM-dD hH:mM:sS.s ( Lower case letters optional, but should be 0 )
	 * @return integer Seconds count based on 1st Jan 1970<br>
	 * returns $iso_date if it is a number, or 0 if format invalid
	 */
	function getTimestampFromISO($iso_date) {
		$ret = 0;
		if ( is_numeric($iso_date) ) $ret = $iso_date;
		else if (preg_match( 
		"|^([0-9]{3,4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})[ -]?(([0-9]{1,2}):?([0-9]{1,2}):?([0-9\.]{1,4}))?|", 
			($iso_date), $rr)) {
			// h-m-s-MM-DD-YY
			if (!isset($rr[5])) $ret = adodb_mktime(0,0,0,$rr[2],$rr[3],$rr[1]);
			else $ret = @adodb_mktime($rr[5],$rr[6],$rr[7],$rr[2],$rr[3],$rr[1]);
		}
		return $ret;			
	}
	
}

?>