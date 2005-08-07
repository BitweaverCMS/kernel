{strip}
{if $gBitSystem->isFeatureActive( 'feature_contact' ) or $gBitUser->isRegistered()}
	<ul>
		{if $gBitSystem->isFeatureActive( 'feature_contact' )}
			<li><a class="item" href="{$smarty.const.MESSU_PKG_URL}contact.php">{biticon ipackage=liberty iname=spacer iforce=icon} {tr}Contact us{/tr}</a></li>
		{/if}

		{if $gBitUser->isRegistered()}
			<li><a class="item" href="{$smarty.const.LIBERTY_PKG_URL}list_content.php">{biticon ipackage=liberty iname=spacer iforce=icon} {tr}All available Content{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.USERS_PKG_URL}index.php">{biticon ipackage=users iname=users iforce=icon} {tr}Users List{/tr}</a></li>
		{/if}
	</ul>
{/if}
{/strip}
