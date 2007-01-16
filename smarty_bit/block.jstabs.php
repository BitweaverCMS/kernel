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
		$ret .= '<div class="tabpane">'.$pContent.'</div>';
	} else {
		if( !empty( $tab ) || !empty( $_REQUEST['jstab'] ) ) {
			// make sure we aren't passed any evil shit
			if( empty( $tab ) && !empty( $_REQUEST['jstab'] ) && preg_match( "!^\d+$!", $_REQUEST['jstab'] ) ) {
				$tab = $_REQUEST['jstab'];
			}
			$id = 1000000 * microtime();
			$ret .= '<div class="tabpane" id="id_'.$id.'">';
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ tabPane = new WebFXTabPane( $( 'id_".$id."' ), true ); /*]]>*/</script>";
			$ret .= $pContent;
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ setupAllTabs();var tabPane;tabPane.setSelectedIndex( $tab );/*]]>*/</script>";
			$ret .= '</div>';
		} else {
			$ret .= '<div class="tabpane">';
			$ret .= $pContent;
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ setupAllTabs();var tabPane; /*]]>*/</script>";
			$ret .= '</div>';
		}
	}

	return $ret;
}
?>
