<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/simple_form_functions_lib.php,v 1.3 2005/08/01 18:40:33 squareing Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * simple_set_toggle
 */
function simple_set_toggle($feature, $pPackageName=NULL) {
	// make function compatible with {html_checkboxes}
	if( isset( $_REQUEST[$feature][0] ) ) {
		$_REQUEST[$feature] = $_REQUEST[$feature][0];
	}
	toggle_preference( $feature, (isset($_REQUEST[$feature]) ? $_REQUEST[$feature] : NULL), $pPackageName );
}

/**
 * toggle_preference
 */
function toggle_preference( $pName, $pValue, $pPackageName=NULL ) {
	global $_REQUEST, $gBitSystem, $gBitSmarty;

	if (isset($pValue) && $pValue == "on") {
		$prefValue='y';
	} elseif( isset($pValue) && $pValue != "n" && strlen( $pValue ) == 1 ) {
		$prefValue=$pValue;
	} else {
		$prefValue='n';
	}
	$gBitSystem->storePreference( $pName, $prefValue, $pPackageName );
}

/**
 * simple_set_value
 */
function simple_set_value($feature) {
	global $_REQUEST, $gBitSystem, $gBitSmarty;
	if (isset($_REQUEST[$feature])) {
		$gBitSystem->storePreference($feature, $_REQUEST[$feature]);
		$gBitSmarty->assign($feature, $_REQUEST[$feature]);
	}
}

/**
 * simple_set_int
 */
function simple_set_int($feature) {
	global $_REQUEST, $gBitSystem, $gBitSmarty;
	if (isset($_REQUEST[$feature]) && is_numeric($_REQUEST[$feature])) {
		$gBitSystem->storePreference($feature, $_REQUEST[$feature]);
		$gBitSmarty->assign($feature, $_REQUEST[$feature]);
	}
}

/**
 * byref_set_value
 */
function byref_set_value($feature, $pref = "", $pPackageName=NULL) {
	global $_REQUEST, $gBitSystem, $gBitSmarty;
	if (isset($_REQUEST[$feature])) {
		if (strlen($pref) > 0) {
			$gBitSystem->storePreference($pref, $_REQUEST[$feature], $pPackageName);
			// also assign the ref appareantly --gongo
			$gBitSmarty->assign_by_ref($pref, $_REQUEST[$feature]);
		} else {
			$gBitSystem->storePreference($feature, $_REQUEST[$feature], $pPackageName);
		}

		$gBitSmarty->assign_by_ref($feature, $_REQUEST[$feature]);
	}
}

/**
 * set_tab
 *
 * simple function used to work out what tab was pressed and activates the correct tab after reload
 * use with <tabname>TabSubmit as the name of the submit button value and set your tabpage class like this
 * <div class="tabpage {$<tabname>TabSelect}">
 * @returns <tabname> that was submitted
 */
function set_tab() {
	global $_REQUEST,$gBitSmarty;
	$ret = FALSE;
	if( !empty( $_REQUEST ) ) {
		foreach( array_keys( $_REQUEST ) as $item ) {
			if( preg_match( "/TabSubmit/",$item ) ) {
				$tab = preg_replace( "/TabSubmit/","",$item );
				$gBitSmarty->assign( $tab.'TabSelect','tdefault' );
				$ret = $tab;
			}
		}
	}
	return $ret;
}
?>
