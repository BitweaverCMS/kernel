{strip}
<div class="navbar{if $gBitSystemPrefs.feature_dropdown_navbar eq 'y'} hor{/if}">
	<ul>
		{if $gBitSystemPrefs.feature_dropdown_navbar eq 'y'}
			<li>{tr}Page Menu{/tr}
				<ul>
		{/if}
		{foreach from=$links item=link}
			<li>{$link}</li>
		{/foreach}
		{if $gBitSystemPrefs.feature_dropdown_navbar eq 'y'}
				</ul>
			</li>
		{/if}
	</ul>
</div>
<div class="clear"></div>
{/strip}
