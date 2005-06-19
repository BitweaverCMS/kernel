{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/footer.tpl,v 1.1 2005/06/19 04:52:54 bitweaver Exp $ *}

{*if $gBitUser->isAdmin() and $gBitSystemPrefs.feature_debug_console eq 'y'*}
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
