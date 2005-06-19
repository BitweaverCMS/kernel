{if $gBitSystemPrefs.feature_left_column eq 'y' && $l_modules && !$gHideModules}
	<td id="bitleft">
		{section name=homeix loop=$l_modules}
			{$l_modules[homeix].data}
		{/section}
	</td> <!-- end #bitleft -->
{/if}
