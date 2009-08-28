<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {include_js} function plugin
 *
 * Type:     function
 * Name:     include_js
 * Input:
 *        - file      (required) - fully qualified file to a javascript file
 *		  - nopack	  (optional) - do not pack
 */
function smarty_function_include_js( $params,&$gBitSmarty ) {
	global $gBitSystem;
	$pack = TRUE;
	foreach($params as $key => $val) {
		switch( $key ) {
			case 'file':
				$file = $val;
				break;
			case 'nopack':
				$pack = FALSE;
			default:
				break;
		}			
	}
	if( is_file( $file )) {
		// pack it
		if( $pack && $gBitSystem->isFeatureActive( 'themes_packed_js_css' ) && shell_exec( 'which java' ) ) {
			// start up caching engine - pretend we are themes package
			$BitCache = new BitCache( 'themes', TRUE );

			// get a name for the cache file we're going to store
			$cachefile = md5( $file ).'.js';

			// if the file hasn't been packed and cached yet, we do that now.
			if( !$BitCache->isCached( $cachefile, filemtime( $file ))) {
				// pack and cache it
				$cachedata = shell_exec( 'java -jar '.UTIL_PKG_PATH.'yui/yuicompressor-2.4.2.jar --type js '.$file );
				$BitCache->writeCacheFile( $cachefile, $cachedata );
			}

			// update the file path with new path
			$file = $BitCache->getCacheFile( $cachefile );
		}
		// get file text
		$text = fread( fopen($file,'r'), filesize( $file ) );
	}else{
		// dump a comment to the page
		$text = "if( typeof( console ) != undefined ){ console.log( 'There was an error trying to include js file: ".$file.", it could not be found. Please check the file path.' ); }";
	}
	return $text;
}
