{strip}
	<ul class="inline navbar">
		{if $gBitSystem->isFeatureActive( 'themes_dropdown_navbar' )}
			<li>{tr}Page Menu{/tr}
				<ul>
		{/if}
		{foreach from=$links item=link}
			<li>{$link}</li>
		{/foreach}
		{if $gBitSystem->isFeatureActive( 'themes_dropdown_navbar' )}
				</ul>
			</li>
		{/if}
	</ul>
<div class="clear"></div>
{/strip}
