<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * source elements
 */
require_once('function.biticon.php');
/**
* smarty_function_bithelp
*/
function smarty_function_bithelp($params, &$gBitSmarty) {
	global $gBitSystem, $gBitUser;
	$outstr = "";
	if($gBitUser->hasPermission( 'p_admin' )){
		$outstr .= "<a href=\"".KERNEL_PKG_URL."admin/index.php\">".smarty_function_biticon(array('ipackage'=>'liberty', 'iname'=>'administration', 'iexplain'=>'Administration Menu'),$gBitSmarty)."</a> ";
	}
	if($gBitSystem->isFeatureActive('help')) {
		$outstr .= "";
	} else {
		$helpInfo = $gBitSmarty->get_template_vars('TikiHelpInfo');
		$outstr .= "<a href=\"".$helpInfo["URL"]."\" >".smarty_function_biticon(array('ipackage'=>'liberty', 'iname'=>'bithelp', 'iexplain'=>(empty($helpInfo["Desc"])?"help":$helpInfo["Desc"])),$gBitSmarty)."</a>";
	}
	return $outstr;
}
?>
