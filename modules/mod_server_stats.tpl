{strip}
{bitmodule title="$moduleTitle" name="loadstats"}
	<ul class="list-inline">
		<li>{tr}Execution time:&nbsp;{elapsed}s{/tr}</li>
		<li>{tr}Memory usage:{/tr}&nbsp;{memusage}</li>
		<li>{tr}Database queries:{/tr}&nbsp;{$gBitSystem->mDb->mNumQueries}</li>
		<li>{tr}Database time:{/tr}&nbsp;{$gBitSystem->mDb->mQueryTime|string_format:"%.3f"}s/{$gBitSystem->mDb->mQueryTime/$gBitSystem->mTimer->elapsed()*100|string_format:" %.1f"}%</li>
		<li>{tr}Compression:{/tr}&nbsp;{$output_compression|default:"0"}</li>
		{if $server_load}
			<li>{tr}Server load:{/tr}&nbsp;{$server_load|default:"&#8211;"}</li>
		{/if}
	</ul>
{/bitmodule}
{/strip}
