<div id="bitbottom">
	{if $gBitSystem->isFeatureActive( 'messages_site_contact' )}
		<a href="{$smarty.const.MESSAGES_PKG_URL}contact.php">{biticon iname=emblem-mail ilocation=menu iexplain="Contact Us"}</a> &bull;
	{/if}
	<a id="poweredby" class="external" href="http://www.bitweaver.org">Powered by bitweaver</a>
	{if $gBitSystem->isFeatureActive( 'babelfish' )}
		{include file="bitpackage:languages/babelfish.tpl"}
	{/if}
</div>
