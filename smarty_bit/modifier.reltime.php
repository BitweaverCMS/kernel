<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_modifier_reltime
 */
function smarty_modifier_reltime( $pTimeStamp, $pMode = 'long', $pFallback = 'bit_short_datetime' ) {
	global $gBitSystem, $gBitSmarty;

	// if this feature is not desired, we simply don't use it.
	if( !$gBitSystem->isFeatureActive( 'site_display_reltime' ) ) {
		require_once $gBitSmarty->_get_plugin_filepath( 'modifier', $pFallback );
		$pFallback = "smarty_modifier_$pFallback";
		return $pFallback( $pTimeStamp );
	}

	$min   = 60;
	$hour  = $min  * 60;
	$day   = $hour * 24;
	$week  = $day  * 7;

	$strf  = "H:i";

	if( !is_numeric( $pTimeStamp ) ) {
		return $pTimeStamp;
	}

	$delta = $gBitSystem->mServerTimestamp->getUTCTime() - $pTimeStamp;

	if( $delta < 0 ) {
		$delta = -$delta;
		return tra( "In the future" ).": ";
	}

	if( $delta < 1 ) {
		// seconds
		return tra( "within the last second" );
	} elseif( $delta < $min ) {
		// minutes
		return tra( "within the last minute" );
	} elseif( $delta < $hour ) {
		// hours
		if( $delta < $min * 2 ) {
			return tra( "one minute ago" );
		} else {
			return round( $delta / $min )." ".tra( "minutes ago" );
		}
	} elseif( $delta < $day ) {
		// up to a day
		if( $delta < $hour * 1.1 ) {
			return tra( "one hour ago" );
		} elseif( $delta < $day ) {
			$delta_hours = floor( ( $delta - ( floor( $delta / $hour ) * $hour ) ) / $min );
			if( $pMode == 'short' ) {
				return floor( $delta / $hour )."h {$delta_hours}m ago";
			}
			if( floor( $delta / $hour ) > 1 ){
				if( $delta_hours > 1 ){
					return floor( $delta / $hour )." hours {$delta_hours} minutes ago";
				} else {
					return floor( $delta / $hour )." hours {$delta_hours} minute ago";
				}
			} else {
				if( $delta_hours > 1 ){
					return floor( $delta / $hour )." hour {$delta_hours} minutes ago";
				} else {
					return floor( $delta / $hour )." hour {$delta_hours} minute ago";
				}
			}

		} else {

			return round( $delta / $hour )." ".tra( "hour(s) ago" );
		}
	} elseif( $delta < $week ) {
		// up to a week
		if( $delta < $day * 2 ) {
			return tra( "Yesterday" )." ".date( $strf, $pTimeStamp );
		} else {
			if( $pMode == 'short' ) {
				return date( 'D '.$strf, $pTimeStamp );
			}
			return tra( date( 'l', $pTimeStamp ) )." ".date( $strf, $pTimeStamp );
		}
	} else {
		// anything longer than a week
		require_once $gBitSmarty->_get_plugin_filepath( 'modifier', $pFallback );
		$pFallback = "smarty_modifier_$pFallback";
		return $pFallback( $pTimeStamp );
	}
}
?>
