<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {libertypagination} function plugin
 *
 * Type:     function
 * Name:     libertypagination
 * Input:    numPages			Number of pages in total
 *           page				current page
 *           pgnName (optional)	parameter name used by script to find page you're on. defaults to page
 *           ianchor (optional)	set an anchor
 * Output:   url of the form: $PHP_SELF?attribute1=value1&attribute2=value2
 */

function smarty_function_libertypagination($params, &$gBitSmarty) {
	if( isset( $params['hash'] ) && is_array( $params['hash'] ) ) {
		$params = $params['hash'];
	}

	if( isset( $params['url'] ) ) {
		parse_str( preg_replace( "/.*\?/", "", $params['url'] ), $urlParams );
		$params = array_merge( $urlParams, $params );
	}
	$pgnName = isset( $params['pgnName'] ) ? $params['pgnName'] : 'page';
	$pgnVars = '';

	$omitParams = array( 'numPages', 'url', 'page', 'pgnName', 'ianchor' );
	foreach( $params as $form_param => $form_val ) {
		if ( !empty( $form_val ) && !in_array( $form_param, $omitParams ) ) {
			$pgnVars .= "&amp;".$form_param."=".$form_val;
			$pgnHidden[$form_param] = $form_val;
		}
	}

	$pgnVars .= ( !empty( $params['ianchor'] ) ? '#'.$params['ianchor'] : '' );

    for( $pageCount = 1; $pageCount < $params['numPages']+1; $pageCount++ ) {
		if( $pageCount != $params['page'] ) {
			$pages[] = '<a href="'.$_SERVER['PHP_SELF'].'?'.$pgnName.'='.$pageCount.$pgnVars.'">'.( $pageCount ).'</a>';
		} else {
			$pages[] = '<strong>'.$pageCount.'</strong>';
		}
	}

	if( $params['numPages'] > 1 ) {
		$gBitSmarty->assign( 'page', $params['page'] );
		$gBitSmarty->assign( 'pgnName', $pgnName );
		$gBitSmarty->assign( 'pgnVars', $pgnVars );
		$gBitSmarty->assign( 'pgnHidden', $pgnHidden );
	    $gBitSmarty->assign( 'pgnPages', $pages );
	    $gBitSmarty->assign( 'numPages', $params['numPages'] );
	    $gBitSmarty->display( 'bitpackage:liberty/libertypagination.tpl' );
	}
}
?>
