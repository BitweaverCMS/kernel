{strip}

<div class="display errorpage">
	<div class="header">
		<h1>{tr}Error{/tr}</h1>
	</div>

	<div class="body">
		{box ipackage="liberty" iname="error" iexplain="error" title="An error has occurred"}
			<p>{$msg}</p>
			{if $template}
				{include file=$template}
			{/if}
			{if $page and ($gBitUser->isAdmin() or  $gBitUser->hasPermission( 'bit_p_admin_wiki' ))}
				<p><a href="{$gBitLoc.WIKI_PKG_URL}edit.php?page={$page}">{tr}Create {$page}{/tr}</a></p>
			{/if}
			<p><a href="javascript:history.back()">{tr}Go back{/tr}</a></p>
			<p><a href="{$gBitSystem->getDefaultPage()}">{tr}Go to home page{/tr}</a></p>
		{/box}
	</div>
</div>

{/strip}
