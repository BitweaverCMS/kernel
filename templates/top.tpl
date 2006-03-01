<div id="bittop">
	{if $gBitSystem->getConfig('site_title')}
		<div style="float:right;">
	{/if}

	{if $gBitUser->isRegistered()}
		{tr}Welcome{/tr}, <strong>{displayname hash=$gBitUser->mInfo}</strong>
		<a href="{$smarty.const.USERS_PKG_URL}logout.php">{tr}logout{/tr}</a>
	{else}
		<a href="{$smarty.const.USERS_PKG_URL}login.php">{tr}login{/tr}</a>
		{if $gBitSystem->isFeatureActive( 'allow_register' )}
			| <a href="{$smarty.const.USERS_PKG_URL}register.php">{tr}register{/tr}</a> 
		{/if}
	{/if}

	{if $gBitSystem->getConfig('site_title')}
		<br />
	{/if}

	{if $gBitSystem->isFeatureActive( 'feature_calendar' ) and $gBitUser->hasPermission( 'bit_p_view_calendar' )}
		<a href="{$smarty.const.CALENDAR_PKG_URL}index.php">{$smarty.now|bit_short_datetime}</a>
	{else}
		{$smarty.now|bit_short_datetime}
	{/if}

	{if $gBitSystem->getConfig('site_title')}
		</div>
	{/if}

	<h1>{$gBitSystem->getConfig('site_title')}</h1>
	<h3>{$gBitSystem->getConfig('site_slogan')}</h3>
</div>
