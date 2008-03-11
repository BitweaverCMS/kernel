<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     display_bytes
 * Purpose:  show an integer in a human readable Byte size with optional resolution
 * Example:  {$someFile|filesize|display_bytes:2}
 * -------------------------------------------------------------
 */
function smarty_modifier_ordinal_suffix( $pNum ) {

	// first convert to string if needed
	$ret = (string) $pNum;
	// now we grab the last digit of the number
	$last_digit = substr($ret, -1, 1);
	// if the string is more than 2 chars long, we get
	// the second to last character to evaluate
	if (strlen($ret)>1) {
		$next_to_last = substr($ret, -2, 1);
	} else {
		$next_to_last = "";
	}
	// now iterate through possibilities in a switch
	switch($last_digit) {
		case "1":
			// testing the second from last digit here
			switch($next_to_last) {
				case "1":
					$suffix ="th";
					break;
				default:
					$suffix ="st";
			}
			break;
		case "2":
			// testing the second from last digit here
			switch($next_to_last) {
				case "1":
					$suffix ="th";
					break;
				default:
					$suffix ="nd";
			}
			break;
		// if last digit is a 3
		case "3":
			// testing the second from last digit here
			switch($next_to_last) {
				case "1":
					$suffix ="th";
					break;
				default:
					$suffix ="rd";
			}
			break;
		// for all the other numbers we use "th"
		default:
			$suffix ="th";
	}

	// finally, return our string with it's new suffix
	return $pNum.tra( $suffix );

}
?>
