<div id="bitbottom">
	{if $gBitSystem->isFeatureActive( 'site_bottom_column' ) && $b_modules && !$gHideModules}
		{section name=homeix loop=$b_modules}
			{$b_modules[homeix].data}
		{/section}
	{/if}

	<a id="poweredby" class="external" href="http://www.bitweaver.org">Powered by bitweaver</a>
	{if $gBitSystem->isFeatureActive( 'babelfish' )}
		{include file="bitpackage:languages/babelfish.tpl"}
	{/if}
</div>