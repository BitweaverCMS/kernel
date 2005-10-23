<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_rss
 */
function smarty_function_rss($params, &$gBitSmarty) {
    global $gBitSystem;
    global $rsslib;
	include_once( RSS_PKG_PATH.'rss_lib.php' );
    extract($params);
    // Param = zone
    if(empty($id)) {
        $gBitSmarty->trigger_error("assign: missing id parameter");
        return;
    }
    if(empty($max)) {
       $max = 99;
    }
    $data = $rsslib->get_rss_module_content($id);
    $items = $rsslib->parse_rss_data($data, $id);

	print('<ul>');
    for($i=0;$i<count($items) && $i<$max;$i++) {
       if ($items[$i]["title"] <> '') print('<li><a href="'.$items[$i]["link"].'">'.$items[$i]["title"].'</a>');
	   if ($items[$i]["pubdate"] <> '') print('<span class="date"> - '.$items[$i]["pubdate"].'</span>');
       print('</li>');
    }
    print('</ul>');
}
?>
