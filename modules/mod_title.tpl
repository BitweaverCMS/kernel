{if $popupPage}
	{include file="bitpackage:kernel/poptop.tpl"}
{else}
	{include file="bitpackage:kernel/top.tpl"}
	{if $gBitSystem->isFeatureActive( 'site_top_bar' )}
		{include file="bitpackage:kernel/top_bar.tpl"}
	{/if}
{/if}