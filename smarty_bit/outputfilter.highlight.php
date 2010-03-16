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
	$colorArr = array( '#ffffcc', '#ffcccc', '#a0ffff', '#ffccff', '#ccffcc' );

	// don't highlight characters that are used as replacements
	$find = array(
		"!(\s|^)%(\s|$)!",
		"!(\s|^)#(\s|$)!",
		"!(\s|^)@(\s|$)!",
		"!(\s|^):(\s|$)!",
		"!(\s|^)&(\s|$)!",
	);
	$words = trim( preg_replace( $find, "$1$2", urldecode( $_REQUEST['highlight'] )));
	if( empty( $words )) {
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
			// highlighted words
			$source = preg_replace( $extractor, "@@@##########:#########%:##########@@@", $source );

			// extraction patterns and their replacements
			$patterns = array(
				// scripts
				"!<script[^>]+>.*?</script>!is"           => "@@@##########:#########%:#########&@@@",
				// maketoc
				"!<div class=.?maketoc[^>]*>.*?</div>!si" => "@@@##########:#########%:#########@@@@",
				// html tags
				"'<[\/\!]*?[^<>]*?>'si"                   => "@@@##########:#########%:#########:@@@",
			);

			ksort( $patterns );

			foreach( $patterns as $pattern => $replace ) {
				preg_match_all( $pattern, $highlight, $match );
				$matches[$replace] = $match[0];
				$highlight = preg_replace( $pattern, $replace, $highlight );
			}

			// Wrap all the highlight words with a colourful span
			$wordArr = array();
			$pattern = '#"([^"]*)"#';
			if( preg_match_all( $pattern, $words, $ms ) ) {
				$wordArr = $ms[1];
				// remove the words we've just dealt with
				$words = preg_replace( $pattern, "", $words );
			}

			$words = preg_replace( "!\s+!", " ", $words );
			if( !empty( $words ) ) {
				$wordArr = array_merge( $wordArr, explode( ' ', $words ) );
			}

			//$wordArr = split( " ", urldecode( $words ) );
			//vd($wordArr);
			$i = 0;
			$wordList = tra( "Highlighted words" ).': ';
			foreach( $wordArr as $word ) {
				$wordList .= '<span style="font-weight:bold;padding:0 0.3em;color:black;background-color:'.$colorArr[$i].';">'.$word.'</span> ';
				$highlight = preg_replace( "/(".preg_quote( $word ).")/si", '<span style="font-weight:bold;color:black;background-color:'.$colorArr[$i++].';">$1</span>', $highlight ); 
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
		// Escape dollars in highlight so they don't back reference
		$highlight = preg_replace('!\$!','\\\$', $highlight);
		$source = preg_replace( "!@@@##########:#########%:##########@@@!", $highlight, $source, 1 );
	}

	return $source;
}
?>
