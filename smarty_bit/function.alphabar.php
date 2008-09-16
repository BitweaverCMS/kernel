<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @author xing <xing$synapse.plus.com>
 */

/**
 * Smarty {alphabar} function plugin
 *
 * Type:	function
 * Name:	alphabar
 * Input:
 *			- iskip		(optional)	array of chars that can be skipped
 *			- iall		(optional)	if set to anything, it will include a link to all
 *			- ifile		(optional)	set the file where the link should point (default is the current file)
 *			- ipackage	(optional)	set the package the link should point to (default is the current package)
 *			- *			(optional)	anything else that gets added to the pile of items is appended using &amp;$key=$val
 * Example	- {alphabar}
 */
function smarty_function_alphabar( $params, &$gBitSmarty ) {
	extract( $params );

	// work out what the url is
	if( isset( $ifile ) ) {
		if( isset( $ipackage ) ) {
			if( $ipackage == 'root' ) {
				$url = BIT_ROOT_URL.$ifile;
			} else {
				$url = constant( strtoupper( $ipackage ).'_PKG_URL' ).$ifile;
			}
		} else {
			$url = constant( strtoupper( ACTIVE_PACKAGE ).'_PKG_URL' ).$ifile;
		}
	} else {
		$url = $_SERVER['PHP_SELF'];
	}

	$alphabar_params = array( 'ifile', 'ipackage', 'iall' );
	// append any other paramters that were passed in
	$url_params = '';
	foreach( $params as $key => $val ) {
		if( !empty( $val ) && !in_array( $key, $alphabar_params ) ) {
			$url_params .= '&amp;'.$key."=".$val;
		}
	}

	$ret = '<div class="pagination alphabar">';
	$alpha = array( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0-9','+' );
	foreach( $alpha as $char ) {
		if( empty( $iskip ) || !in_array( $char, $iskip )) {
			$wrap = array( 'open' => '', 'close' => '' );
			if( !empty( $_REQUEST['char'] ) && $_REQUEST['char'] == strtolower( $char )) {
				$wrap = array( 'open' => '<strong>', 'close' => '</strong>' );
			}
			$ret .= $wrap['open'].'<a href="'.$url.'?char='.urlencode( strtolower( $char )).$url_params.'">'.$char.'</a>'.$wrap['close'].' ';
		}
	}

	if( !empty( $params['iall'] ) ) {
		$ret .= '<a href="'.$url.'?char='.urlencode( strtolower( 'All' ) ).$url_params.'">'.tra( 'All' ).'</a> ';
	}
	$ret .= '</div>';

	return $ret;
}
?>
