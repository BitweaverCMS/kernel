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
    $display_days    = true;
    $display_months  = true;
    $display_years   = true;
    $year_as_text    = false;
    /* Display years in reverse order? Ie. 2000,1999,.... */
    $reverse_years   = false;
    /* Should the select boxes be part of an array when returned from PHP?
       e.g. setting it to "birthday", would create "birthday[Day]",
       "birthday[Month]" & "birthday[Year]". Can be combined with prefix */
    $field_array     = null;
    /* <select size>'s of the different <select> tags.
       If not set, uses default dropdown. */
    /* Unparsed attributes common to *ALL* the <select>/<input> tags.
       An example might be in the template: all_extra ='class ="foo"'. */
    $all_extra       = null;

    extract($params);

    global $gBitSystem;
    $field_format = $gBitSystem->getPreference( "short_date_format" );
    if ($time != null)
        $time = $gBitSystem->mServerTimestamp->strftime($field_format, $time);

    if ($readonly) {
        $html_result = $time;
    } else { 
        $html_result = "<input type=\"text\" name=\"".$field_array."\" id=\"".$field_array."\" value=\"".$time."\" />
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
    }

    return $html_result;
}

/* vim: set expandtab: */

?>
