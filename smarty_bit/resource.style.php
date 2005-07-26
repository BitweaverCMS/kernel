<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin - smarty_resource_style_source
 * ------------------------------------------------------------- 
 * 
 * -------------------------------------------------------------
 */
function smarty_resource_style_source($tpl_name, &$tpl_source, &$gBitSmarty)
{
	// Check if file exists in the style directory if not
	// check if file exists in the templates directory,
	// if not then fall
}

/**
 * Smarty plugin - smarty_resource_style_timestamp
 * ------------------------------------------------------------- 
 * 
 * -------------------------------------------------------------
 */
function smarty_resource_style_timestamp($tpl_name, &$tpl_timestamp, &$gBitSmarty)
{
    // do database call here to populate $tpl_timestamp.
    $sql = new SQL;
    $sql->query("select tpl_timestamp
                   from my_table
                  where tpl_name='$tpl_name'");
    if ($sql->num_rows) {
        $tpl_timestamp = $sql->record['tpl_timestamp'];
        return true;
    } else {
        return false;
    }
}

/**
 * Smarty plugin - smarty_resource_style_secure
 * ------------------------------------------------------------- 
 * 
 * -------------------------------------------------------------
 */
function smarty_resource_style_secure($tpl_name, &$gBitSmarty)
{
    // assume all templates are secure
    return true;
}

/**
 * Smarty plugin - smarty_resource_style_trusted
 * ------------------------------------------------------------- 
 * 
 * -------------------------------------------------------------
 */
function smarty_resource_style_trusted($tpl_name, &$gBitSmarty)
{
    // not used for templates
}
?>
