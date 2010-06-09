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
 * Purpose:  give back the display URL
 * If the modification is preformed on a string then the (new <lib>)->getDisplayUrl method is called, (this defaults to BitPage if not specified)
 * If the modification is preformed on an object then, if <lib> is given, then it is passed as the parameter to (new <lib>)->getDisplayUrl otherwise, the object' getDisplayUrl method is called
 * If the modification is preformed on an array then, if <lib> is given, then it is passed as the parameter to (new <lib>)->getDisplayUrl otherwise the following is attempted:
 *     If the array contains an element display_url it is returned
 *     If the array contains an element content_type_guid then lib becomes the handler class of the content_type_guid and the array is passed as the parameter to (new <lib>)->getDisplayUrl
 *     If the array contains an element handler_class then lib becomes the handler_class and the array is passed as the parameter to (new <lib>)->getDisplayUrl
 * --
 * If all of the above tests fail then LibertyContent::getDisplayUrl with the argument to the modifier passed as the second argument
 * Example: {'My Page'|displayUrl}, {'admin'|displayUrl:BitUser}, {$gContent|displayUrl:MyObject}
 * -------------------------------------------------------------
 */

function smarty_modifier_displayUrl_findLib(&$lib,$class_only=false) {
  global $gLibertySystem;
	if (!class_exists($lib)) {
		foreach ($gLibertySystem->mContentTypes as $type) {
			if ($type['handler_class']==$lib) {
				smarty_modifier_displayUrl_loadLib($type);
				return true;
			} elseif ((!$class_only) && ($type['content_type_guid']==$lib)) {
				$lib = $type['handler_class'];
				smarty_modifier_displayUrl_loadLib($type);
				return true;
			}
		}
		return false;
	}
	return true;
}

function smarty_modifier_displayUrl_loadLib($type) {
	$path = constant(strtoupper($type['handler_package']).'_PKG_PATH');
	require_once($path.$type['handler_file']);
}

function smarty_modifier_displayUrl($pMixed, $lib='') {
	global  $gLibertySystem;
	if (is_string($pMixed)) {
		if (empty($lib)) $lib ='BitPage';
		if (smarty_modifier_displayUrl_findLib($lib)) {
			$call =array($lib, 'getDisplayUrl');
			if (is_callable($call)) {
				return call_user_func($call,$pMixed);
			}
			$i = $lib();
			if (method_exists($i,'getDisplayUrl')) {
				return $i->getDisplayUrl($pMixed);
			}
		}
	} elseif (is_object($pMixed)) {
		if (!empty($lib)) {
			if (smarty_modifier_displayUrl_findLib($lib)) {
				$i = $lib();
				return $i->getDisplayUrl($pMixed);
			}
		}
		if (method_exists($pMixed,'getDisplayUrl')) {
			return $pMixed->getDisplayUrl();
		}
	} elseif (is_array($pMixed)) {
		if (!empty($lib)) {
			if (smarty_modifier_displayUrl_findLib($lib)) {
				$i = new $lib();
				return $i->getDisplayUrl($pMixed);
			}
		}
		if (!empty($pMixed['display_url'])) {
			return $pMixed['display_url'];
		}
		if (!empty($pMixed['content_type_guid'])) {
			$type =$gLibertySystem->mContentTypes[$pContentType];
			if (!empty($type)) {
				$lib = $type['handler_class'];
				smarty_modifier_displayUrl_loadLib($type);
				$i = new $lib();
				return $i->getDisplayUrl($pMixed);
			}
		}
		if (!empty($pMixed['handler_class'])) {
			$lib= $pMixed['handler_class'];
			if (smarty_modifier_displayUrl_findLib($lib,true)) {
				$i = $lib();
				return $i->getDisplayUrl($pMixed);
			}
		}
	}
	return LibertyContent::getDisplayUrl(null,$pMixed);
}
?>
