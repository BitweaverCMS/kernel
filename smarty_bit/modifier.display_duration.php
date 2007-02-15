<?php
/**
 * @package Smarty
 * @subpackage plugins
 */

/**
 * basic function to convert a number of seconds into a human readable format
 * 
 * @param array $pDuration Duration of event in seconds
 * @access public
 * @return TRUE on success, FALSE on failure - mErrors will contain reason for failure
 */
function smarty_modifier_display_duration( $pDuration ) {
	$units = array(
		'month'  => 60 * 60 * 24 * 7 * 4,
		'week'   => 60 * 60 * 24 * 7,
		'day'    => 60 * 60 * 24,
		'hour'   => 60 * 60,
		'min'    => 60,
		'sec'    => 1,
	);

	foreach( $units as $unit => $secs ) {
		$duration[$unit] = 0;
		if( $pDuration > $secs ) {
			$duration[$unit] = floor( $pDuration / $secs );
			$pDuration = $pDuration % $secs;
		}
	}

	$ret  = !empty( $duration['month'] ) ? $duration['month'].tra( 'month(s)' ).' ' : '';
	$ret .= !empty( $duration['week'] )  ? $duration['week'] .tra( 'week(s)' ).' '  : '';
	$ret .= !empty( $duration['day'] )   ? $duration['day']  .tra( 'day(s)' ).' '   : '';
	$ret .= str_pad( $duration['hour'], 2, 0, STR_PAD_LEFT ).':'.str_pad( $duration['min'], 2, 0, STR_PAD_LEFT ).':'.str_pad( $duration['sec'], 2, 0, STR_PAD_LEFT );
	return $ret;
}
?>
