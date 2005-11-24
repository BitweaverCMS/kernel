<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {jscalendar} plugin
 *
 * Type:     function<br>
 * Name:     jscalendar<br>
 * Purpose:  Prints the dropdowns for date selection.
 *
 * ChangeLog:<br>
 *           - 1.0 initial release
 * @version 1.0
 * @author   Stephan Borg
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_jscalendar($params, &$gBitSmarty)
{
    require_once $gBitSmarty->_get_plugin_filepath('shared','make_timestamp');
    require_once $gBitSmarty->_get_plugin_filepath('function','html_options');
    /* Default values. */
    $prefix          = "";
    $start_year      = strftime("%Y");
    $end_year        = $start_year;
    $display_days    = true;
    $display_months  = true;
    $display_years   = true;
    $month_format    = "%B";
    /* Write months as numbers by default  GL */
    $month_value_format = "%m";
    $day_format      = "%e";
    /* Write day values using this format MB */
    $day_value_format = "%d";
    $year_as_text    = false;
    /* Display years in reverse order? Ie. 2000,1999,.... */
    $reverse_years   = false;
    /* Should the select boxes be part of an array when returned from PHP?
       e.g. setting it to "birthday", would create "birthday[Day]",
       "birthday[Month]" & "birthday[Year]". Can be combined with prefix */
    $field_array     = null;
    /* <select size>'s of the different <select> tags.
       If not set, uses default dropdown. */
    $day_size        = null;
    $month_size      = null;
    $year_size       = null;
    /* Unparsed attributes common to *ALL* the <select>/<input> tags.
       An example might be in the template: all_extra ='class ="foo"'. */
    $all_extra       = null;
    /* Separate attributes for the tags. */
    $day_extra       = null;
    $month_extra     = null;
    $year_extra      = null;
    /* Order in which to display the fields.
       "D" -> day, "M" -> month, "Y" -> year. */
    $field_order      = 'MDY';
    /* String printed between the different fields. */
    $field_separator = "\n";
	$time = time();


    extract($params);

  	// If $time is not in format yyyy-mm-dd
  	if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $time)) {
  		// then $time is empty or unix timestamp or mysql timestamp
  		// using smarty_make_timestamp to get an unix timestamp and
  		// strftime to make yyyy-mm-dd
  		$time = strftime('%Y-%m-%d', smarty_make_timestamp($time));
  	}
  	// Now split this in pieces, which later can be used to set the select
  	$time = explode("-", $time);
  
  	// make syntax "+N" or "-N" work with start_year and end_year
  	if (preg_match('!^(\+|\-)\s*(\d+)$!', $end_year, $match)) {
  		if ($match[1] == '+') {
  			$end_year = strftime('%Y') + $match[2];
  		} else {
  			$end_year = strftime('%Y') - $match[2];
  		}
  	}
  	if (preg_match('!^(\+|\-)\s*(\d+)$!', $start_year, $match)) {
  		if ($match[1] == '+') {
  			$start_year = strftime('%Y') + $match[2];
  		} else {
  			$start_year = strftime('%Y') - $match[2];
  		}
  	}
  
    $field_order = strtoupper($field_order);

    $html_result = $month_result = $day_result = $year_result = "";

    if ($display_months) {
    }

    if ($display_days) {
    }

    if ($display_years) {
    }

    // Loop thru the field_order field
    for ($i = 0; $i <= 2; $i++){
      $c = substr($field_order, $i, 1);
      switch ($c){
        case 'D':
            $field_format .= $day_format . " ";
            break;

        case 'M':
            $field_format .= $month_format . " ";
            break;

        case 'Y':
            $field_format .= "%Y ";
            break;
      }
    }
    trim($field_format);

    $html_result = "<input type=\"text\" name=\"".$field_array."\" id=\"".$field_array."\" readonly=\"1\" />
                <img src=\"".JSCALENDAR_PKG_URL."img.gif\" id=\"f_trigger_".$field_array."\" style=\"cursor: pointer;\" title=\"Date selector\" />
                                <script type=\"text/javascript\">//<![CDATA[
    Calendar.setup({
        inputField     :    \"".$field_array."\",     // id of the input field
        ifFormat       :    \"".$field_format."\",      // format of the input field
        button         :    \"f_trigger_".$field_array."\",  // trigger for the calendar (button ID)
        align          :    \"Tl\",           // alignment (defaults to \"Bl\")
        singleClick    :    true
    });
//]]></script>";

    return $html_result;
}

/* vim: set expandtab: */

?>
