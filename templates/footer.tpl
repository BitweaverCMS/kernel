{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/footer.tpl,v 1.1.1.1.2.1 2005/07/15 12:01:15 squareing Exp $ *}

{*if $gBitUser->isAdmin() and $gBitSystem->isFeatureActive( 'feature_debug_console' )*}
  {* Include debugging console. Note it should be processed as near as possible to the end of file *}

  {php}
	if (defined("DEBUG_PKG_PATH")) {
		include_once( DEBUG_PKG_PATH.'debug_console.php' );
	}
  {/php}
{*  {include file="bitpackage:debug/debug_console"} *}
{*/if*}
</body>
</html>  
