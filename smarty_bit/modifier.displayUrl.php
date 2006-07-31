<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     displayUrl
 * Purpose:  give back the display Url of an obect
 * Example: {'My Page'|displayUrl}, {'admin'|displyUrl:BitUser}, {$gContent|displayUrl:MyObject}
 * -------------------------------------------------------------
 */
function smarty_modifier_displayUrl($string, $lib='BitPage') {
	switch ($lib) {
	case 'BitPage':
		require_once(WIKI_PKG_PATH.'BitPage.php'); 
		return BitPage::getDisplayUrl($string);
	case 'BitUser':
		require_once(USERS_PKG_PATH.'BitUser.php'); 
		return BitUser::getDisplayUrl($string);
	default:
		if (is_array($string) && !empty($string['display_url'])) {
			return $string['display_url'];
		} else if (is_object($string) && method_exists($string, 'getDisplayUrl')) {
			return $string->getDisplayUrl();
		} else {
			return LibertyContent::getDisplayUrl(null, $string);
		}
	}
}

?>
