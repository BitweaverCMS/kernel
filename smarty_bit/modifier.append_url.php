<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_modifier_append_url
 */
function smarty_modifier_append_url( $pUrl, $pKey, $pValue=NULL ) {
	$ret = $pUrl;
	if( isset( $pValue ) ) {
		$ret .= (strpos( $pUrl, '?' ) ? '&' : '?').urlencode( $pKey ).'='.urlencode( $pValue );
	}
	return $ret;
}
