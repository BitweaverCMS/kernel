<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/view_cache.php,v 1.1.1.1.2.2 2005/07/26 15:50:08 drewslater Exp $
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
if($gBitSystem->getPreference('feature_listPages') != 'y') {
  $gBitSmarty->assign('msg',tra("This feature is disabled"));
  $gBitSystem->display( 'error.tpl' );
  die;  
}
*/
if (isset($_REQUEST['url'])) {
	$id = $wikilib->isCached($_REQUEST['url']);

	if (!$id) {
		$gBitSmarty->assign('msg', tra("No cache information available"));

		$gBitSystem->display( 'error.tpl' );
		die;
	}

	$_REQUEST["cache_id"] = $id;
}

if (!isset($_REQUEST["cache_id"])) {
	$gBitSmarty->assign('msg', tra("No page indicated"));

	$gBitSystem->display( 'error.tpl' );
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
$gBitSystem->display( 'bitpackage:kernel/view_cache.tpl');
$gBitSmarty->display('bitpackage:kernel/view_cache.tpl');

?>
