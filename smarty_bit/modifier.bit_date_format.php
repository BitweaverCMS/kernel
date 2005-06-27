<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/*
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
require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
function smarty_modifier_bit_date_format($string, $format = "%b %e, %Y", $default_date=null, $tra_format=null)
{
	global $gBitSystem, $user;
    $dc =& $gBitSystem->get_date_converter($user);

    $disptime = $dc->getDisplayDateFromServerDate($string);
    if ($dc->getTzName() != "UTC") $format = preg_replace("/ ?%Z/","",$format);
    else $format = preg_replace("/%Z/","UTC",$format);

    // strftime doesn't do translations right
	//return strftime($format, $disptime);

	global $gBitLanguage; //$gBitLanguage->mLanguage= $gBitSystem->getPreference("language", "en");
	if ($gBitSystem->getPreference("language", "en") != $gBitLanguage->mLanguage && $tra_format) {
		$format = $tra_format;
	}

    $date = new Date($disptime);
    return $date->format($format);
}

/* vim: set expandtab: */

?>
