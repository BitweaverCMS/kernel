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
function smarty_function_jscalendar($params, &$gBitSmarty) {
	global $gBitSystem;
	if( $gBitSystem->isFeatureActive( 'site_use_jscalendar' ) ) {
		//require_once $gBitSmarty->_get_plugin_filepath('shared', 'make_timestamp');
		//require_once $gBitSmarty->_get_plugin_filepath('function', 'html_options');

		// Default values
		$inputField   = '';      // ID of the input field
		$fieldFormat  = '%s';    // format of the input field
		$electric     = 'false'; // ID of the span where the date is to be shown
		$time         = time();  // override the currently set date
		$onUpdate     = '';      // execute the following javascript function when a link is pressed
		$daFormat     = $gBitSystem->getConfig( 'site_short_date_format' ).' '.$gBitSystem->getConfig( 'site_short_time_format' ); // format of output date
		$displayArea  = '';

		// override default values
		extract( $params );

		$time = $gBitSystem->mServerTimestamp->getDisplayDateFromUTC( $time );
		$time = strftime( "%m/%d/%Y %H:%M", $time );

		if( $readonly ) {
			$html_result = $time;
		} else {
			$html_result =
				"<script type=\"text/javascript\">//<![CDATA[
					Calendar.setup({
						date        : \"$time\",
						inputField  : \"$inputField\",
						ifFormat    : \"$fieldFormat\",
						daFormat    : \"$daFormat\",
						displayArea : \"$displayArea\",
						electric    : $electric,
						onUpdate    : $onUpdate
					});
				//]]></script>"
			;
		}

		return $html_result;
	} else {
		return '';
	}
}
?>
