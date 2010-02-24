<?php
/**
 * Smarty {libertypagination} function plugin
 *
 * This provides a means of paging through longer lists of data using an up and down arrow.
 * In addition, if the 'site_direct_pagination' feature is enabled, then a direct page number can be entered jump directly to
 *
 * Type:     function<br>
 * Name:     libertypagination<br>
 * Input:<br>
 *			- numPages				Number of pages in total<br>
 *			- page					current page<br>
 *			- pgnName (optional)	parameter name used by script to find page you're on. defaults to page<br>
 *			- ianchor (optional)	set an anchor<br>
 *			- ihash   (optional)	you can pass in all the above as an array called ihash or secondary * items common to all links<br>
 *			The ihash option allow the inclusion of additional link values as provided for smartlink navigation<br>
 * Output:   url of the form: $REQUEST_URI?attribute1=value1&attribute2=value2
 * 
 * @package Smarty
 * @subpackage plugins
 * @link http://www.bitweaver.org/wiki/function_libertypagination function.libertypagination
 */

/**
 * Smarty {libertypagination} function plugin
 */
function smarty_function_libertypagination($params, &$gBitSmarty) {
	if( isset( $params['ihash'] ) && is_array( $params['ihash'] ) ) {
		$params = array_merge( $params['ihash'], $params );
		$params['ihash'] = NULL;
	}

	if( isset( $params['url'] ) ) {
		$urlParams = '';
		parse_str( preg_replace( "/.*\?/", "", $params['url'] ), $urlParams );
		$params = array_merge( $urlParams, $params );
	}
	$pgnName = isset( $params['pgnName'] ) ? $params['pgnName'] : ( isset( $params['curPage'] ) ? 'curPage' : 'page' );
	$pgnVars = '';

	$omitParams = array( 'numPages', 'url', $pgnName, 'pgnName', 'ianchor', 'ajaxId' );
	foreach( $params as $form_param => $form_val ) {
		if ( !empty( $form_val ) && !in_array( $form_param, $omitParams ) ) {
			$pgnVars .= ( !empty( $params['ajaxId'] ) ? "&" : "&amp;" ).$form_param."=".$form_val;
			$pgnHidden[$form_param] = $form_val;
		}
	}
	$pgnVars .= ( !empty( $params['ianchor'] ) ? '#'.$params['ianchor'] : '' );
    for( $pageCount = 1; $pageCount < $params['numPages']+1; $pageCount++ ) {
		if( $pageCount != $params[$pgnName] ) {
			if( $params['ajaxId'] ) {
				$pages[] = '<a href="javascript:void(0);" onclick="BitAjax.updater(\''.$params['ajaxId']."','".$_SERVER['REQUEST_URI']."','".$pgnName.'='.$pageCount.$pgnVars.'\')'.'">'.( $pageCount ).'</a>';
			} else {
				$pages[] = '<a href="'.$_SERVER['SCRIPT_URL'].'?'.$pgnName.'='.$pageCount.$pgnVars.'">'.( $pageCount ).'</a>';
			}
		} else {
			$pages[] = '<strong>'.$pageCount.'</strong>';
		}
	}

	if( $params['numPages'] > 1 ) {
		$gBitSmarty->assign( 'pgnPage', $params[$pgnName] );
		$gBitSmarty->assign( 'pgnName', $pgnName );
		$gBitSmarty->assign( 'pgnVars', $pgnVars );
		$gBitSmarty->assign( 'pgnHidden', $pgnHidden );
	    $gBitSmarty->assign( 'pgnPages', $pages );
	    $gBitSmarty->assign( 'numPages', $params['numPages'] );
	    $gBitSmarty->assign( 'ajaxId', $params['ajaxId'] );
	    $gBitSmarty->display( 'bitpackage:liberty/libertypagination.tpl' );
	}
}
?>
