{strip}
<div class="display errorpage">
	<div class="header">
		{if $errorHeading}
			<h1>{tr}{$errorHeading}{/tr}</h1>
		{/if}
	</div>

	<div class="body">
		{if $fatalTitle}
			<h2>{tr}{$fatalTitle}{/tr}</h2>
		{/if}

	
		<p class="highlight">{$msg}</p>

		{if $template}
			{include file=$template}
		{/if}

		{if $page and ( $gBitUser->isAdmin() or $gBitUser->hasPermission( 'p_wiki_admin' ) )}
			<p>{tr}Create the page{/tr}: <a href="{$smarty.const.WIKI_PKG_URL}edit.php?page={$page}">{$page}</a></p>
		{/if}

		<ul>
			{if !$gBitUser->isRegistered()}
				<li><a href="{$smarty.const.USERS_PKG_URL}login.php">{tr}Login{/tr}</a></li>
			{/if}
			{if $gBitSystem->isFeatureActive('users_allow_register') and !$gBitUser->isRegistered()}
				<li><a href="{$smarty.const.USERS_PKG_URL}register.php">{tr}Register{/tr}</a></li>
			{/if}
			<li><a href="{$gBitSystem->getDefaultPage()}">{tr}Home page{/tr}</a></li>
			<li><a href="javascript:history.back()">{tr}Previous page{/tr}</a></li>
		</ul>
	</div><!-- end .body -->
</div>
{/strip}
