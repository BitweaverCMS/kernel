<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
// $Header$
/**
 * \brief Smarty {bitmodule}{/bitmodule} block handler
 *
 * To make a module it is enough to place smth like following
 * into corresponding mod-name.tpl file:
 * \code
 *  {bitmodule name="module_name" title="Module title"}
 *    <!-- module Smarty/HTML code here -->
 *  {/bitmodule}
 * \endcode
 *
 * This block may (can) use 2 Smarty templates:
 *  1) module.tpl = usual template to generate module look-n-feel
 *  2) module-error.tpl = to generate diagnostic error message about
 *     incorrect {bitmodule} parameters

\Note
error was used only in case the name was not there.
I fixed that error case. -- mose
 
 */
function smarty_block_bitmodule( $pParams, $pContent, &$gBitSmarty) {
	if( empty( $pContent )) {
		return '';
	} else {
		$pParams['data'] = $pContent;
	}

//	if( empty( $pParams['title'] )) {
//		$pParams['title'] = substr( $pContent, 0, 12 )."&hellip;";
//	}

//	if( empty( $pParams['name'] )) {
//		$pParams['name'] = preg_replace( "/[^-_a-zA-Z0-9]/", "", $pParams['title'] );
//	}

	// this is outdated and will not work with our serialised cookies - xing
	/*
	if( $_COOKIE[$name] == 'c' ) {
		$pParams['toggle_state'] = 'none';
	} else {
		$pParams['toggle_state'] = 'block';
	}
	 */
	
	$pParams['name'] = preg_replace( "/[^a-zA-Z0-9\\-\\_]/", "", $pParams['name'] );
	
	$gBitSmarty->assign( 'modInfo', $pParams );
	return $gBitSmarty->fetch('bitpackage:themes/module.tpl');
}
?>
