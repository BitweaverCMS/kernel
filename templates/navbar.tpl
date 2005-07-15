{strip}
<div class="navbar{if $gBitSystem->isFeatureActive( 'feature_dropdown_navbar' )} hor{/if}">
	<ul>
		{if $gBitSystem->isFeatureActive( 'feature_dropdown_navbar' )}
			<li>{tr}Page Menu{/tr}
				<ul>
		{/if}
		{foreach from=$links item=link}
			<li>{$link}</li>
		{/foreach}
		{if $gBitSystem->isFeatureActive( 'feature_dropdown_navbar' )}
				</ul>
			</li>
		{/if}
	</ul>
</div>
<div class="clear"></div>
{/strip}
