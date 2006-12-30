<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.highlight.php
 * Type:     outputfilter
 * Name:     highlight
 * Version:  1.1
 * Date:     Sep 18, 2003
 * Version:  1.0
 * Date:     Aug 10, 2003
 * Purpose:  Adds Google-cache-like highlighting for terms in a
 *           template after its rendered. This can be used 
 *           easily integrated with the wiki search functionality
 *           to provide highlighted search terms.
 * Install:  Drop into the plugin directory, call 
 *           $gBitSmarty->load_filter('output','highlight');
 *           from application.
 * Author:   Greg Hinkle <ghinkl@users.sourceforge.net>
 *           patched by mose <mose@feu.org>
 * -------------------------------------------------------------
 */
function smarty_outputfilter_highlight( $source, &$gBitSmarty ) {
	// This array is used to choose colours for supplied highlight terms
	$colorArr = array( '#ffff66', '#ff9999', '#a0ffff', '#ff66ff', '#99ff99' );

	$words = $_REQUEST['highlight'];
	if( empty( $words ) ) {
		return $source;
	}

	// get all text that needs to be highlighted
	$extractor = "#<div\s+class=.?body[^>]*>.*?<!--\s*end\s*\.?body\s*-->#is";
	preg_match_all( $extractor, $source, $match );
	$highlights = $match[0];

	$ret = array();
	foreach( $highlights as $key => $highlight ) {
		// if we picked something up, we highlight the contents
		if( !empty( $highlight ) ) {
			$source = preg_replace( $extractor, "@@@SMARTY:TRIM:HIGHLIGHT@@@", $source );

			// extraction patterns and their replacements
			$patterns = array(
				"!<script[^>]+>.*?</script>!is"           => "@@@SMARTY:TRIM:SCRIPT@@@",
				"!<div class=.?maketoc[^>]*>.*?</div>!si" => "@@@SMARTY:TRIM:MAKETOC@@@",
				"'<[\/\!]*?[^<>]*?>'si"                   => "@@@SMARTY:TRIM:TAG@@@",
			);

			ksort( $patterns );

			foreach( $patterns as $pattern => $replace ) {
				preg_match_all( $pattern, $highlight, $match );
				$matches[$replace] = $match[0];
				$highlight = preg_replace( $pattern, $replace, $highlight );
			}

			$i = 0;
			// Wrap all the highlight words with a colourful span
			$wordArr = split( " ", urldecode( $words ) );
			$wordList = '';
			foreach( $wordArr as $word ) {
				$wordList .= '<span style="font-weight:bold;padding:0 0.3em;color:black;background-color:'.$colorArr[$i].';">'.$word.'</span> ';
				$highlight = preg_replace( "'($word)'si", '<span style="font-weight:bold;padding:0 0.3em;color:black;background-color:'.$colorArr[$i++].';">$1</span>', $highlight ); 
			}

			krsort( $patterns );

			foreach( $patterns as $pattern ) {
				foreach( $matches[$pattern] as $insert ) {
					$highlight = preg_replace( "!{$pattern}!", $insert, $highlight, 1 );
				}
			}

			$highlight = '<div class="wordlist">'.$wordList.'</div>'.$highlight;
			$ret[] = $highlight;
		}
	}

	// insert the highlighted code back into the source
	foreach( $ret as $highlight ) {
		$source = preg_replace( "!@@@SMARTY:TRIM:HIGHLIGHT@@@!", $highlight, $source, 1 );
	}

	return $source;
}
?>
