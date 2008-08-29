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
 * -------------------------------------------------------------
 */
function smarty_modifier_bit_date_format( $pString, $format = "%b %e, %Y", $pTraFormat = "%b %e, %Y" ) {
	global $gBitSystem, $user, $gBitLanguage;

	if( empty( $pString )) {
		return '';
	}

	// we translate the entire date format string for total control
	if( $gBitSystem->getConfig( "bitlanguage", "en" ) != $gBitLanguage->mLanguage ) {
		$format = tra( $pTraFormat );
	}

	if( $gBitSystem->get_display_offset() ) {
		$format = preg_replace( "/ ?%Z/", "", $format );
	} else {
		$format = preg_replace( "/%Z/", "UTC", $format );
	}

	$disptime = $gBitSystem->mServerTimestamp->getDisplayDateFromUTC( $pString );
	return $gBitSystem->mServerTimestamp->strftime( $format, $disptime, TRUE );
}
?>
