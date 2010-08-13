<div id="bittop">
	<div class="floatright alignright">
		{if $gBitUser->isRegistered()}
			{tr}Welcome{/tr}, <strong>{displayname hash=$gBitUser->mInfo}</strong> 
			&bull; <a href="{$smarty.const.USERS_PKG_URL}my.php">{tr}My Account{/tr}</a>
			&bull; <a href="{$smarty.const.USERS_PKG_URL}logout.php">{tr}logout{/tr}</a>
		{else}
			<a href="{$smarty.const.USERS_PKG_URL}login.php">{tr}login{/tr}</a>
			{if $gBitSystem->isFeatureActive( 'users_allow_register' )}
				&bull; <a href="{$smarty.const.USERS_PKG_URL}register.php">{tr}register{/tr}</a> 
			{/if}
		{/if}

		<br />

		{if $gBitSystem->isFeatureActive( 'feature_calendar' ) and $gBitUser->hasPermission( 'p_calendar_view' )}
			<a href="{$smarty.const.CALENDAR_PKG_URL}index.php">{$smarty.now|bit_short_datetime}</a>
		{/if}
	</div>
	<h1><a href="{$smarty.const.BIT_ROOT_URL}">{$gBitSystem->getConfig('site_title')}</a></h1>
	{if $gBitSystem->getConfig('site_slogan')}
	<h3>{$gBitSystem->getConfig('site_slogan')}</h3>
	{/if}
</div>
