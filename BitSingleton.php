<?php
/**
* Base class for all objects where only one object should be created
*
* @package  kernel
* @author   spiderr <spider@bitweaver.org>
*/
// +---------------------------------------------------------------------------+
// | Copyright (c) 2012, bitweaver.org
// +---------------------------------------------------------------------------+
// | All Rights Reserved. See below for details and a complete list of authors.
// | Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. 
// | -> See http://www.gnu.org/copyleft/lesser.html
// |
// | For comments, please use phpdocu.sourceforge.net documentation standards!
// | -> See http://phpdocu.sourceforge.net/
// +---------------------------------------------------------------------------+
// | Authors: spiderr <spider@bitweaver.org>
// +---------------------------------------------------------------------------+

require_once( KERNEL_PKG_PATH . 'BitBase.php' );

abstract class BitSingleton extends BitBase {

	static protected function getSingleInstance() {
		bit_error_log( "BitSingleton::getSingleInstance() must be overridden" );
		die;
	}

    function __construct() {
        // Thou shalt not construct that which is unconstructable!
		parent::__construct();
    }
    protected function __clone() {
        //Me not like clones! Me smash clones!
    }

    public static function getSingleton() {
		$singleton = static::getSingleInstance();
        if (!isset($singleton)) {
            $singleton = new static;
        }
        return $singleton;
    }
}
