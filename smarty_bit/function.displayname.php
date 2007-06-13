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
	if( !empty( $pParams['hash'] ) && is_array($pParams['hash']) ) {
		$hash = array_merge( $pParams, $pParams['hash'] );
		unset( $hash['hash'] );
	} elseif( !empty( $pParams['user_id'] ) || !empty( $pParams['login'] ) || !empty( $pParams['user'] ) || !empty( $pParams['real_name'] )) {
		// maybe params were passed in separately
		$hash = $pParams;
	} else {
		global $gBitUser;
		$hash = array_merge( $pParams, $gBitUser->mInfo );
	}

	if( !( is_array( $hash ))) {
		// We were probably just passed the 'login' due to legacy code which has yet to be converted
		$user = new BitUser();
		$user->load( TRUE, $hash );
		$hash = $user->mInfo;
	} elseif( empty( $hash['real_name'] ) && empty( $hash['user'] ) && empty( $hash['login'] ) && empty( $hash['email'] )) {
		if( empty( $hash['user_id'] )) {
			// Now we're really in trouble. We don't even have a user_id to work with
			$displayName = "Unknown";
		} else {
			// Maybe we just weren't passed enuf info in $hash. We'll load up a BitUser instance to make sure we get the right display name
			$user = new BitUser( $hash['user_id'] );
			$user->load( TRUE );
			$displayName = $user->mInfo['display_name'];
			$hash = $user->mInfo;
		}
	}

	return( BitUser::getDisplayName( empty( $pParams['nolink'] ), $hash ));
}
?>
