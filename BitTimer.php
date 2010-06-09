<?php
/**
 * @version $Header$
 * Basic processes timer
 *
 * @package kernel
 */
 
/**
 * @package kernel
 */
class BitTimer {
	function parseMicro( $micro ) {
		list( $micro, $sec ) = explode( ' ', microtime() );
		return $sec + $micro;
	}

	function start( $timer = 'default' ) {
		$this->mTimer[$timer] = $this->parseMicro( microtime() );
	}

	function stop( $timer = 'default' ) {
		return $this->current( $timer );
	}

	function elapsed( $timer = 'default' ) {
		return $this->parseMicro( microtime() ) - $this->mTimer[$timer];
	}
}

/**
 * Create timer
 */
global $gBitTimer;
$gBitTimer = new BitTimer();
$gBitTimer->start();
?>
