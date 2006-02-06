{if $gBitSystem->isFeatureActive( 'left_column' ) && $l_modules && !$gHideModules}
	<td id="bitleft">
		{section name=homeix loop=$l_modules}
			{$l_modules[homeix].data}
		{/section}
	</td> <!-- end #bitleft -->
{/if}
