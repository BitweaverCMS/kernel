<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * moduleinc 
 * 
 * Usage: add to the body of any .tpl file
 * Example: {inlinemodule file="_custom:custom/my_custom_module" cache_time=600}
 *
 * Note: currently only supports custom modules generated in themes package, 
 * could support any module with more work
 * moduleinc mirrors process in modules_inc.php except $r is replaced with $pParams
 * see modules_inc.php to expand support to other modules 
 *
 * @param array $pParams 
 * @param string $pParams['module_rsrc'] the full name of the template, example: _custom:custom/my_custom_module 
 * @param integer $pParams['cache_time'] seconds the template will be cached
 */
function smarty_function_moduleinc($pParams, &$gBitSmarty) {
	global $gBitSystem, $gBitThemes;

	// go through some hassle here in consideration of a future day when this handles any module
	list( $package, $template ) = split(  '/', $pParams['module_rsrc'] );

	if( $package == '_custom:custom' ) {
		global $gBitLanguage;

		// We're gonna run our own cache mechanism for user_modules
		// the cache is here to avoid calls to consumming queries,
		// each module is different for each language because of the strings
		$cacheDir = TEMP_PKG_PATH.'modules/cache/';
		if( !is_dir( $cacheDir )) {
			mkdir_p( $cacheDir );
		}
		$cachefile = $cacheDir.'_custom.'.$gBitLanguage->mLanguage.'.'.$template.'.tpl.cache';

		if( !empty( $pParams["cache_time"] ) && file_exists( $cachefile ) && !(( $gBitSystem->getUTCTime() - filemtime( $cachefile )) > $pParams["cache_time"] )) {
			$fp = fopen( $cachefile, "r" );
			$data = fread( $fp, filesize( $cachefile ));
			fclose( $fp );
			print( $data );
		} else {
			if( $moduleParams = $gBitThemes->getCustomModule( $template )) {
				$moduleParams = array_merge( $pParams, $moduleParams );
				$gBitSmarty->assign_by_ref( 'moduleParams', $moduleParams );
				$data = $gBitSmarty->fetch( 'bitpackage:themes/custom_module.tpl' );

				if( !empty( $pParams["cache_time"] ) ) {
					// write to chache file
					$fp = fopen( $cachefile, "w+" );
					fwrite( $fp, $data, strlen( $data ));
					fclose( $fp );
				}
				print( $data );
			}
		}
		unset( $data );
	} 
}
