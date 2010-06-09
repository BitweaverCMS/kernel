<?php
/**
 * @version $Header$
 * @package kernel
 * @subpackage plugins
 * 
 * Smarty {set} compiler function plugin
 *
 * File:     compiler.set.php
 * Type:     compiler function
 * Name:     set
 * Purpose:  Set a value to a variable (also arrays).
 * The optional parameter "if" is used to set the value if the test is true. The test can be: 'empty', '!empty', 'is_null', '!is_null', 'isset', '!isset', 'is_void'.
 * The new command 'is_void' test if the variable is empty and != 0, very useful for test $_REQUEST parameters.
 *
 * @link http://smarty.incutio.com/?page=set
 * @link http://www.dav-muz.net/
 * @version 1.0
 * @copyright Copyright 2006 by Muzzarelli Davide
 * @author Davide Muzzarelli <info@dav-muz.net>
 *
 * @param array parameters "var": variable. "value": value to assign. "if": assign the value only if this test is true (tests avaiables: 'empty', '!empty', 'is_null', '!is_null', 'isset', '!isset', 'is_void').
 * @param Smarty_Compiler object
 * @return void|string
 */
 
/**
 * Set Compiler Function
 */ 
function smarty_compiler_set($params, &$smarty) {
	// Extract if "value" parameter contain an array
	$regularExpression = '/ value=array\([\'"]?.*[\'"]?\)/';
	if (preg_match($regularExpression, $params, $array)) {
		$array = substr($array[0], 7);
		$params = preg_replace($regularExpression, '', $params);
	}

	$params = $smarty->_parse_attrs($params);
	$functionsPermitted = array('empty', '!empty', 'is_null', '!is_null', 'isset', '!isset', 'is_void'); // Functions permitted in "if" parameter.

	if (!isset($params['var'])) {
		$smarty->_syntax_error("set: missing 'var' parameter", E_USER_WARNING);
		return;
	}
	if (!empty($array)) {
		$params['value'] = $array;
	}

	if (!isset($params['value'])) { // Clean setting
		return "{$params['var']} = null;";
	} elseif (isset($params['if'])) { // Setting with "if" parameter
		$params['if'] = substr($params['if'], 1, -1);
		if (in_array($params['if'], $functionsPermitted)) {
			if ($params['if'] == 'is_void') { // "is_void" command
				return "if (empty({$params['var']}) and ({$params['var']} !== 0) and ({$params['var']} !== '0')) {$params['var']} = {$params['value']};";
			} else { // others commands
				return "if ({$params['if']}({$params['var']})) {$params['var']} = {$params['value']};";
			}
		} else { // "if" parameter not correct
			$smarty->_syntax_error("set: 'if' parameter not valid", E_USER_WARNING);
			return;
		}
	} else { // normal setting
		return "{$params['var']} = {$params['value']};";
	}
}
?>

