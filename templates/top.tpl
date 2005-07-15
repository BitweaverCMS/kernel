<div id="bittop">
{if $siteTitle}
<div style="float:right;">
{/if}

{if $gBitUser->isRegistered()}
	{tr}Welcome{/tr}, <b>{displayname hash=$gBitUser->mInfo}</b>
	<a href="{$gBitLoc.USERS_PKG_URL}logout.php">{tr}logout{/tr}</a>
{else}
	<a href="{$gBitLoc.USERS_PKG_URL}login.php">{tr}login{/tr}</a>
	{if $gBitSystem->isFeatureActive( 'allowRegister' )}
		| <a href="{$gBitLoc.USERS_PKG_URL}register.php">{tr}register{/tr}</a> 
	{/if}
{/if}

{if $siteTitle}
<br />
{/if}

{if $gBitSystem->isFeatureActive( 'feature_calendar' ) and $gBitUser->hasPermission( 'bit_p_view_calendar' )}
	<a href="{$gBitLoc.CALENDAR_PKG_URL}index.php">{$smarty.now|bit_short_datetime}</a>
{else}
	{$smarty.now|bit_short_datetime}
{/if}
{if $gBitUser->isAdmin() and $gBitSystem->isFeatureActive( 'feature_debug_console' )}
	&#160;//&#160;<a title="{tr}View Debugger{/tr}" href="javascript:toggle('debugconsole');">{tr}Debugger{/tr}</a>
	{include file="bitpackage:debug/debug_console.tpl"}
{/if}

{if $siteTitle}
</div>
{/if}

<h1>{$siteTitle}</h1>
<h3>{$site_slogan}</h3>
</div>
