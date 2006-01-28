<div id="bittop">
	{if $gBitSystemPrefs.siteTitle}
		<div style="float:right;">
	{/if}

	{if $gBitUser->isRegistered()}
		{tr}Welcome{/tr}, <strong>{displayname hash=$gBitUser->mInfo}</strong>
		<a href="{$smarty.const.USERS_PKG_URL}logout.php">{tr}logout{/tr}</a>
	{else}
		<a href="{$smarty.const.USERS_PKG_URL}login.php">{tr}login{/tr}</a>
		{if $gBitSystem->isFeatureActive( 'allowRegister' )}
			| <a href="{$smarty.const.USERS_PKG_URL}register.php">{tr}register{/tr}</a> 
		{/if}
	{/if}

	{if $gBitSystemPrefs.siteTitle}
		<br />
	{/if}

	{if $gBitSystem->isFeatureActive( 'feature_calendar' ) and $gBitUser->hasPermission( 'bit_p_view_calendar' )}
		<a href="{$smarty.const.CALENDAR_PKG_URL}index.php">{$smarty.now|bit_short_datetime}</a>
	{else}
		{$smarty.now|bit_short_datetime}
	{/if}

	{if $gBitSystemPrefs.siteTitle}
		</div>
	{/if}

	<h1>{$gBitSystemPrefs.siteTitle}</h1>
	<h3>{$gBitSystemPrefs.site_slogan}</h3>
</div>
