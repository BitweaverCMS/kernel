<div class="span12">
{if $gBitSystem->isFeatureActive( 'messages_site_contact' )}
	<a href="{$smarty.const.MESSAGES_PKG_URL}contact.php">{booticon iname="icon-envelope" ilocation=menu iexplain="Contact Us"}</a> 
{/if}
{if $gBitSystem->isFeatureActive( 'babelfish' )}
	{include file="bitpackage:languages/babelfish.tpl"}
{/if}
	<a id="poweredby" class="external pull-right" href="http://www.bitweaver.org"><img src="/liberty/icons/bitweaver/bitweaver.gif" alt="Bitweaver" title="Bitweaver" class="icon"></a>
	{if $gBitUser->isAdmin()} <strong>{$gBitSystem->getBitVersion()}</strong>
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
{if $gBitUser->isAdmin()}
<div class="stats">
	<ul class="inline">
		{if !$gBitSystem->isLive()}<li><span class="alert alert-warning">{tr}Site is not live. Search engines will not index this site.{/tr}</span></li>{/if}
		<li>{tr}Execution time:{/tr} {elapsed}s</li>
		<li>{tr}Memory:{/tr} {memusage}</li>
		<li>{tr}Db queries:{/tr} {$gBitSystem->mDb->mNumQueries}</li>
		<li>{tr}Db time:{/tr} {$gBitSystem->mDb->mQueryTime|string_format:"%.3f"}s / {$gBitSystem->mDb->mQueryTime/$gBitSystem->mTimer->elapsed()*100|round}%</li>
		<li>{tr}Compression:{/tr} {$output_compression|default:"0"}</li>
		{if $server_load}
			<li>{tr}Server load:{/tr} {$server_load|default:"&#8211;"}</li>
		{/if}
	</ul>
</div>
{/if}
