{strip}
<div class="display errorpage">
	<div class="header">
		{if $errorHeading}
			<h1>{tr}{$errorHeading}{/tr}</h1>
		{/if}
	</div>

	<div class="body row">
		{if $fatalTitle}
			<h2>{tr}{$fatalTitle}{/tr}</h2>
		{/if}

		{if $msg}	
		<div class="alert alert-danger">{$msg}</div>
		{/if}

		{if $template}
			{include file=$template}
		{/if}

		{if $page and ( $gBitUser->isAdmin() or $gBitUser->hasPermission( 'p_wiki_admin' ) )}
			<p>{tr}Create the page{/tr}: <a href="{$smarty.const.WIKI_PKG_URL}edit.php?page={$page}">{$page}</a></p>
		{/if}

	</div><!-- end .body -->
</div>
{/strip}
