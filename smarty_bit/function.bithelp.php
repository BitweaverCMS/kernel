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
function smarty_function_bithelp($params, &$smarty) {
	global $gBitSystem, $gBitUser;
	$outstr = "";
	if($gBitUser->hasPermission( 'bit_p_admin' )){
		$outstr .= "<a href=\"".KERNEL_PKG_URL."admin/index.php\">".smarty_function_biticon(array('ipackage'=>'liberty', 'iname'=>'administration', 'iexplain'=>'Administration Menu'),$smarty)."</a> ";
	}
	if($gBitSystem->getPreference('feature_help') != 'y') {
		$outstr .= "";
	} else {
		$helpInfo = $smarty->get_template_vars('TikiHelpInfo');
		$outstr .= "<a href=\"".$helpInfo["URL"]."\" >".smarty_function_biticon(array('ipackage'=>'liberty', 'iname'=>'bithelp', 'iexplain'=>(empty($helpInfo["Desc"])?"help":$helpInfo["Desc"])),$smarty)."</a>";
	}
	return $outstr;
}
?>
