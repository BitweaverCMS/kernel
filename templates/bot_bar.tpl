<div>
{if $gBitSystem->isFeatureActive( 'messages_site_contact' )}
	<a href="{$smarty.const.MESSAGES_PKG_URL}contact.php">{booticon iname="icon-envelope" ilocation=menu iexplain="Contact Us"}</a> 
{/if}
{if $gBitSystem->isFeatureActive( 'babelfish' )}
	{include file="bitpackage:languages/babelfish.tpl"}
{/if}
	<a id="poweredby" class="external" href="http://www.bitweaver.org"><img src="/liberty/icons/bitweaver/bitweaver.gif" alt="Bitweaver" title="Bitweaver" class="icon"></a> <strong>{$gBitSystem->getBitVersion()}</strong>
	{if $gBitUser->isAdmin()}
		{assign var=version_info value=$gBitSystem->checkBitVersion()}
		{if $version_info.compare lt 0}
			{tr}Upgrade to{/tr} <strong>{$version_info.upgrade}</strong>
		{elseif $version_info.compare gt 0}
			{tr}Latest Version{/tr} <strong>{$version_info.upgrade}</strong>
		{/if}

		{if $version_info.release}
			{tr}Latest Release{/tr} <strong>{$version_info.release}</strong>
		{/if}
		
		{if $version_info.error.number ne 0}
			{$version_info.error.string}
		{elseif $version_info.compare eq 0 and !$version_info.release}
			{tr}Your version is up to date.{/tr}
		{elseif $version_info.compare lt 0 or $version_info.release}
			{tr}Your version is not up to date.{/tr}
		{elseif $version_info.compare gt 0 or $version_info.release}
			{tr}Seems you are using a test version.{/tr}
		{/if}
</div>
<div class="stats">
	<ul class="inline">
		<li>{tr}Execution time:&nbsp;{elapsed}s{/tr}</li>
		<li>{tr}Memory usage:{/tr}&nbsp;{memusage}</li>
		<li>{tr}Database queries:{/tr}&nbsp;{$gBitSystem->mDb->mNumQueries}</li>
		<li>{tr}Database time:{/tr}&nbsp;{$gBitSystem->mDb->mQueryTime|string_format:"%.3f"}s / {$gBitSystem->mDb->mQueryTime/$gBitSystem->mTimer->elapsed()*100|round}%</li>
		<li>{tr}Compression:{/tr}&nbsp;{$output_compression|default:"0"}</li>
		{if $server_load}
			<li>{tr}Server load:{/tr}&nbsp;{$server_load|default:"&#8211;"}</li>
		{/if}
	</ul>
{/if}
</div>
