<?php
/**
 * Base class for all objects where only one object should be created
 *
 * @version $Header$
 *
 * Copyright (c) 2004 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * Virtual base class (as much as one can have such things in PHP) for all
 * derived tikiwiki classes that require database access.
 *
 * created 2004/8/15
 *
 * @author spider <spider@steelsun.com>
 * @package  kernel
 */

/**
 * required setup
 */
require_once( KERNEL_PKG_PATH . 'BitBase.php' );

/**
 * @package  kernel
 */
abstract class BitSingleton extends BitBase implements BitCacheable {

	protected static $singletons = NULL;

	function __construct() {
		parent::__construct();
	}

	public static function loadSingleton( $pVarName = NULL ) {
		$class = static::getClass();
		$globalVarName = !empty( $pVarName ) ? $pVarName : 'g'.$class;
		global $$globalVarName;

		if( !($$globalVarName = static::loadFromCache( 'Singleton' )) ) {
			$$globalVarName = new $class;
		}
		if(!isset(static::$singletons[$globalVarName])) {
			static::$singletons[$globalVarName] = $$globalVarName;
			global $gBitSmarty;
			if( $gBitSmarty ) {
				$gBitSmarty->assignByRef( $globalVarName, $$globalVarName );
			}
		}
		return static::$singletons[$globalVarName];
	}

	public function getCacheKey() {
		return 'Singleton';
	}

	public static function isCacheableClass() {
		return TRUE;
	}

	public function isCacheableObject() {
		return TRUE;
	}

}

// I don't remember where I found this, but this is to allow php < 5.3 to use this method.
if (! function_exists ( 'get_called_class' )) {
	function get_called_class($bt = FALSE, $l = 1) {
		if (! $bt) {
			$bt = debug_backtrace ();
		}
		if (! isset ( $bt [$l] )) {
			throw new Exception ( "Cannot find called class -> stack level too deep." );
		}
		if (! isset ( $bt [$l] ['type'] )) {
			throw new Exception ( 'type not set' );
		} else {
			switch ($bt [$l] ['type']) {
				case '::' :
					$lines = file ( $bt [$l] ['file'] );
					$i = 0;
					$callerLine = '';
					do {
						$i ++;
						$callerLine = $lines [$bt [$l] ['line'] - $i] . $callerLine;
					} while ( stripos ( $callerLine, $bt [$l] ['function'] ) === FALSE );
					preg_match ( '/([a-zA-Z0-9\_]+)::' . $bt [$l] ['function'] . '/', $callerLine, $matches );
					if (! isset ( $matches [1] )) {
						// must be an edge case.
						throw new Exception ( "Could not find caller class: originating method call is obscured." );
					}
					switch ($matches [1]) {
						case 'self' :
						case 'parent' :
							return get_called_class ( $bt, $l + 1 );
						default :
							return $matches [1];
					}
				// won't get here.
				case '->' :
					switch ($bt [$l] ['function']) {
						case '__get' :
							// edge case -> get class of calling object
							if (! is_object ( $bt [$l] ['object'] )) {
								throw new Exception ( "Edge case fail. __get called on non object." );
							}
							return get_class ( $bt [$l] ['object'] );
						default :
							return $bt [$l] ['class'];
					}

				default :
					throw new Exception ( "Unknown backtrace method type" );
			}
		}
	}
}
