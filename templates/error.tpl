{strip}
<div class="display errorpage">
	<div class="header">
		<h1>{tr}Oops!{/tr}</h1>
	</div>

	<div class="body">
		{box title=$fatalTitle}
			<p class="highlight">{$msg}</p>

			{if $template}
				{include file=$template}
			{/if}

			{if $page and ( $gBitUser->isAdmin() or $gBitUser->hasPermission( 'p_wiki_admin' ) )}
				<p>{tr}Create the page{/tr}: <a href="{$smarty.const.WIKI_PKG_URL}edit.php?page={$page}">{$page}</a></p>
			{/if}

			<p><a href="javascript:history.back()">{tr}Go back{/tr}</a></p>
			<p><a href="{$gBitSystem->getDefaultPage()}">{tr}Go to home page{/tr}</a></p>
		{/box}
	</div>
</div>
{/strip}
