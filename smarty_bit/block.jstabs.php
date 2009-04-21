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
 * Input:		you can use {jstab tab=<tab number>} (staring with 0) to select a given tab
 *              or you can use the url to do so: page.php?jstab=<tab number>
 * Abstract:	Used to enclose a set of tabs
 */
function smarty_block_jstabs( $pParams, $pContent, &$gBitSmarty ) {
	global $gBitSystem;
	extract( $pParams );

	$alwaysTab = ( isset( $alwaysTab )) ? ( boolean )$alwaysTab : FALSE;

	// Work out if we want to insert tabs at all on this page
	// This is necessary since we insert tabs dynamically using services
	preg_match_all( '#<div class="tabpage.*?">#', $pContent, $tabs );
	if( !empty( $tabs[0] ) && count( $tabs[0] ) <= 1 && $alwaysTab === FALSE ) {
		$pContent = preg_replace( "#<h4[^>]*tab.*?</h4>#", '', $pContent );
		return $pContent;
	}

	// When tabs are disabled, we simply wrap the tabs with the appropriate div for styling
	if( $gBitSystem->isFeatureActive( 'site_disable_jstabs' ) ) {
		$ret = '<div class="tabpane"';
		$ret .= !empty( $pParams['id'] ) ? ' id="'.$pParams['id'].'"' : '';
		$ret .= '>'.$pContent.'</div>';
	} else {
		// @TODO review this conditional or comment its purpose, $tab never seems to be set
		if( isset( $tab ) || isset( $_REQUEST['jstab'] ) ) {
			// make sure we aren't passed any evil shit
			if( !isset( $tab ) && isset( $_REQUEST['jstab'] ) && preg_match( "!^\d+$!", $_REQUEST['jstab'] ) ) {
				$tab = $_REQUEST['jstab'];
			}
			$ret = '<div class="tabpane" id="jstabs">';
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ tabPane = new WebFXTabPane( $( 'jstabs' ), true ); /*]]>*/</script>";
			$ret .= $pContent;
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ setupAllTabs();".( isset( $tab ) ? "var tabPane; tabPane.setSelectedIndex( $tab );" : "" )."/*]]>*/</script>";
			$ret .= '</div>';
		} else {
			$ret = '<div class="tabpane"';
			$ret .= !empty( $pParams['id'] ) ? ' id="'.$pParams['id'].'"' : '';
			$ret .= '>';
			$ret .= $pContent;
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ setupAllTabs();var tabPane; /*]]>*/</script>";
			$ret .= '</div>';
		}
	}

	return $ret;
}
?>
