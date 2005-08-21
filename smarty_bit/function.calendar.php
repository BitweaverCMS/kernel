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
// $Id: function.calendar.php,v 1.1.2.2 2005/08/21 21:07:57 squareing Exp $
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:		 function.calendar.php
 * Type:		 function
 * Name:		 calendar
 * Purpose:	outputs a calendar
 * -------------------------------------------------------------
 */
include_once( CALENDAR_PKG_PATH.'Calendar.php' );

function smarty_function_calendar( $params, &$smarty ) {
	$gBitSmarty->display('bitpackage:calendar/calendar_inc.tpl');
}
?>
