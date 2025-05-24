<?php
/**
 * Date Handling Class
 *
 * @package kernel
 * @version $Header$
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
	 * @var int
	 */
	public $display_offset;

	/**
	 * Current UTC offset of server
	 * @var int
	 */
	public $server_offset;

	/**
	 * Default constructor
	 * @param int desired offset for date display, in minutes
	 */
	function __construct($_display_offset = 0) {
		if ( version_compare( phpversion(), "5.1.0", ">=" ) ) {
			date_default_timezone_set( @date_default_timezone_get() );
		}
		$this->display_offset = $_display_offset;
		$this->server_offset = mktime(0,0,0,1,2,1970) - gmmktime(0,0,0,1,2,1970);
	}

	/**
	 * Retrieves the user's preferred offset for displaying dates.
	 *
	 * @param int the logged-in user.
	 * @return int the preferred offset to UTC or 0 for straight UTC display
	 */
	function get_display_offset($_user = false) {
		global $gBitUser;

		// Cache preference from DB
		$display_tz = "UTC";

		// Default to UTC get_display_offset
		$this->display_offset = 0;

		// Load pref from DB if cache is empty
		$display_tz = $gBitUser->getPreference('site_display_utc', "Local");

		// Recompute offset each request in case DST kicked in
		if ( $display_tz == "Local" && isset($_COOKIE["tz_offset"]))
			$this->display_offset = intval($_COOKIE["tz_offset"]);
		else if ( $display_tz == "Fixed" )
			$this->display_offset = $gBitUser->getPreference( 'site_display_timezone', 0 ); 
			if ( version_compare( phpversion(), "5.1.0", ">=" ) and !is_numeric( $this->display_offset ) ) {
				$dateTimeZoneUser = new DateTimeZone( $this->display_offset );
				$dtNow = new DateTime( "now" );
				$this->display_offset = $dateTimeZoneUser->getOffset( $dtNow );
			}
		return $this->display_offset;
	}

	/**
	 * Convert a UTC timestamp to the preferred display offset.
	 * @param timestamp ISO format date
	 *	yYYY-mM-dD hH:mM:sS.s ( Lower case letters optional, but should be 0 )
	 * @return int Seconds count based on 1st Jan 1970<br>
	 */
	function getDisplayDateFromUTC($_timestamp) {
		global $gBitUser;
		
		if ( $gBitUser->getPreference('site_display_utc', "Local") == "Fixed" && class_exists( 'DateTime' ) ) {
			date_default_timezone_set( $gBitUser->getPreference( 'site_display_timezone', 'UTC' ) );
			if ( is_numeric( $_timestamp )) {
				$dateTimeUser = new DateTime( '@'.$_timestamp );
			} else  {
				$dateTimeUser = new DateTime( $_timestamp );
			}
			$dateTimeUserZone = new DateTimeZone( $gBitUser->getPreference( 'site_display_timezone', 'UTC' ) );
			return strtotime( $dateTimeUser->format(DATE_ATOM) ) + timezone_offset_get( $dateTimeUserZone, $dateTimeUser );
		} else {
			return $this->getTimestampFromISO($_timestamp) + $this->display_offset;
		}
	}

	/**
	 * Convert a display-offset timestamp to UTC.
	 * @param timestamp ISO format date
	 *	yYYY-mM-dD hH:mM:sS.s ( Lower case letters optional, but should be 0 )
	 * @return int Seconds count based on 1st Jan 1970<br>
	 */
	function getUTCFromDisplayDate($_timestamp) {
		global $gBitUser;
		
		if ( $gBitUser->getPreference('site_display_utc', "Local") == "Fixed" ) {
			date_default_timezone_set( $gBitUser->getPreference( 'site_display_timezone', 'UTC' ) );
			if ( is_numeric( $_timestamp )) {
				$dateTimeUser = new DateTime( '@'.$_timestamp );
			} else  {
				$dateTimeUser = new DateTime( $_timestamp );
			}
			$dateTimeUserZone = new DateTimeZone( $gBitUser->getPreference( 'site_display_timezone', 'UTC' ) );
			return strtotime( $dateTimeUser->format(DATE_ATOM) ) - timezone_offset_get( $dateTimeUserZone, $dateTimeUser );
		} else {
			return $this->getTimestampFromISO($_timestamp) - $this->display_offset;
		}
	}

	/**
	 * Convert a UTC timestamp to the local server time.
	 * @param  timestamp UTC timestamp to convert.
	 * @return timestamp Server timestamp.
	 */
	function getServerDateFromUTC($_timestamp) {
		return $this->getTimestampFromISO($_timestamp) + $this->server_offset;
	}

	/**
	 * Convert a local server timestamp to UTC.
	 * @param  timestamp Server timestamp to convert.
	 * @return timestamp UTC timestamp.
	 */
	function getUTCFromServerDate($_timestamp) {
		return $this->getTimestampFromISO($_timestamp) - $this->server_offset;
	}

	/**
	 * Retrieve a current UTC timestamp as Unix epoch.
	 * @return int Unix epoch
	 */
	function getUTCTime() {
		return time();
	}

	/**
	 * Retrieve a current UTC Timestamp as an ISO formated date/time.
	 * @return string Current ISO formated date/time
	 */
	function getUTCTimestamp() {
		return $this->date("Y-m-d H:i:s",time(),true);
	}

	/**
	 * Retrieve a current UTC Date as an ISO formated date
	 * @return string Current ISO formated date
	 */
	function getUTCDate() {
		return $this->date("Y-m-d",time(),true);
	}

	/**
	 * Get the name of the current timezone.
	 * Currently, only "UTC" or an empty string (Local).
	 * @return string Current timezone
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
	 * @param string ISO format date
	 *	yYYY-mM-dD hH:mM:sS.s ( Lower case letters optional, but should be 0 )
	 * @return int Seconds count based on 1st Jan 1970<br>
	 * returns $iso_date if it is a number, or 0 if format invalid
	 */
	function getTimestampFromISO( $iso_date, $reverse = false ) {
		$ret = 0;
		if ( is_numeric($iso_date) ) { 
			$ret = $iso_date;
		} else if ( $reverse ) {
			// Input d.m.y, h:m:s
			if (preg_match(
				"|^([0-9]{1,2})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{3,4}), ?(([0-9]{1,2}):?([0-9]{1,2}):?([0-9\.]{1,8}))?|",
				($iso_date), $rr)) {
					if (!isset($rr[5])) $ret = $this->gmmktime(0,0,0,$rr[2],$rr[1],$rr[3]);
					else $ret = @$this->gmmktime($rr[5],$rr[6],$rr[7],$rr[2],$rr[1],$rr[3]);
				}
		} else {	
				// Input y.m.d h:m:s
				if (preg_match(
					"|^([0-9]{3,4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})[ -]?(([0-9]{1,2}):?([0-9]{1,2}):?([0-9\.]{1,4}))?|",
					($iso_date), $rr)) {
						if (!isset($rr[5])) $ret = $this->gmmktime(0,0,0,$rr[2],$rr[3],$rr[1]);
						else $ret = @$this->gmmktime($rr[5],$rr[6],$rr[7],$rr[2],$rr[3],$rr[1]);
			}
		}
		return $ret;
	}

	/**
	 * Returns day of week, 0 = Sunday,... 6=Saturday.
	 * Algorithm from PEAR::Date_Calc
	 * @param int
	 * @param int
	 * @param int
	 * @return int
	 */
	function dayOfWeek($year, $month, $day)
	{
	/*
	Pope Gregory removed 10 days - October 5 to October 14 - from the year 1582 and
	proclaimed that from that time onwards 3 days would be dropped from the calendar
	every 400 years.

	Thursday, October 4, 1582 (Julian) was followed immediately by Friday, October 15, 1582 (Gregorian).
	*/
		if ($year <= 1582) {
			if ($year < 1582 ||
				($year == 1582 && ($month < 10 || ($month == 10 && $day < 15)))) $greg_correction = 3;
		 	else
				$greg_correction = 0;
		} else
			$greg_correction = 0;

		if($month > 2)
			$month -= 2;
		else {
			$month += 10;
			$year--;
		}

		$day =  floor((13 * $month - 1) / 5) +
			$day + ($year % 100) +
			floor(($year % 100) / 4) +
			floor(($year / 100) / 4) - 2 *
			floor($year / 100) + 77 + $greg_correction;

		return $day - 7 * floor($day / 7);
	}

	/**
	 *	Returns week of year, 1 = first week of year.
	 *	Algorithm from PEAR::Date_Calc
	 * This needs to be checked out for both start day and early date rules
	 * @param int
	 * @param int
	 * @param int
	 * @return int
	 */
	function weekOfYear($year, $month, $day)
	{
        $iso    = $this->gregorianToISO($year, $month, $day);
        $parts  = explode('-',$iso);
        $week_number = intval($parts[1]);
        if ( $week_number == 0 ) $week_number = 53;
        return $week_number;
    }

	/**
	 * Checks for leap year, returns true if it is. No 2-digit year check. Also
	 * handles julian calendar correctly.
	 * @param int
	 * @return boolean
	 */
	function _is_leap_year($year)
	{
		if ($year % 4 != 0) return false;

		if ($year % 400 == 0) {
			return true;
		// if gregorian calendar (>1582), century not-divisible by 400 is not leap
		} else if ($year > 1582 && $year % 100 == 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * checks for leap year, returns true if it is. Has 2-digit year check
	 * @param int
	 * @return boolean
	 */
	function is_leap_year($year)
	{
		return  $this->_is_leap_year($this->year_digit_check($year));
	}

	/**
	 * Fix 2-digit years. Works for any century.
	 * Assumes that if 2-digit is more than 30 years in future, then previous century.
	 * @todo This needs to be disabled when dates prior to 100AD are required in ISO format
	 * @param int
	 * @return int
	 */
	function year_digit_check($y)
	{
		if ($y < 100) {

			$yr = (integer) date("Y");
			$century = (integer) ($yr /100);

			if ($yr%100 > 50) {
				$c1 = $century + 1;
				$c0 = $century;
			} else {
				$c1 = $century;
				$c0 = $century - 1;
			}
			$c1 *= 100;
			// if 2-digit year is less than 30 years in future, set it to this century
			// otherwise if more than 30 years in future, then we set 2-digit year to the prev century.
			if (($y + $c1) < $yr+30) $y = $y + $c1;
			else $y = $y + $c0*100;
		}
		return $y;
	}

	/**
	 *Returns an array with date info.
	 * @param boolean
	 * @param boolean
	 * @return array
	 */
	function getDate($d=false,$fast=false)
	{
		return getdate( $d );
/* 2025-05-24 spiderr - REMOVED - this looks like early PHP madness
		if ($d === false) return $this->getdate();
		if ((abs($d) <= 0x7FFFFFFF)) { // check if number in 32-bit signed range
			if (!defined('ADODB_NO_NEGATIVE_TS') || $d >= 0) // if windows, must be +ve integer
				return @$this->_getDate($d);
		}
		return $this->_getDate($d,$fast);
*/
	}

	/*
	 * generate $YRS table for _adodb_getdate()
	 *
	function _date_gentable($out=true)
	{

		for ($i=1970; $i >= 1600; $i-=10) {
			$s = adodb_gmmktime(0,0,0,1,1,$i);
			echo "$i => $s,<br>";
		}
	}
	adodb_date_gentable();

	for ($i=1970; $i > 1500; $i--) {

	echo "<hr>$i ";
		adodb_date_test_date($i,1,1);
	}

	*/

	/**
	 * Low-level function that returns the getdate() array. We have a special
	 * $fast flag, which if set to true, will return fewer array values,
	 * and is much faster as it does not calculate dow, etc.
	 * @param int Date to be converted in Unix epochs
	 * @param boolean Return short format array ( less weekday and month )
	 * @param boolean Ignore timezone
	 * @return array
	 */
/* 2025-05-24 spiderr - REMOVED - this code is as ancient as the dates it handles. use php getdate() function instead

	function _getDate($origd=false,$fast=false,$is_gmt=false)
	{
		static $YRS;

		$d =  $origd - ($is_gmt ? 0 : adodb_get_gmt_diff(false,false,false));

		$_day_power = 86400;
		$_hour_power = 3600;
		$_min_power = 60;

		if ($d < -12219321600) $d -= 86400*10; // if 15 Oct 1582 or earlier, gregorian correction

		$_month_table_normal = array("",31,28,31,30,31,30,31,31,30,31,30,31);
		$_month_table_leaf = array("",31,29,31,30,31,30,31,31,30,31,30,31);

		$d366 = $_day_power * 366;
		$d365 = $_day_power * 365;

		if ($d < 0) {

			if (empty($YRS)) $YRS = array(
				1970 => 0,
				1960 => -315619200,
				1950 => -631152000,
				1940 => -946771200,
				1930 => -1262304000,
				1920 => -1577923200,
				1910 => -1893456000,
				1900 => -2208988800,
				1890 => -2524521600,
				1880 => -2840140800,
				1870 => -3155673600,
				1860 => -3471292800,
				1850 => -3786825600,
				1840 => -4102444800,
				1830 => -4417977600,
				1820 => -4733596800,
				1810 => -5049129600,
				1800 => -5364662400,
				1790 => -5680195200,
				1780 => -5995814400,
				1770 => -6311347200,
				1760 => -6626966400,
				1750 => -6942499200,
				1740 => -7258118400,
				1730 => -7573651200,
				1720 => -7889270400,
				1710 => -8204803200,
				1700 => -8520336000,
				1690 => -8835868800,
				1680 => -9151488000,
				1670 => -9467020800,
				1660 => -9782640000,
				1650 => -10098172800,
				1640 => -10413792000,
				1630 => -10729324800,
				1620 => -11044944000,
				1610 => -11360476800,
				1600 => -11676096000);

			if ($is_gmt) $origd = $d;
			// The valid range of a 32bit signed timestamp is typically from
			// Fri, 13 Dec 1901 20:45:54 GMT to Tue, 19 Jan 2038 03:14:07 GMT
			//

			$lastsecs = 0;
			$lastyear = 1970;
			foreach($YRS as $year => $secs) {
				if ($d >= $secs) {
					$a = $lastyear;
					break;
				}
				$lastsecs = $secs;
				$lastyear = $year;
			}

			$d -= $lastsecs;
			if (!isset($a)) $a = $lastyear;

			for (; --$a >= 0;) {
				$lastd = $d;

				if ($leaf = _adodb_is_leap_year($a)) $d += $d366;
				else $d += $d365;

				if ($d >= 0) {
					$year = $a;
					break;
				}
			}

			$secsInYear = 86400 * ($leaf ? 366 : 365) + $lastd;

			$d = $lastd;
			$mtab = ($leaf) ? $_month_table_leaf : $_month_table_normal;
			for ($a = 13 ; --$a > 0;) {
				$lastd = $d;
				$d += $mtab[$a] * $_day_power;
				if ($d >= 0) {
					$month = $a;
					$ndays = $mtab[$a];
					break;
				}
			}

			$d = $lastd;
			$day = $ndays + ceil(($d+1) / ($_day_power));

			$d += ($ndays - $day+1)* $_day_power;
			$hour = floor($d/$_hour_power);

		} else {
			for ($a = 1970 ;; $a++) {
				$lastd = $d;

				if ($leaf = _adodb_is_leap_year($a)) $d -= $d366;
				else $d -= $d365;
				if ($d < 0) {
					$year = $a;
					break;
				}
			}
			$secsInYear = $lastd;
			$d = $lastd;
			$mtab = ($leaf) ? $_month_table_leaf : $_month_table_normal;
			for ($a = 1 ; $a <= 12; $a++) {
				$lastd = $d;
				$d -= $mtab[$a] * $_day_power;
				if ($d < 0) {
					$month = $a;
					$ndays = $mtab[$a];
					break;
				}
			}
			$d = $lastd;
			$day = ceil(($d+1) / $_day_power);
			$d = $d - ($day-1) * $_day_power;
			$hour = floor($d /$_hour_power);
		}

		$d -= $hour * $_hour_power;
		$min = floor($d/$_min_power);
		$secs = $d - $min * $_min_power;
		if ($fast) {
			return array(
			'seconds' => $secs,
			'minutes' => $min,
			'hours' => $hour,
			'mday' => $day,
			'mon' => $month,
			'year' => $year,
			'yday' => floor($secsInYear/$_day_power),
			'leap' => $leaf,
			'ndays' => $ndays
			);
		}


		$dow = adodb_dow($year,$month,$day);

		return array(
			'seconds' => $secs,
			'minutes' => $min,
			'hours' => $hour,
			'mday' => $day,
			'wday' => $dow,
			'mon' => $month,
			'year' => $year,
			'yday' => floor($secsInYear/$_day_power),
			'weekday' => gmdate('l',$_day_power*(3+$dow)),
			'month' => gmdate('F',mktime(0,0,0,$month,2,1971)),
			0 => $origd
		);
	}
*/

	/*
	 * Accepts unix timestamp and iso date format
	 * ISO format date is converted into unix timestamp before calling date()
	 * @param string Format of date output
	 * @param int/string Date to be converted
	 * @param boolean Ignore timezone
	 * @return string In the format specified by $fmt
	 */
	function date2($fmt, $d=false, $is_gmt=false)
	{	if ( is_numeric($d) ) $this->date($fmt,$d,$is_gmt);

		if ($d !== false) {
			if (!preg_match(
				"|^([0-9]{3,4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})[ -]?(([0-9]{1,2}):?([0-9]{1,2}):?([0-9\.]{1,4}))?|",
				($d), $rr)) return $this->date($fmt,false,$is_gmt);

			if ($rr[1] <= 100 && $rr[2]<= 1) return adodb_date($fmt,false,$is_gmt);

			// h-m-s-MM-DD-YY
			if (!isset($rr[5])) $d = adodb_mktime(0,0,0,$rr[2],$rr[3],$rr[1]);
			else $d = @adodb_mktime($rr[5],$rr[6],$rr[7],$rr[2],$rr[3],$rr[1]);
		}

		return $this->date($fmt,$d,$is_gmt);
	}


	/**
	 * Return formatted date based on timestamp $d
	 * @param string Format of date output
	 * @param int Date to be converted
	 * @param boolean Ignore timezone
	 * @return string In the format specified by $fmt
	 */
	function date($fmt,$d=false,$is_gmt=false)
	{
	static $daylight;
		if ($d === false) {
			return ($is_gmt)? @gmdate($fmt): @date($fmt);
		}
		if ((abs($d) <= 0x7FFFFFFF)) { // check if number in 32-bit signed range
			if (!defined('ADODB_NO_NEGATIVE_TS') || $d >= 0) { // if windows, must be +ve integer
				return ($is_gmt)? @gmdate($fmt,$d): @date($fmt,$d);
			}
		}
		$_day_power = 86400;

//		$arr = $this->_getDate($d,true,$is_gmt);
		$arr = getdate( $d );

//		if (!isset($daylight)) $daylight = function_exists('adodb_daylight_sv');
//		if ($daylight) adodb_daylight_sv($arr, $is_gmt);

		$year = $arr['year'];
		$month = $arr['mon'];
		$day = $arr['mday'];
		$hour = $arr['hours'];
		$min = $arr['minutes'];
		$secs = $arr['seconds'];

		$max = strlen($fmt);
		$dates = '';

		/*
			at this point, we have the following integer vars to manipulate:
			$year, $month, $day, $hour, $min, $secs
		*/
		for ($i=0; $i < $max; $i++) {
			switch($fmt[$i]) {
			case 'T': $dates .= date('T');break;
			// YEAR
			case 'L': $dates .= $arr['leap'] ? '1' : '0'; break;
			case 'r': // Thu, 21 Dec 2000 16:01:07 +0200

				// 4.3.11 uses '04 Jun 2004'
				// 4.3.8 uses  ' 4 Jun 2004'
				$dates .= gmdate('D',$_day_power*(3+$this->dow($year,$month,$day))).', '
					. ($day<10?'0'.$day:$day) . ' '.date('M',mktime(0,0,0,$month,2,1971)).' '.$year.' ';

				if ($hour < 10) $dates .= '0'.$hour; else $dates .= $hour;

				if ($min < 10) $dates .= ':0'.$min; else $dates .= ':'.$min;

				if ($secs < 10) $dates .= ':0'.$secs; else $dates .= ':'.$secs;

				$gmt = adodb_get_gmt_diff();
				$dates .= sprintf(' %s%04d',($gmt<0)?'+':'-',abs($gmt)/36); break;

			case 'Y': $dates .= $year; break;
			case 'y': $dates .= substr($year,strlen($year)-2,2); break;
			// MONTH
			case 'm': if ($month<10) $dates .= '0'.$month; else $dates .= $month; break;
			case 'Q': $dates .= ($month+3)>>2; break;
			case 'n': $dates .= $month; break;
			case 'M': $dates .= date('M',mktime(0,0,0,$month,2,1971)); break;
			case 'F': $dates .= date('F',mktime(0,0,0,$month,2,1971)); break;
			// DAY
			case 't': $dates .= $arr['ndays']; break;
			case 'z': $dates .= $arr['yday']; break;
			case 'w': $dates .= adodb_dow($year,$month,$day); break;
			case 'l': $dates .= gmdate('l',$_day_power*(3+adodb_dow($year,$month,$day))); break;
			case 'D': $dates .= gmdate('D',$_day_power*(3+adodb_dow($year,$month,$day))); break;
			case 'j': $dates .= $day; break;
			case 'd': if ($day<10) $dates .= '0'.$day; else $dates .= $day; break;
			case 'S':
				$d10 = $day % 10;
				if ($d10 == 1) $dates .= 'st';
				else if ($d10 == 2 && $day != 12) $dates .= 'nd';
				else if ($d10 == 3) $dates .= 'rd';
				else $dates .= 'th';
				break;

			// HOUR
			case 'Z':
				$dates .= ($is_gmt) ? 0 : -adodb_get_gmt_diff(); break;
			case 'O':
				$gmt = ($is_gmt) ? 0 : adodb_get_gmt_diff();
				$dates .= sprintf('%s%04d',($gmt<0)?'+':'-',abs($gmt)/36); break;

			case 'H':
				if ($hour < 10) $dates .= '0'.$hour;
				else $dates .= $hour;
				break;
			case 'h':
				if ($hour > 12) $hh = $hour - 12;
				else {
					if ($hour == 0) $hh = '12';
					else $hh = $hour;
				}

				if ($hh < 10) $dates .= '0'.$hh;
				else $dates .= $hh;
				break;

			case 'G':
				$dates .= $hour;
				break;

			case 'g':
				if ($hour > 12) $hh = $hour - 12;
				else {
					if ($hour == 0) $hh = '12';
					else $hh = $hour;
				}
				$dates .= $hh;
				break;
			// MINUTES
			case 'i': if ($min < 10) $dates .= '0'.$min; else $dates .= $min; break;
			// SECONDS
			case 'U': $dates .= $d; break;
			case 's': if ($secs < 10) $dates .= '0'.$secs; else $dates .= $secs; break;
			// AM/PM
			// Note 00:00 to 11:59 is AM, while 12:00 to 23:59 is PM
			case 'a':
				if ($hour>=12) $dates .= 'pm';
				else $dates .= 'am';
				break;
			case 'A':
				if ($hour>=12) $dates .= 'PM';
				else $dates .= 'AM';
				break;
			default:
				$dates .= $fmt[$i]; break;
			// ESCAPE
			case "\\":
				$i++;
				if ($i < $max) $dates .= $fmt[$i];
				break;
			}
		}
		return $dates;
	}

	/**
	 * Returns a timestamp given a GMT/UTC time.
	 * @param int Hour
	 * @param int Minute
	 * @param int Second
	 * @param int Month
	 * @param int Day
	 * @param int Year
	 * @param int $is_dst is not implemented and is ignored
	 * @return int 
	 */
	function gmmktime($hr,$min,$sec,$mon=false,$day=false,$year=false,$is_dst=false)
	{
		return $this->mktime($hr,$min,$sec,$mon,$day,$year,$is_dst,true);
	}

	/**
	 * Return a timestamp given a local time. Originally by jackbbs.
	 * Not a very fast algorithm - O(n) operation. Could be optimized to O(1).
	 * @param int Hour
	 * @param int Minute
	 * @param int Second
	 * @param int Month
	 * @param int Day
	 * @param int Year
	 * @param int $is_dst is not implemented and is ignored
	 * @return int 
	 */
	function mktime($hr,$min,$sec,$mon=false,$day=false,$year=false,$is_dst=false,$is_gmt=false)
	{
		if ($mon === false) {
			return $is_gmt? @gmmktime($hr,$min,$sec): @mktime($hr,$min,$sec);

/* Need to check if this can be deleted
			// for windows, we don't check 1970 because with timezone differences,
			// 1 Jan 1970 could generate negative timestamp, which is illegal
			if (1971 < $year && $year < 2038
				|| !defined('ADODB_NO_NEGATIVE_TS') && (1901 < $year && $year < 2038)
				) {
					return $is_gmt ?
						@gmmktime($hr,$min,$sec,$mon,$day,$year):
						@mktime($hr,$min,$sec,$mon,$day,$year);
				}
*/
		}

		$gmt_different = ($is_gmt) ? 0 : $this->server_offset;

		/*
		# disabled because some people place large values in $sec.
		# however we need it for $mon because we use an array...
		$hr = intval($hr);
		$min = intval($min);
		$sec = intval($sec);
		*/
		$mon = intval($mon);
		$day = intval($day);
		$year = intval($year);


		$year = $this->year_digit_check($year);

		if ($mon > 12) {
			$y = floor($mon / 12);
			$year += $y;
			$mon -= $y*12;
		}
		else if ( $mon <= 0 ) {
			$mon += 12;
			$year -= 1;
		}
		
		$_day_power = 86400;
		$_hour_power = 3600;
		$_min_power = 60;

		$_month_table_normal = array("",31,28,31,30,31,30,31,31,30,31,30,31);
		$_month_table_leaf = array("",31,29,31,30,31,30,31,31,30,31,30,31);

		$_total_date = 0;
		if ($year >= 1970) {
			for ($a = 1970 ; $a <= $year; $a++) {
				$leaf = _adodb_is_leap_year($a);
				if ($leaf == true) {
					$loop_table = $_month_table_leaf;
					$_add_date = 366;
				} else {
					$loop_table = $_month_table_normal;
					$_add_date = 365;
				}
				if ($a < $year) {
					$_total_date += $_add_date;
				} else {
					for($b=1;$b<$mon;$b++) {
						$_total_date += $loop_table[$b];
					}
				}
			}
			$_total_date +=$day-1;
			$ret = $_total_date * $_day_power + $hr * $_hour_power + $min * $_min_power + $sec + $gmt_different;

		} else {
			for ($a = 1969 ; $a >= $year; $a--) {
				$leaf = _adodb_is_leap_year($a);
				if ($leaf == true) {
					$loop_table = $_month_table_leaf;
					$_add_date = 366;
				} else {
					$loop_table = $_month_table_normal;
					$_add_date = 365;
				}
				if ($a > $year) { $_total_date += $_add_date;
				} else {
					for($b=12;$b>$mon;$b--) {
						$_total_date += $loop_table[$b];
					}
				}
			}
			$_total_date += $loop_table[$mon] - $day;

			$_day_time = $hr * $_hour_power + $min * $_min_power + $sec;
			$_day_time = $_day_power - $_day_time;
			$ret = -( $_total_date * $_day_power + $_day_time - $gmt_different);
			if ($ret < -12220185600) $ret += 10*86400; // if earlier than 5 Oct 1582 - gregorian correction
			else if ($ret < -12219321600) $ret = -12219321600; // if in limbo, reset to 15 Oct 1582.
		}
		//print " dmy=$day/$mon/$year $hr:$min:$sec => " .$ret;
		return $ret;
	}

	function gmstrftime($fmt, $ls=false)
	{
		return strftime($fmt,$ls,true);
	}

	function strtotime($time, $now=NULL)
	{
		if($now == NULL) 
			return strtotime($time);
		else
			return strtotime($time, $now);
	}

	// hack - convert to adodb_date
	function strftime($fmt, $ls=false,$is_gmt=false)
	{
	global $ADODB_DATE_LOCALE;

		if ((abs($ls) <= 0x7FFFFFFF)) { // check if number in 32-bit signed range
			if (!defined('ADODB_NO_NEGATIVE_TS') || $ls >= 0) // if windows, must be +ve integer
				return ($is_gmt)? @gmstrftime($fmt,$ls): @strftime($fmt,$ls);
		}

		if (empty($ADODB_DATE_LOCALE)) {
			$tstr = strtoupper(gmstrftime('%c',31366800)); // 30 Dec 1970, 1 am
			$sep = substr($tstr,2,1);
			$hasAM = strrpos($tstr,'M') !== false;

			$ADODB_DATE_LOCALE = array();
			$ADODB_DATE_LOCALE[] =  strncmp($tstr,'30',2) == 0 ? 'd'.$sep.'m'.$sep.'y' : 'm'.$sep.'d'.$sep.'y';
			$ADODB_DATE_LOCALE[]  = ($hasAM) ? 'h:i:s a' : 'H:i:s';

		}
		$inpct = false;
		$fmtdate = '';
		for ($i=0,$max = strlen($fmt); $i < $max; $i++) {
			$ch = $fmt[$i];
			if ($ch == '%') {
				if ($inpct) {
					$fmtdate .= '%';
					$inpct = false;
				} else
					$inpct = true;
			} else if ($inpct) {

				$inpct = false;
				switch($ch) {
				case '0':
				case '1':
				case '2':
				case '3':
				case '4':
				case '5':
				case '6':
				case '7':
				case '8':
				case '9':
				case 'E':
				case 'O':
					/* ignore format modifiers */
					$inpct = true;
					break;

				case 'a': $fmtdate .= 'D'; break;
				case 'A': $fmtdate .= 'l'; break;
				case 'h':
				case 'b': $fmtdate .= 'M'; break;
				case 'B': $fmtdate .= 'F'; break;
				case 'c': $fmtdate .= $ADODB_DATE_LOCALE[0].$ADODB_DATE_LOCALE[1]; break;
				case 'C': $fmtdate .= '\C?'; break; // century
				case 'd': $fmtdate .= 'd'; break;
				case 'D': $fmtdate .= 'm/d/y'; break;
				case 'e': $fmtdate .= 'j'; break;
				case 'g': $fmtdate .= '\g?'; break; //?
				case 'G': $fmtdate .= '\G?'; break; //?
				case 'H': $fmtdate .= 'H'; break;
				case 'I': $fmtdate .= 'h'; break;
				case 'j': $fmtdate .= '?z'; $parsej = true; break; // wrong as j=1-based, z=0-basd
				case 'm': $fmtdate .= 'm'; break;
				case 'M': $fmtdate .= 'i'; break;
				case 'n': $fmtdate .= "\n"; break;
				case 'p': $fmtdate .= 'a'; break;
				case 'r': $fmtdate .= 'h:i:s a'; break;
				case 'R': $fmtdate .= 'H:i:s'; break;
				case 'S': $fmtdate .= 's'; break;
				case 't': $fmtdate .= "\t"; break;
				case 'T': $fmtdate .= 'H:i:s'; break;
				case 'u': $fmtdate .= '?u'; $parseu = true; break; // wrong strftime=1-based, date=0-basde
				case 'U': $fmtdate .= '?U'; $parseU = true; break;// wrong strftime=1-based, date=0-based
				case 'x': $fmtdate .= $ADODB_DATE_LOCALE[0]; break;
				case 'X': $fmtdate .= $ADODB_DATE_LOCALE[1]; break;
				case 'w': $fmtdate .= '?w'; $parseu = true; break; // wrong strftime=1-based, date=0-basde
				case 'W': $fmtdate .= '?W'; $parseU = true; break;// wrong strftime=1-based, date=0-based
				case 'y': $fmtdate .= 'y'; break;
				case 'Y': $fmtdate .= 'Y'; break;
				case 'Z': $fmtdate .= 'T'; break;
				}
			} else if (('A' <= ($ch) && ($ch) <= 'Z' ) || ('a' <= ($ch) && ($ch) <= 'z' ))
				$fmtdate .= "\\".$ch;
			else
				$fmtdate .= $ch;
		}
		//echo "fmt=",$fmtdate,"<br>";
		if ($ls === false) $ls = time();
		$ret = $this->date($fmtdate, $ls, $is_gmt);
		return $ret;
	}

	/**
	 * Converts from Gregorian Year-Month-Day to ISO YearNumber-WeekNumber-WeekDay
	 *
	 * Uses ISO 8601 definitions.
	 * Algorithm from Rick McCarty, 1999 at http://personal.ecu.edu/mccartyr/ISOwdALG.txt
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 * @return string
	 * @access public
	 */
	// Transcribed to PHP by Jesus M. Castagnetto (blame him if it is fubared ;-)
	function gregorianToISO($year, $month, $day) {
		$mnth = array (0,31,59,90,120,151,181,212,243,273,304,334);
		if ($month == 0) {
			$year--;
			$month = 12;
		}
		$y_isleap = $this->is_leap_year($year);
		$y_1_isleap = $this->is_leap_year($year - 1);

		$day_of_year_number = $day + $mnth[$month - 1];
		if ($y_isleap && $month > 2) {
			$day_of_year_number++;
		}
		// find Jan 1 weekday (monday = 1, sunday = 7)
		$yy = ($year - 1) % 100;
		$c = ($year - 1) - $yy;
		$g = $yy + intval($yy/4);
		$jan1_weekday = 1 + intval((((($c / 100) % 4) * 5) + $g) % 7);
		// weekday for year-month-day
		$h = $day_of_year_number + ($jan1_weekday - 1) - 1;
		$weekday = 1 + intval(($h - 1) % 7);
		// find if Y M D falls in YearNumber Y-1, WeekNumber 52 or
		if ($day_of_year_number <= (8 - $jan1_weekday) && $jan1_weekday > 4){
			$yearnumber = $year - 1;
			if ($jan1_weekday == 5 || ($jan1_weekday == 6 && $y_1_isleap)) {
				$weeknumber = 53;
			} else {
				$weeknumber = 52;
			}
		} else {
			$yearnumber = $year;
		}
		// find if Y M D falls in YearNumber Y+1, WeekNumber 1
		if ($yearnumber == $year) {
			if ($y_isleap) {
				$i = 366;
			} else {
				$i = 365;
			}
			if (($i - $day_of_year_number) < (4 - $weekday)) {
				$yearnumber++;
				$weeknumber = 1;
			}
		}
		// find if Y M D falls in YearNumber Y, WeekNumber 1 through 53
		if ($yearnumber == $year) {
			$j = $day_of_year_number + (7 - $weekday) + ($jan1_weekday - 1);
			//$weeknumber = intval($j / 7) + 1; // kludge!!! - JMC
	           $weeknumber = intval($j / 7); // kludge!!! - JMC
			if ($jan1_weekday > 4) {
				$weeknumber--;
			}
		}
		// put it all together
		if ($weeknumber < 10)
			$weeknumber = '0'.$weeknumber;
		return "{$yearnumber}-{$weeknumber}-{$weekday}";
	}

	/**
	 * Get a list of timezones to be worked with
	 */
	function get_timezone_list($use_default = false) {
		static $timezone_options;

		if (!$timezone_options) {
			$timezone_options = array();

			if ($use_default)
				$timezone_options['default'] = '-- Use Default Time Zone --';

			foreach ($GLOBALS['_DATE_TIMEZONE_DATA'] as $tz_key => $tz) {
				$offset = $tz['offset'];

				$absoffset = abs($offset /= 60000);
				$plusminus = $offset < 0 ? '-' : '+';
				$gmtoff = sprintf("GMT%1s%02d:%02d", $plusminus, $absoffset / 60, $absoffset - (intval($absoffset / 60) * 60));
				$tzlongshort = $tz['longname'] . ' (' . $tz['shortname'] . ')';
				$timezone_options[$tz_key] = sprintf('%-28.28s: %-36.36s %s', $tz_key, $tzlongshort, $gmtoff);
			}
		}

		return $timezone_options;
	}

	/**
	 * Per http://www.w3.org/TR/NOTE-datetime
	 */
	function get_iso8601_datetime($timestamp, $user = false) {
		return $this->strftime('%Y-%m-%dT%H:%M:%S%O', $timestamp, $user);
	}

	function get_rfc2822_datetime($timestamp = false, $user = false) {
		if (!$timestamp)
			$timestamp = time();

	# rfc2822 requires dates to be en formatted
		$saved_locale = @setlocale(0);
		@setlocale ('en_US');
	#was return date('D, j M Y H:i:s ', $time) . $this->timezone_offset($time, 'no colon');
		$rv = $this->strftime('%a, %e %b %Y %H:%M:%S', $timestamp, $user). $this->get_rfc2822_timezone_offset($timestamp, $user);

	# switch back to the 'saved' locale
		if ($saved_locale)
			@setlocale ($saved_locale);

		return $rv;
	}

	function get_rfc2822_timezone_offset($time = false, $no_colon = false, $user = false) {
		if ($time === false)
			$time = time();

		$secs = $this->strftime('%Z', $time, $user);

		if ($secs < 0) {
			$sign = '-';

			$secs = -$secs;
		} else {
			$sign = '+';
		}

		$colon = $no_colon ? '' : ':';
		$mins = intval(($secs + 30) / 60);

		return sprintf("%s%02d%s%02d", $sign, $mins / 60, $colon, $mins % 60);
	}

	function set_locale($user = false) {
		static $locale = false;

		if (!$locale) {
			# breaks the RFC 2822 code
			$locale = @setlocale(LC_TIME, $this->get_locale($user));
			#print "<pre>set_locale(): locale=$locale\n</pre>";
		}

		return $locale;
	}

	function daysInMonth( $month, $year ) {
		switch( $month ) {
			case 1:
			case 3:
			case 5:
			case 7:
			case 8:
			case 10:
			case 12:
			case 0: // == 12
				return 31;

			case 4:
			case 6:
			case 9:
			case 11:
				return 30;

			case 2:
				return $this->is_leap_year( $year ) ? 29 : 28;

			default:
				assert( FALSE );
		}
	}

	/**
	* Get a hash of holidays for a given year
	* @param $pYear the year in question
	* @param $pCountryCode -- the country in question - only US is supported currently
	* @return an associative array containing the holidays occuring in the given year they key is a date stamp of the form Y-m-d, the value is the name of the corresponding holiday
	* @access public
	**/
	static function getHolidays( $pYear=NULL, $pCountryCode='US' ) {
		$return = array();

		if( empty( $pYear ) ) {
			$pYear = date( 'Y' );
		}

		switch( $pCountryCode ) {
			case 'UK':
				$return[date( 'Y-m-d', strtotime("-1 week monday", strtotime("1 september $pYear")) + 43200)] = 'Bank Holiday';
				break;
			case 'CA':
				$return[$pYear . '-12-26'] = 'Boxing Day';
				$return[$pYear . '-07-01'] = 'Canada Day';

				// Labor Day - first Monday in September
				$return[date( 'Y-m-d', strtotime( 'monday', strtotime( "September 1, $pYear" ) ) + 43200 )] = 'Labor Day';

				break;
			case 'US':
				//$return[$pYear . '-02-14'] = 'Valentine`s Day';
				$return[$pYear . '-06-14'] = 'Flag Day';
				$return[$pYear . '-07-04'] = 'Independence Day';
				$return[$pYear . '-11-11'] = 'Veteran`s Day';

				// Martin Luther King, Jr. Day - third Monday in January
				$return[date( 'Y-m-d', strtotime( '2 weeks monday', strtotime( "January 1, $pYear" ) ) + 43200 )] = 'Martin Luther King, Jr. Day';

				// Presidents` Day - third Monday in February
				$return[date( 'Y-m-d', strtotime( '2 weeks monday', strtotime( "February 1, $pYear" ) ) + 43200 )] = 'Presidents` Day';

				// Mardi Gras - Tuesday ~47 days before Easter
				//$return[date( 'Y-m-d', strtotime( 'last tuesday 46 days ago', easter_date( $pYear ) ) + 43200 )] = 'Mardi Gras';

				// Memorial Day - last Monday in May
				$return[date( 'Y-m-d', strtotime( '-1 week monday', strtotime( "June 1, $pYear" ) ) + 43200 )] = 'Memorial Day';

				// Labor Day - first Monday in September
				$return[date( 'Y-m-d', strtotime( 'monday', strtotime( "September 1, $pYear" ) ) + 43200 )] = 'Labor Day';

				// Columbus Day - second Monday in October
				$return[date( 'Y-m-d', strtotime( '1 week monday', strtotime( "October 1, $pYear" ) ) + 43200 )] = 'Columbus Day';

				// Thanksgiving - fourth Thursday in November
				$return[date( 'Y-m-d', strtotime( '3 weeks thursday', strtotime( "November 1, $pYear" ) ) + 43200 )] = 'Thanksgiving';

				ksort( $return );
				break;
		}

		// Common to all
		// First off, the simple ones
		$return[$pYear . '-01-01'] = 'New Year`s Day';
		$return[$pYear . '-12-25'] = 'Christmas';

		// Easter - the Sunday after the first full moon which falls on or after the Spring Equinox
		// Thank God (ha!) PHP has a function for that...
		$return[date( 'Y-m-d', easter_date( $pYear ) - (3 * 86400) + 43200 )] = 'Good Friday';
		$return[date( 'Y-m-d', easter_date( $pYear ) + 43200 )] = 'Easter';

		return $return;

	}

	function calculateTimeZoneDate( $dateTime, $fromTZ, $toTZ, $fromLocation = 'North America', $toLocation = 'GMT' ) {

		$retval     = $dateTime;
		$timeStamp  = strtotime( $dateTime );

		$timeZonesArray = array( 'GMT' => array( 'GMT' => +0 // GMT
                                                     ),
				'North America'  =>  array( 'NST' => -3.5, // Newfoundland Standard Time
											'NDT' => -2.5, // Newfoundland Daylight Time
											'AST' => -4, // Atlantic Standard Time
											'ADT' => -3, // Atlantic Daylight Time
											'EST' => -5, // Eastern Standard Time
											'EDT' => -4, // Eastern Daylight Time
											'CST' => -6, // Central Standard Time
											'CDT' => -5, // Central Daylight Time
											'MST' => -7, // Central Daylight Time
											'MDT' => -6, // Mountain Daylight Time
											'PST' => -8, // Pacific Standard Time
											'PDT' => -7, // Pacific Daylight Time
											'AKST' => -9, // Alaska Standard Time
											'AKDT' => -8, // Alaska Daylight Time
											'HAST' => -10, // Hawaii-Aleutian Standard Time
											'HADT' => -9 // Hawaii-Aleutian Daylight Time
											),
				'Australia'      =>  array( 'NFT' => +11.5, // Norfolk (Island) Time
											'EST' => +10, // Eastern Standard Time
											'EDT' => +11, // Eastern Daylight Time
											'CST' => +9.5, // Central Standard Time
											'CDT' => +10.5, // Central Daylight Time
											'WST' => +8, // Western Standard Time
											'CXT' => +7, // Christmas Island Time
											),
				'Europe'         =>  array( 'GMT' => +0, // Greenwich Mean Time
											'BST' => +1, // British Summer Time
											'IST' => +1, // Irish Summer Time
											'WET' => +0, // Western European Time
											'WEST' => +1, // Western European Summer Time
											'CET' => +1, // Central European Time
											'CEST' => +2, // Central European Summer Time
											'EET' => +2, // Eastern European Time
											'EEST' => +3 // Eastern European Summer Time
											),
				'Military'       =>  array( 'Z' => +0, // Zulu Time Zone
											'Y' => -12, // Yankee Time Zone
											'X' => -11, // X-ray Time Zone
											'W' => -10, // Whiskey Time Zone
											'V' => -9, // Victor Time Zone
											'U' => -8, // Uniform Time Zone
											'T' => -7, // Tango Time Zone
											'S' => -6, // Sierra Time Zone
											'R' => -5, // Romeo Time Zone
											'Q' => -4, // Quebec Time Zone
											'P' => -3, // Papa Time Zone
											'O' => -2, // Oscar Time Zone
											'N' => -1, // November Time Zone
											'A' => +1, // Alpha Time Zone
											'B' => +2, // Bravo Time Zone
											'C' => +3, // Charlie Time Zone
											'D' => +4, // Delta Time Zone
											'E' => +5, // Echo Time Zone
											'F' => +6, // Foxtrot Time Zone
											'G' => +7, // Golf Time Zone
											'H' => +8, // Hotel Time Zone
											'I' => +9, // India Time Zone
											'K' => +10, // Kilo Time Zone
											'L' => +11, // Lima Time Zone
											'M' => +12 // Mike Time Zone
											));

			$fromGMTDiff  = $timeZonesArray[$fromLocation][$fromTZ];
			$toGMTDiff    = $timeZonesArray[$toLocation][$toTZ];

			if(( '' != trim( $fromGMTDiff )) && ( '' != trim( $toGMTDiff ))) {
			if( $fromGMTDiff > $toGMTDiff ) {
				$netDiff = $fromGMTDiff - $toGMTDiff;
			} else {
				$netDiff = $toGMTDiff - $fromGMTDiff;
			}
			$retval = date( 'Y-m-d h:i:sa', ( $timeStamp + ( 3600 * $netDiff )));

		}
 		return $retval;

	} // end function calculateTimeZoneDate()
	
}
?>
