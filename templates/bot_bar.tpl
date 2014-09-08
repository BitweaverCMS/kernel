<div class="col-md-12">
{if $gBitSystem->isFeatureActive( 'messages_site_contact' )}
	<a href="{$smarty.const.MESSAGES_PKG_URL}contact.php">{booticon iname="icon-envelope" ilocation=menu iexplain="Contact Us"}</a> 
{/if}
	<a id="poweredby" class="external pull-right" href="http://www.bitweaver.org"><img src="/liberty/icons/bitweaver/bitweaver.gif" alt="Bitweaver" title="Bitweaver" class="icon"></a> {$version_info.local}
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
{include file="bitpackage:kernel/server_stats_inc.tpl"}
{/if}
