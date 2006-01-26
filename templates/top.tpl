<div id="bittop">
	{if $siteTitle}
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

	{if $siteTitle}
		<br />
	{/if}

	{if $gBitSystem->isFeatureActive( 'feature_calendar' ) and $gBitUser->hasPermission( 'bit_p_view_calendar' )}
		<a href="{$smarty.const.CALENDAR_PKG_URL}index.php">{$smarty.now|bit_short_datetime}</a>
	{else}
		{$smarty.now|bit_short_datetime}
	{/if}

	{if $siteTitle}
		</div>
	{/if}

	<h1>{$siteTitle}</h1>
	<h3>{$site_slogan}</h3>
</div>
