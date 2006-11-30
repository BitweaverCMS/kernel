<?php
  /**
   * Smarty plugin
   * @package Smarty
   * @subpackage plugins
   */

  /**
   * Smarty block plugin
   * Requires PHP >= 4.3.0
   * -------------------------------------------------------------
   * Type: block
   * Name: textarea
   * Version: 1.0
   * Author: WaterDragon (nick at sluggardy dot net)
   * Purpose: Creates a textarea
   * -------------------------------------------------------------
   */
function smarty_block_textarea($params, $content, &$smarty)
{
	global $gBitSystem;

	if ($content) {
		$out = '<textarea ';
		
		foreach ($params as $key => $value) {
			$out = $out . $key . '="' . $value . '" ';
		}
		
		$out = $out . ">" . $content . "</textarea>";
	}

	return $out;
}
