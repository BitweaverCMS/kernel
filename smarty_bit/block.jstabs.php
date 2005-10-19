<?php 
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {jstabs} block plugin
 *
 * Type:		block
 * Name:		jstabs
 * Input:		
 * Abstract:	Used to enclose a set of tabs
 */
function smarty_block_jstabs( $params, $content, &$gBitSmarty ) {
	global $gBitSystem;
	extract( $params );

	if( $gBitSystem->mPrefs['disable_jstabs'] == 'y' ) {
		$ret .= '<div class="tabpane" id="tabby">'.$content.'</div>';
	} else {
		$js_before = "<script type=\"text/javascript\">//<![CDATA[\ntabPane = new WebFXTabPane( document.getElementById( 'tabby' ), true );\n//]]></script>";
		$js_after .= "<script type=\"text/javascript\">//<![CDATA[\nsetupAllTabs();\nvar tabPane;".( !empty( $tab ) ? "\ntabPane.setSelectedIndex( $tab );" : '' )."\n//]]></script>";
		$ret .= '<div class="tabpane" id="tabby">'.$js_before.$content.$js_after.'</div>';
	}

	return $ret;
}
?>
