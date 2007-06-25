{strip}
{if $gBitSystem->isFeatureActive( 'messages_site_contact' ) or $gBitUser->isRegistered() and $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}
	<ul>
		{if $gBitSystem->isFeatureActive( 'messages_site_contact' )}
			<li><a class="item" href="{$smarty.const.MESSAGES_PKG_URL}contact.php">{biticon iname=emblem-mail iforce=icon} {tr}Contact us{/tr}</a></li>
		{/if}

		{if $gBitUser->isRegistered()}
			<li><a class="item" href="{$smarty.const.LIBERTY_PKG_URL}list_content.php">{biticon iname=format-justify-fill iforce=icon} {tr}All available Content{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.USERS_PKG_URL}index.php">{biticon ipackage="icons" iname="system-users" iexplain="List of Users" iforce=icon} {tr}Users List{/tr}</a></li>
		{/if}
	</ul>
{/if}
{/strip}
