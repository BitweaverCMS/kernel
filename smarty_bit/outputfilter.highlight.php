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
 function smarty_outputfilter_highlight($source, &$gBitSmarty) {
 	
    $highlight = $_REQUEST['highlight']; 
    $words = $highlight;
    if (!isset($highlight) || empty($highlight)) {
			return $source;
    }

   	//  Pull everything up to bitweaver's <div class="body" tag
	preg_match_all("!.*?.\<div class=.?body.?!si", $source, $match);
	$_bwbody_blocks = $match[0];
    $source = preg_replace("!.*?.\<div class=.?body.?!si",
    '@@@SMARTY:TRIM:BWBODY@@@', $source);
	
	//  Pull everything past <!-- end .body -->
	preg_match_all("!<.?.?.? end .body .?.?>.*!si", $source, $match);
	$_bwendbody_blocks = $match[0];
    $source = preg_replace("!<.?.?.? end .body .?.?>.*!si",
    '@@@SMARTY:TRIM:BWENDBODY@@@', $source);
	
	// let's try killing everything in module blocks
    preg_match_all("!<div class=?.boxtitle.?[^>]+>.*?</div>!si", $source, $match);
    $_boxtitle_blocks = $match[0];
    $source = preg_replace("!<div class=?.boxtitle.?[^>]+>.*?</div>!si",
    '@@@SMARTY:TRIM:BOXTITLE@@@', $source);
	
	// let's try killing everything in module blocks
    preg_match_all("!<div class=?.boxcontent.?[^>]+>.*?</div>!si", $source, $match);
    $_boxcontent_blocks = $match[0];
    $source = preg_replace("!<div class=?.boxcontent.?[^>]+>.*?</div>!si",
    '@@@SMARTY:TRIM:BOXCONTENT@@@', $source);
	
	// remove everything in the TOC
    preg_match_all("!<div class=?.maketoc.?[^>]+>.*?</div>!si", $source, $match);
    $_maketoc_blocks = $match[0];
    $source = preg_replace("!<div class=?.maketoc.?[^>]+>.*?</div>!si",
    '@@@SMARTY:TRIM:MAKETOC@@@', $source);
	
	// Pull out the script blocks
    preg_match_all("!<script[^>]+>.*?</script>!is", $source, $match);
    $_script_blocks = $match[0];
    $source = preg_replace("!<script[^>]+>.*?</script>!is",
    '@@@SMARTY:TRIM:SCRIPT@@@', $source);

    // pull out all html tags
    preg_match_all("'<[\/\!]*?[^<>]*?>'si", $source, $match);
    $_tag_blocks = $match[0];
    $source = preg_replace("'<[\/\!]*?[^<>]*?>'si", '@@@SMARTY:TRIM:TAG@@@', $source);

    // This array is used to choose colors for supplied highlight terms
    $colorArr = array('#ffff66','#ff9999','#A0FFFF','#ff66ff','#99ff99');

    // Wrap all the highlight words with tags bolding them and changing
    // their background colors
    $wordArr = split(" ",$words);
    $i = 0;
    foreach($wordArr as $word) {
			$source = preg_replace("'($word)'si", '<span style="color:black;background-color:'.$colorArr[$i].';">$1</span>', $source); 
			$i++;
    }

	
	// replace the document all the way up to the <div class="body"
    foreach($_bwbody_blocks as $curr_block) {
            $source = preg_replace("!@@@SMARTY:TRIM:BWBODY@@@!",$curr_block,$source,1);
    }
	
	// replace the document from <!-- end .body -->
    foreach($_bwendbody_blocks as $curr_block) {
            $source = preg_replace("!@@@SMARTY:TRIM:BWENDBODY@@@!",$curr_block,$source,1);
    }
	
	// replace the boxtitle
    foreach($_boxtitle_blocks as $curr_block) {
            $source = preg_replace("!@@@SMARTY:TRIM:BOXTITLE@@@!",$curr_block,$source,1);
    }
	
	// replace the boxtitle
    foreach($_boxcontent_blocks as $curr_block) {
            $source = preg_replace("!@@@SMARTY:TRIM:BOXCONTENT@@@!",$curr_block,$source,1);
    }

	// replace the TOC
    foreach($_maketoc_blocks as $curr_block) {
            $source = preg_replace("!@@@SMARTY:TRIM:MAKETOC@@@!",$curr_block,$source,1);
    }
	
	// replace script blocks
    foreach($_script_blocks as $curr_block) {
			$source = preg_replace("!@@@SMARTY:TRIM:SCRIPT@@@!",$curr_block,$source,1);
    }
	
	foreach($_tag_blocks as $curr_block) {
			$source = preg_replace("!@@@SMARTY:TRIM:TAG@@@!",$curr_block,$source,1);
    }

    return $source; 
 }
?>
