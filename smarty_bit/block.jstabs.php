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

	if( $gBitSystem->isFeatureActive( 'site_disable_jstabs' ) ) {
		$ret .= '<div class="tabpane">'.$content.'</div>';
	} else {
		if( !empty( $tab ) ) {
			$id = 1000000 * microtime();
			$ret .= '<div class="tabpane" id="id_'.$id.'">';
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ tabPane = new WebFXTabPane( $( 'id_".$id."' ), true ); /*]]>*/</script>";
			$ret .= $content;
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ setupAllTabs();var tabPane;".( !empty( $tab ) ? "tabPane.setSelectedIndex( $tab );" : '' )." /*]]>*/</script>";
			$ret .= '</div>';
		} else {
			$ret .= '<div class="tabpane">';
			$ret .= $content;
			$ret .= "<script type=\"text/javascript\">/*<![CDATA[*/ setupAllTabs();var tabPane; /*]]>*/</script>";
			$ret .= '</div>';
		}

	}

	return $ret;
}
?>
