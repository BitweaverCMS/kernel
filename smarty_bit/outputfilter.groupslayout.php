<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Handler
 */
function group_replace_url_hander($matched) {
	$url = $matched[2];
	// Make sure it is not an off site url
	if( substr($url, 0, strlen(BIT_ROOT_URL)) == BIT_ROOT_URL ||
		substr($url, 0, strlen(BIT_ROOT_URI)) == BIT_ROOT_URI ) {
		// Make sure it is decoded
		$url = urldecode($url);
		// Figure out which way to express it
		if( strstr($url, '?') ) {
			$url = $url.'&group_layout_id='.$_REQUEST['group_layout_id'];
		}
		else {
			$url = $url.'?group_layout_id='.$_REQUEST['group_layout_id'];
		}
		// Return it in the right way.
		return $matched[1].$url.$matched[3];
	}

	return $matched[0];
}

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.groupslayout.php
 * Type:     outputfilter
 * Name:     groupslayout
 * Version:  1.0
 * Date:     April 14, 2008
 * Purpose:
 * Install:  Drop into the plugin directory, call 
 *           $gBitSmarty->load_filter('output','groupslayout');
 *           from application.
 * Author:   Nick Palmer <nick@slugardy.net> based on highlight filter by
 *           Greg Hinkle <ghinkl@users.sourceforge.net>
 *           and mose <mose@feu.org>
 * -------------------------------------------------------------
 */
function smarty_outputfilter_groupslayout( $source, &$gBitSmarty ) {
	if( empty($_REQUEST['group_layout_id']) || !is_numeric($_REQUEST['group_layout_id']) ) {
		return $source;
	}

	// get all text that needs to be
	$extractor = "|<div\s+id=.?content[^>]*>.*?<!--\s*end\s*#content\s*-->|is";
	preg_match_all( $extractor, $source, $match );
	$contents = $match[0];

	$ret = array();
	foreach( $contents as $key => $content ) {
		// if we picked something up, we rip it out and modify
		if( !empty( $content ) ) {
			$source = preg_replace( $extractor, "@@@SMARTY:TRIM:CONTENT@@@", $source );

			// Protect some parts
			$patterns = array(
				"!<script[^>]+>.*?</script>!is"           => "@@@SMARTY:TRIM:SCRIPT@@@"
			);

			//			ksort( $patterns );

			foreach( $patterns as $pattern => $replace ) {
				preg_match_all( $pattern, $content, $match );
				$matches[$replace] = $match[0];
				$content = preg_replace( $pattern, $replace, $content );
			}

			// Now we can finally fix up the anchor tags
			$pattern = '#(<a\s+.*?href=[\"\'])(.+?)([\"\']\s*[^>]*>)#';
			$content = preg_replace_callback($pattern, group_replace_url_hander, $content);

			// Put the protected parts back in.
			foreach( $patterns as $pattern ) {
				foreach( $matches[$pattern] as $insert ) {
					$content = preg_replace( "!{$pattern}!", $insert, $content, 1 );
				}
			}

			$ret[] = $content;
		}
	}

	// insert the code back into the source
	foreach( $ret as $content ) {
	    // Escape dollars in content so they don't back reference
	    $content = preg_replace('!\$!','\\\$', $content);
		$source = preg_replace( "!@@@SMARTY:TRIM:CONTENT@@@!", $content, $source, 1 );
	}

	return $source;
}
?>
