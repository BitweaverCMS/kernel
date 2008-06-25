<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/view_cache.php,v 1.10 2008/06/25 22:21:11 spiderr Exp $
 * @package kernel
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );
require_once( WIKI_PKG_PATH.'BitPage.php' );

/*
if($gBitSystem->getConfig('wiki_list_pages') != 'y') {
  $gBitSmarty->assign('msg',tra("This feature is disabled"));
  $gBitSystem->display( 'error.tpl' , NULL, array( 'display_mode' => 'display' ));
  die;  
}
*/
if (isset($_REQUEST['url'])) {
	$id = $wikilib->isUrlCached($_REQUEST['url']);

	if (!$id) {
		$gBitSmarty->assign('msg', tra("No cache information available"));

		$gBitSystem->display( 'error.tpl' , NULL, array( 'display_mode' => 'display' ));
		die;
	}

	$_REQUEST["cache_id"] = $id;
}

if (!isset($_REQUEST["cache_id"])) {
	$gBitSmarty->assign('msg', tra("No page indicated"));

	$gBitSystem->display( 'error.tpl' , NULL, array( 'display_mode' => 'display' ));
	die;
}

// Get a list of last changes to the Wiki database
$info = $gBitSystem->get_cache($_REQUEST["cache_id"]);
$ggcacheurl = 'http://google.com/search?q=cache:'.urlencode(strstr($info['url'],'http://'));

// test if url ends with .txt : formatting for text
if (substr($info["url"], -4, 4) == ".txt") {
	$info["data"] = "<pre>" . $info["data"] . "</pre>";
}

$gBitSmarty->assign('ggcacheurl', $ggcacheurl);
$gBitSmarty->assign_by_ref('info', $info);
$gBitSystem->display( 'bitpackage:kernel/view_cache.tpl', NULL, array( 'display_mode' => 'display' ));



?>
