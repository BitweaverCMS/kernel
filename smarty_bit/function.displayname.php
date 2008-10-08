<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**** smarty_function_displayName
	* This is a smarty function which will allow different values to be
	* output to identify users (real_name, user, user_id) as opposed todo
	* only allowing the 'login' to be output.
	* hash=fooHash is a short cut to specifying each parameter by hand
	* usage: {displayname user= user_id= real_name= link_title=}
*/
function smarty_function_displayname( $pParams, &$gBitSmarty ) {
	global $gBitUser;
	if( !empty( $pParams['hash'] ) ) {
		if( is_array( $pParams['hash'] ) ) {
			$hash = array_merge( $pParams, $pParams['hash'] );
			unset( $hash['hash'] );
			// if the hash only has a user_id, we need to look up the user
			if( @BitBase::verifyId( $hash['user_id'] ) && empty( $hash['user'] ) && empty( $hash['email'] ) && empty( $hash['login'] )) {
				$lookupHash['user_id'] = $hash['user_id'];
			}
		} else {
			// We were probably just passed the 'login' due to legacy code which has yet to be converted
			if( strpos( '@', $pParams['hash'] ) ) {
				$lookupHash['email'] = $hash;
			} elseif( is_numeric( $pParams['hash'] ) ) {
				$lookupHash['user_id'] = $hash;
			} else {
				$lookupHash['login'] = $hash;
			}
		}
	} elseif( !empty( $pParams['user_id'] ) ) {
		$lookupHash['user_id'] = $pParams['user_id'];
	} elseif( !empty( $pParams['email'] ) ) {
		$lookupHash['email'] = $pParams['email'];
	} elseif( !empty( $pParams['login'] ) ) {
		$lookupHash['login'] = $pParams['login'];
	} elseif( !empty( $pParams['user'] ) ) {
		$lookupHash['login'] = $pParams['user'];
	} elseif( empty( $pParams ) ) {
		global $gBitUser;
		$hash = $gBitUser->mInfo;
	}

	if( !empty( $lookupHash ) ) {
		$hash = $gBitUser->getUserInfo( $lookupHash );
	}

	if( !empty( $hash ) ) {
		$displayName = BitUser::getDisplayName( empty( $pParams['nolink'] ), $hash );
	} else {
		// Now we're really in trouble. We don't even have a user_id to work with
		$displayName = "Unknown";
	}

	return( $displayName );
}
?>
