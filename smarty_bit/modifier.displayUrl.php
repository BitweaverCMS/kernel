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
 * Example: {'My Page'|displayUrl}, {'admin'|displyUrl:BitUser}, {$gContent|displayUrl:MyObject}
 * -------------------------------------------------------------
 */
function smarty_modifier_displayUrl($pMixed, $lib='') {
	global  $gLibertySystem;
	if (is_string($pMixed)) {
		if (empty($lib)) $lib ='BitPage';
		if (class_exists($lib)) {
			call_user_func(array($lib, 'getDisplayUrl'),$pMixed);
		}
	} elseif (is_object($pMixed)) {
		if (!empty($lib)) {
			if (class_exists($lib)) {
				$i = $lib();
				return $i->getDisplayUrl($pMixed);
			}
		} else {
			if (method_exists($pMixed,'getDisplayUrl')) {
				return $pMixed->getDisplayUrl();
			}
		}
	} elseif (is_array($pMixed)) {
		if (!empty($lib)) {
			if (class_exists($lib)) {
				$i = $lib();
				return $i->getDisplayUrl($pMixed);
			}
		} elseif (!empty($pMixed['display_url'])) {
			return $pMixed['display_url'];
		} elseif (!empty($pMixed['content_type_guid'])) {
			$lib= $gLibertySystem->mContentTypes[$pContentType]['handler_class'];
			if (class_exists($lib)) {
				$i = $lib();
				return $i->getDisplayUrl($pMixed);
			}
		} elseif (!empty($pMixed['handler_class'])) {
			$lib= $pMixed['handler_class'];
			if (class_exists($lib)) {
				$i = $lib();
				return $i->getDisplayUrl($pMixed);
			}
		}
	}
	return LibertyContent::getDisplayUrl(null,$pMixed);
}
?>
