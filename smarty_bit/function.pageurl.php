<?php
/**
 * Smarty {pageurl} function plugin
 * @package Smarty
 * @subpackage plugins
 * @link http://www.bitweaver.org/wiki/function_pageurl function_pageurl
 */

/**
 * Smarty {pagination} function plugin
 *
 * Type:     function<br>
 * Name:     pageurl<br>
 * Input:
 *           - <listHash>=<attribute=>value>  (optional) - pass in any attributes and they will be added to the url string<br>
 * Output:   url of the form: $PHP_SELF?attribute1=value1&attribute2=value2

/* Build up URL variable string */
function smarty_function_pageurl( $params, &$gBitSmarty ) {
	extract( $params );

	if( !isset( $pgnUrl ) ) {
		$pgnUrl = $gBitSmarty->get_template_vars('returnURL');
		if ( isset( $params['url'] ) ) {
			$pgnUrl = $params['url'];
			unset( $params['url'] );
		}
		if( empty( $pgnUrl ) ) {
			$pgnUrl = $_SERVER['PHP_SELF'];
		}
	}

	$str = '';

	if( !empty( $listInfo['parameters'] ) ){
		$str .= pageurl_hash_to_string( $listInfo['parameters'] );
	}
	if( !empty( $listInfo['ihash'] ) ){
		$str .= pageurl_hash_to_string( $listInfo['ihash'] );
		// find can show up in two places
		$foundFind = !empty( $listInfo['ihash']['find'] );
	}
	if( !empty( $pgnHidden ) ){
		$str .= pageurl_hash_to_string( $pgnHidden );
	}
	if ( !empty( $listInfo['sort_mode'] ) ){
		if ( is_array( $listInfo['sort_mode']) ){
			foreach( $listInfo['sort_mode'] as $sort ){
				$str .= "&amp;sort_mode[]=".$sort;
			}
		}else{
			$str .= "&amp;sort_mode=".$listInfo['sort_mode'];
		}
	}
	if( !$foundFind && isset($listInfo['find']) && $listInfo['find'] != '' ){
		$str .= "&amp;find=".$listInfo['find'];
	}

	$pageUrlVar = preg_replace( '/^\&amp;/', '', $str );

	$pageUrl = $pgnUrl . "?" . $pageUrlVar;
	
	return $pageUrl;
}

function pageurl_hash_to_string( $pParamHash ){
	$str = "";

	foreach( $pParamHash as $param=>$value ){
		if( is_array( $value ) ){
			foreach ( $value as $v ){
				if ( $value != '' ){
					$str .= "&amp;".$param."[]=".$v;
				}
			}
		}elseif ( $value != '' ){
			$str .= "&amp;".$param."=".$value;
		}
	}

	return $str;
}
