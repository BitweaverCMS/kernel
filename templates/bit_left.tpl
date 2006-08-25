{if $gBitSystem->isFeatureActive( 'site_left_column' ) && $l_modules && !$gHideModules}
	{section name=homeix loop=$l_modules}
		{$l_modules[homeix].data}
	{/section}
{/if}
