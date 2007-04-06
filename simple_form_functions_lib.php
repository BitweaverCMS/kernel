<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_kernel/simple_form_functions_lib.php,v 1.10 2007/04/06 11:48:56 nickpalmer Exp $
 * @package kernel
 * @subpackage functions
 */

/**
 * Store or update a boolean value in the database - automatically collects data from $_REQUEST[$pFeature]
 * @param $pFeature name of the parameter to be set in the database
 * @param $pPackageName name of the package the feature belongs to
 * @return none
 */
function simple_set_toggle( $pFeature, $pPackageName = NULL ) {
	// make function compatible with {html_checkboxes}
	if( isset( $_REQUEST[$pFeature][0] ) ) {
		$_REQUEST[$pFeature] = $_REQUEST[$pFeature][0];
	}
	toggle_preference( $pFeature, ( isset( $_REQUEST[$pFeature] ) ? $_REQUEST[$pFeature] : NULL ), $pPackageName );
}

/**
 * Store or update a boolean value in the database - automatically collects data from $_REQUEST[$pArray] Handy for an array from html_checkboxes when options is used.
 * @param $pArray name of the array to check for features in
 * @param $pFeatures feature to check
 * @param $pPackageName name of the package the feature belongs to
 * @return none
 */
function simple_set_toggle_array( $pArray, $pFeature, $pPackageName = NULL ) {
  	$flipped = array_flip($_REQUEST[$pArray]);
	toggle_preference( $pFeature, ( isset( $flipped[$pFeature] ) ? 'y' : NULL ), $pPackageName );
}

/**
 * Store or update a boolean value in the database
 * @param $pName name of the parameter to be set in the database
 * @param $pValue set $pName to $pValue in kernel_prefs
 * @param $pPackageName name of the package the feature belongs to
 * @return none
 */
function toggle_preference( $pName, $pValue = NULL, $pPackageName = NULL ) {
	global $_REQUEST, $gBitSystem, $gBitSmarty;

	if( isset( $pValue ) && $pValue == "on" ) {
		$prefValue='y';
	} elseif( isset( $pValue ) && $pValue != "n" && strlen( $pValue ) == 1 ) {
		$prefValue=$pValue;
	} else {
		$prefValue='n';
	}
	$gBitSystem->storeConfig( $pName, $prefValue, $pPackageName );
}

/**
 * Store or update a value in the database - automatically collects data from $_REQUEST[$pFeature]
 * @param $pFeature name of the parameter to be set in the database
 * @param $pPackageName name of the package the feature belongs to
 * @return none
 */
function simple_set_value( $pFeature, $pPackageName = NULL ) {
	global $_REQUEST, $gBitSystem, $gBitSmarty;
	if( isset( $_REQUEST[$pFeature] ) ) {
		$gBitSystem->storeConfig( $pFeature, $_REQUEST[$pFeature], $pPackageName );
		$gBitSmarty->assign( $pFeature, $_REQUEST[$pFeature] );
	}
}

/**
 * Store or update an integer in the database - automatically collects data from $_REQUEST[$pFeature]
 * @param $pFeature name of the parameter to be set in the database
 * @param $pPackageName name of the package the feature belongs to
 * @return none
 */
function simple_set_int( $pFeature, $pPackageName = NULL ) {
	global $_REQUEST, $gBitSystem, $gBitSmarty;
	if ( isset( $_REQUEST[$pFeature] ) && is_numeric( $_REQUEST[$pFeature] ) ) {
		$gBitSystem->storeConfig( $pFeature, $_REQUEST[$pFeature], $pPackageName );
		$gBitSmarty->assign( $pFeature, $_REQUEST[$pFeature] );
	}
}

/**
 * Store or update a value in the database but assign it by reference to smarty - automatically collects data from $_REQUEST[$pFeature]
 * @param $pFeature name of the parameter to be set in the database
 * @param $pPackageName name of the package the feature belongs to
 * @return none
 */
function byref_set_value( $pFeature, $pPref = "", $pPackageName = NULL ) {
	global $_REQUEST, $gBitSystem, $gBitSmarty;
	if( isset( $_REQUEST[$pFeature] ) ) {
		if( strlen( $pPref ) > 0 ) {
			$gBitSystem->storeConfig( $pPref, $_REQUEST[$pFeature], $pPackageName );
			// also assign the ref appareantly --gongo
			$gBitSmarty->assign_by_ref( $pPref, $_REQUEST[$pFeature] );
		} else {
			$gBitSystem->storeConfig( $pFeature, $_REQUEST[$pFeature], $pPackageName );
		}

		$gBitSmarty->assign_by_ref( $pFeature, $_REQUEST[$pFeature] );
	}
}

/**
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
