<div id="dynamic">
	{if $dynamicContent}
		{$dynamicContent}
	{else}
		{if $gDefaultCenter}
			{include file=$gDefaultCenter}
		{/if}
	{/if}
</div>
