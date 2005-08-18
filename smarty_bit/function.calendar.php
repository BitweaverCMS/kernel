<?php
// +----------------------------------------------------------------------+
// | Copyright (C) 2002-2003 Michael Yoon                                 |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU General Public License          |
// | as published by the Free Software Foundation; either version 2       |
// | of the License, or (at your option) any later version.               |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the         |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA            |
// | 02111-1307, USA.                                                     |
// +----------------------------------------------------------------------+
// | Authors: Michael Yoon <michael@yoon.org>                             |
// +----------------------------------------------------------------------+
//
// $Id: function.calendar.php,v 1.1.2.1 2005/08/18 22:27:26 squareing Exp $
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:		 function.calendar.php
 * Type:		 function
 * Name:		 calendar
 * Purpose:	outputs a calendar
 * -------------------------------------------------------------
 */
function smarty_function_calendar($params, &$smarty) {
	global $gBitSmarty;

	$year  = date( 'Y', $params['todate'] );
	$month = date( 'm', $params['todate'] );
	$day   = date( 'd', $params['todate'] );

	if ($day != '') {
		$selected_date = mktime(0, 0, 0, $month, $day, $year);
	}

	// this is the original code, but the behaviour doesn't fit with our calendar
	//$prev_month_end = mktime(0, 0, 0, $month, 0, $year);
	//$next_month_begin = mktime(0, 0, 0, $month + 1, 1, $year);

	$prev_month_end = mktime(0, 0, 0, $month - 1, $day, $year);
	$next_month_begin = mktime(0, 0, 0, $month + 1, $day, $year);

	$month_name = strftime('%B', mktime(0, 0, 0, $month, 1, $year));
	$prev_month_abbrev = strftime('%b', $prev_month_end);
	$prev_month_end_info = getdate($prev_month_end);
	$prev_month = $prev_month_end_info['mon'];
	$prev_month_year = $prev_month_end_info['year'];

	$next_month_abbrev = strftime('%b', $next_month_begin);

	// TODO: make "week starts on" configurable: Monday vs. Sunday
	$day_of_week_abbrevs = array();
	for ($i = 0; $i < 7; $i++) {
		$day_of_week_abbrevs[] = smarty_function_calendar__day_of_week_abbrev($i);
	}

	// Build a two-dimensional array of UNIX timestamps.
	$calendar = array();

	// Start the first row with the final day(s) of the previous month.
	$week = array();
	$month_begin = mktime(0, 0, 0, $month, 1, $year);
	$month_begin_day_of_week = strftime('%w', $month_begin);
	$days_in_prev_month = smarty_function_calendar__days_in_month($prev_month, $prev_month_year);
	for ($day_of_week = 0; $day_of_week < $month_begin_day_of_week; $day_of_week++) {
		$day = $days_in_prev_month - $month_begin_day_of_week + $day_of_week;
		$week[] = mktime(0, 0, 0, $month - 1, $day, $year);
	}

	// Fill in the days of the selected month.
	$days_in_month = smarty_function_calendar__days_in_month($month, $year);
	for ($i = 1; $i <= $days_in_month; $i++) {
		if ($day_of_week == 7) {
			$calendar[] = $week;

			// re-initialize $day_of_week and $week
			$day_of_week = 0;
			unset($week);
			$week = array();
		}
		$week[] = mktime(0, 0, 0, $month, $i, $year);
		$day_of_week++;
	}

	// Fill out the last row with the first day(s) of the next month.
	for ($i = 1; $day_of_week < 7; $i++, $day_of_week++) {
		$week[] = mktime(0, 0, 0, $month + 1, $i, $year);
	}
	$calendar[] = $week;

	// Generate the URL for today, which will be null if $selected_date is
	// today.
	$today = getdate();
	$today_date = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);

	$gBitSmarty->assign('month_name', $month_name);
	$gBitSmarty->assign('selected_date', $selected_date);
	$gBitSmarty->assign('month', $month);
	$gBitSmarty->assign('year', $year);
	$gBitSmarty->assign('prev_month_end', $prev_month_end);
	$gBitSmarty->assign('prev_month_abbrev', $prev_month_abbrev);
	$gBitSmarty->assign('next_month_begin', $next_month_begin);
	$gBitSmarty->assign('next_month_abbrev', $next_month_abbrev);
	$gBitSmarty->assign('day_of_week_abbrevs', $day_of_week_abbrevs);
	$gBitSmarty->assign('calendar', $calendar);

	$gBitSmarty->display('bitpackage:calendar/calendar_inc.tpl');
}

/* Helper functions for the plugin that replicate a subset of what
 * Date_Calc does (as well as MCAL); required for the plugin to work
 * without Date_Calc or MCAL installed.
 *
 * TODO: make it possible to use Date_Calc or MCAL if installed
 */

function smarty_function_calendar__is_leap_year($year) {
	return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0);
}

function smarty_function_calendar__days_in_month($month, $year) {
	switch ($month) {
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
		return smarty_function_calendar__is_leap_year($year) ? 29 : 28;

	default:
		assert(false);
	}
}

/**
 * @param int $day_of_week Sunday is 0, Monday is 1, etc.
 * @return string
 */
function smarty_function_calendar__day_of_week_abbrev($day_of_week) {
	// January 2, 2000 is an arbitrary Sunday that serves as the basis for
	// using strftime() to get the localized name (or abbreviation) of the
	// specified day of week.
	$day = 2 + $day_of_week;
	$timestamp = mktime(0, 0, 0, 1, $day, 2000);

	return strftime('%a', $timestamp);
}
?>
