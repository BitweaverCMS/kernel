<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * required setup
 */
global $gBitSmarty;
require_once $gBitSmarty->_get_plugin_filepath('shared','make_timestamp');

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     bit_date_format
 * Purpose:  format datestamps via strftime, (timezone adjusted to administrator specified timezone)
 * Input:    string: input date string
 *           format: strftime format for output
 *           default_date: default date if $string is empty
 * -------------------------------------------------------------
 */
function smarty_modifier_bit_date_format($string, $format = "%b %e, %Y", $default_date=null, $tra_format=null)
{
	global $gBitSystem, $user;

	if ( $gBitSystem->mServerTimestamp->get_display_offset()) {
                $format = preg_replace("/ ?%Z/","",$format);
        } else {
                $format = preg_replace("/%Z/","UTC",$format);
        }

	$disptime = $gBitSystem->mServerTimestamp->getDisplayDateFromUTC($string);
	
	global $gBitLanguage; //$gBitLanguage->mLanguage= $gBitSystem->getPreference("language", "en");
	if ($gBitSystem->getPreference("language", "en") != $gBitLanguage->mLanguage && $tra_format) {
		$format = $tra_format;
	}

	return $gBitSystem->mServerTimestamp->strftime($format, $disptime, true);
}

/* vim: set expandtab: */

?>
