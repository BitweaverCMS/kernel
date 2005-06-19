<?php


function smarty_function_rss($params, &$smarty)
{
    global $gBitSystem;
    global $rsslib;
	include_once( RSS_PKG_PATH.'rss_lib.php' );
    extract($params);
    // Param = zone
    if(empty($id)) {
        $smarty->trigger_error("assign: missing id parameter");
        return;
    }
    if(empty($max)) {
       $max = 99;
    }
    $data = $rsslib->get_rss_module_content($id);
    $items = $rsslib->parse_rss_data($data, $id);
		print('<ul class="rsslist">');
    for($i=0;$i<count($items) && $i<$max;$i++) {
       if ($items[$i]["title"] <> '') print('<li><a href="'.$items[$i]["link"].'">'.$items[$i]["title"].'</a>');
       if ($items[$i]["pubdate"] <> '') print(' <small>'.$items[$i]["pubdate"].'</small>');
       print('</li>');
    }
    print('</ul>');
}

/* vim: set expandtab: */

?>
