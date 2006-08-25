{if $gBitSystem->isFeatureActive( 'site_right_column' ) && $r_modules && !$gHideModules}
	{section name=homeix loop=$r_modules}
		{$r_modules[homeix].data}
	{/section}
{/if}
