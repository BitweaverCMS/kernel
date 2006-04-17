{if $gBitSystem->isFeatureActive( 'site_right_column' ) && $r_modules && !$gHideModules}
	<td id="bitright">
		{section name=homeix loop=$r_modules}
			{$r_modules[homeix].data}
		{/section}
	</td> <!-- end #bitright -->
{/if}
