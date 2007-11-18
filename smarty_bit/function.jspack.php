<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * this function will output the URL to a given compressed and cached js file.
 * NOTE: if you use the util package, it will automatically insert the javascript/ subdir
 * 
 * @param string $pParams[ipackage] package the javascript file is in. this will default to: 'util'. e.g.: wiki
 * @param string $pParams[ifile] subdir and filename of the file you wish to pack and cache. e.g.: libs/jsfile.js
 * @param string $pParams[defer] includes defer='defer'
 * @param object $gBitSmarty 
 * @access public
 * @return URL to cached javascript file
 */
function smarty_function_jspack( $pParams, &$gBitSmarty ) {
	// make sure we have a file to pack
	if( empty( $pParams['ifile'] )) {
		die();
	}

	if( empty( $pParams['ipackage'] )) {
		$pParams['ipackage'] = 'util';
	}

	// get the full path to the file we want to pack - insert javasscript/ into path when we're getting stuff in util
	$jsfile = constant( strtoupper( $pParams['ipackage'] ).'_PKG_PATH' ).(( $pParams['ipackage'] == 'util' ) ? 'javascript/' : '' ).$pParams['ifile'];

	if( is_file( $jsfile )) {
		// get a name for the cache file we're going to store
		$cachefile = $pParams['ipackage'].'_'.str_replace( '/', '_', $pParams['ifile'] );

		require_once( KERNEL_PKG_PATH.'BitCache.php' );
		$bitCache = new BitCache( 'javascript', TRUE );

		// if the file hasn't been packed and cached yet, we do that now.
		if( !$bitCache->isCached( $cachefile, filemtime( $jsfile ))) {
			/*
			 * params of the constructor :
			 * $script:       the JavaScript to pack, string.
			 * $encoding:     level of encoding, int or string :
			 *                0,10,62,95 or 'None', 'Numeric', 'Normal', 'High ASCII'.
			 *                default: 62.
			 * $fastDecode:   include the fast decoder in the packed result, boolean.
			 *                default : true.
			 * $specialChars: if you have flagged your private and local variables
			 *                in the script, boolean.
			 *                default: false.
			 */
			require_once( UTIL_PKG_PATH.'javascript/class.JavaScriptPacker.php' );
			$packer = new JavaScriptPacker( file_get_contents( $jsfile ), 'Normal', TRUE, FALSE );
			$bitCache->writeCacheFile( $cachefile, $packer->pack() );
		}

		$defer = !empty( $pParams['defer'] ) ? " defer='".$pParams['defer']."'" : "";
		return '<script'.$defer.' type="text/javascript" src="'.$bitCache->getCacheUrl( $cachefile ).'"></script>';
	} else {
		return "<!-- ".tra( 'not a valid file: ' ).$pParams['ifile']." -->";
	}
}
?>
