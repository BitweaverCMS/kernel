<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *  @version $Id: function.bit_select_datetime.php,v 1.1 2007/01/12 23:18:30 hiranchaudhuri Exp $
 * @author hiran  
 */
 
/**** smarty_function_bit_select_datetime
	*
	*	NOTE: This code looks good but needs intensive testing, especially with different date/time formats.
	*
	*	This function generates HTML code that adds a date picker to an HTML form.
	*	Depending on Bitweaver settings (Administration/Themes/Theme Settings)
	*	this can be the ordinary Smarty way of date/time picking (see html_select_date and html_select_time functions)
	*	or a nice javascript calendar.
	*
	*	Parameters:
	*	name	The name of the inputfield. Use this to identify the date/datetime input from other inputs. (will be used for <input name="..., defaults to 'date'.
	*	showtime	defines whether you need a date or datetime picking method. Set to 'true' or 'false', defaults to 'true'.
	*	format	The datetime format used to display/return the timestamp. Defaults to the user's preference on this Bitweaver system.
	*	time	The time value to be displayed, in the format given in the format parameter. Defaults to the current system time.
	*
	*	Usage sample:
	*	<form action="edit.php" method="POST">
	*		{formlabel label="Test JSCalendar"}
	*		{forminput}
	*			{bit_select_datetime name="mydate2" time=$gContent->mInfo.start}
	*		{/forminput}
	*	</form>
	*
	*	Later, in edit.php, use this code to obtain the timestamp in UTC:
	*	$timestamp = $gBitSystem->mServerTimestamp->getUTCFromDisplayDate(_REQUEST['mydate2'])
	*
	*/
function smarty_function_bit_select_datetime($params, &$gBitSmarty) {
	global $gBitSystem;

	// Default values
	$name         = 'date';                   // ID of the input field
	$format       = $gBitSystem->getConfig( 'site_short_date_format' ).' '.$gBitSystem->getConfig( 'site_short_time_format' );      // date format used
	$showtime     = 'true';                   //true: show time; false: pick date only
	$time         = time();                   // override the currently set date
	
	//extract actual parameters from the params hashmap.
	extract( $params );
	
	if( $gBitSystem->isFeatureActive( 'site_use_jscalendar' ) ) {
	
		// A readonly field will be used to display the currently selected value.
		//A button besides the field will bring up the calendar (style similar to other PIM rich client applications)
		//It is the readonly input field that will be evaluated back on the server
	
		$format = preg_replace( "/%Z/", "", $format );  // JSCalendar does not know about time zones
		$html_result = "<input type=\"text\" name=\"$name\" id=\"${name}_id\" value=\"$time\" readonly /><button type=\"reset\" id=\"${name}_button\">...</button>\n";
		$html_result = $html_result . "<script type=\"text/javascript\">\n";
		$html_result = $html_result . "    Calendar.setup({\n";
		$html_result = $html_result . "        date        : \"$time\",\n";
		$html_result = $html_result . "        inputField  :    \"${name}_id\",      // id of the input field\n";
		$html_result = $html_result . "        ifFormat    :    \"${format}\",       // format of the input field\n";
		$html_result = $html_result . "        showsTime   :    $showtime,            // will display a time selector\n";
		$html_result = $html_result . "        button      :    \"${name}_button\",   // trigger for the calendar (button ID)\n";
		$html_result = $html_result . "        singleClick :    true,           // double-click mode\n";
		$html_result = $html_result . "        step        :    1                // show all years in drop-down boxes (instead of every other year as default)\n";
		$html_result = $html_result . "    });\n";
		$html_result = $html_result . "</script>\n";
		return $html_result;
	} else {
		require_once $gBitSmarty->_get_plugin_filepath( 'function', 'html_select_date' );
		require_once $gBitSmarty->_get_plugin_filepath( 'function', 'html_select_time' );
		
		// we use html_select_date and html_select_time to pick a date, which generate a number of select fields.
		//On every change a hidden field will be updated via javascript.
		//it's the hidden field that is evaluated back on the server.
		
		$pDate = array (
			'prefix' => $name,
			'all_extra' => "onchange=\"bit_select_datetime_${name}()\"",
			'time' => $time
		);
		$pTime = array (
			'prefix' => $name,
			'all_extra' => "onchange=\"bit_select_datetime_${name}()\"",
			'display_seconds' => false,
			'time' => $time
		);
		
		$html_result = "<input type=\"hidden\" name=\"$name\" value=\"$time\">";
		$html_result = $html_result . smarty_function_html_select_date( $pDate, $gBitSmarty );
		if($showtime=='true') {
			$html_result = $html_result . smarty_function_html_select_time( $pTime, $gBitSmarty );
			$html_result = $html_result . "<script type=\"text/javascript\">\n";
			$html_result = $html_result . "    function bit_select_datetime_${name} () {\n";
			$html_result = $html_result . "        document.getElementsByName(\"${name}\")[0].value = document.getElementsByName(\"${name}Year\")[0].value+\"-\"+document.getElementsByName(\"${name}Month\")[0].value+\"-\"+document.getElementsByName(\"${name}Day\")[0].value+\" \"+document.getElementsByName(\"${name}Hour\")[0].value+\":\"+document.getElementsByName(\"${name}Minute\")[0].value;\n";
			$html_result = $html_result . "    }\n";
			$html_result = $html_result . "</script>\n";
		} else {
			$html_result = $html_result . "<script type=\"text/javascript\">\n";
			$html_result = $html_result . "    function bit_select_datetime_${name} () {\n";
			$html_result = $html_result . "        document.getElementsByName(\"${name}\")[0].value = document.getElementsByName(\"${name}Year\")[0].value+\"-\"+document.getElementsByName(\"${name}Month\")[0].value+\"-\"+document.getElementsByName(\"${name}Day\")[0].value;\n";
			$html_result = $html_result . "    }\n";
			$html_result = $html_result . "</script>\n";
		}
		return $html_result;
	}
}
?>