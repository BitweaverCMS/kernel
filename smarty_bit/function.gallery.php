<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * smarty_function_gallery
 */
function smarty_function_gallery($params, &$gBitSmarty)
{
    global $gBitSystem;
    include_once( IMAGEGALS_PKG_PATH.'imagegal_lib.php' );
    extract($params);
    // Param = id

    if (empty($id)) {
        $gBitSmarty->trigger_error("assign: missing 'id' parameter");
        return;
    }
    $img = $gBitSystem->get_random_image($id);
    print('<center>');
    print('<table  border="0" cellpadding="0" cellspacing="0">');
    print('<tr>');
    print('<td align=center>');
    print('<a href="'.IMAGEGALS_PKG_URL.'browse_image.php?gallery_id='.$img['gallery_id'].'&amp;image_id='.$img['image_id'].'"><img alt="thumbnail" class="athumb" src="show_image.php?id='.$img['image_id'].'&amp;thumb=1" /></a><br/>');    
    print('<b>'.$img['name'].'</b><br>');
    if ($showgalleryname == 1) { 
    print('<small>From <a href="'.IMAGEGALS_PKG_URL.'browse_gallery.php?gallery_id='.$img['gallery_id'].'">'.$img['gallery'].'</a></small>');
    } 
    print('</td></tr></table></center>');
}    
?>
<!--
<center>
<table  border="0" cellpadding="0" cellspacing="0">
<tr>
<td align=center>
<a href="'.IMAGEGALS_PKG_URL.'browse_image.php?gallery_id=<?php echo $img['gallery_id']; ?>&amp;image_id=<?php echo $img['image_id']; ?>"><img alt="thumbnail" class="athumb" src="show_image.php?id=<?php echo $img['image_id']; ?>&amp;thumb=1" /></a><br/>
<b><?php echo $img['name']; ?></b><br>
<?php if ($showgalleryname == 1) { ?><small>From <a href="'.IMAGEGALS_PKG_URL.'browse_gallery.php?gallery_id=<?php echo $img['gallery_id']; ?>"><?php echo $img['gallery']; ?></a></small><?php } ?>
</td>
</tr>
</table>
</center>
-->
