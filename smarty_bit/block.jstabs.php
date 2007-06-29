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
function smarty_block_jstabs( $pParams, $pContent, &$gBitSmarty ) {
	global $gBitSystem;
	extract( $pParams );

	// Work out if we want to insert tabs at all on this page
	// This is necessary since we insert tabs dynamically using services
	preg_match_all( '#<div class="tabpage">#', $pContent, $tabs );
	if( !empty( $tabs[0] ) && count( $tabs[0] ) <= 1 ) {
		$pContent = preg_replace( "#<h4[^>]*tab.*?</h4>#", '', $pContent );
		return $pContent;
	}

	// When tabs are disabled, we simply wrap the tabs with the appropriate div for styling
	if( $gBitSystem->isFeatureActive( 'site_disable_jstabs' ) ) {
		$ret = '<div class="tabpane">'.$pContent.'</div>';
	} else {
		if( isset( $tab ) || isset( $_REQUEST['jstab'] ) ) {
			// make sure we aren't passed any evil shit
			if( !isset( $tab ) && isset( $_REQUEST['jstab'] ) && preg_match( "!^\d+$!", $_REQUEST['jstab'] ) ) {
				$tab = $_REQUEST['jstab'];
			}
			$ret = '<div class="tabpane"'.(empty($id)?'':' id="'.$id.'"').'>';
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ WebFXTabPane.setCookie( 'webfxtab_".(empty($id)?'':$id)."', $tab );/*]]>*/</script>";
			$ret .= $pContent;
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ setupAllTabs(); /*]]>*/</script>";
			$ret .= '</div>';
		} else {
			$ret = '<div class="tabpane"'.(empty($id)?'':' id="'.$id.'"').'>';
			$ret .= $pContent;
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ setupAllTabs();var tabPane; /*]]>*/</script>";
			$ret .= '</div>';
		}
	}

	return $ret;
}
?>
