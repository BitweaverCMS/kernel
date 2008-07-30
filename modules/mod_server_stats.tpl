{strip}
{bitmodule title="$moduleTitle" name="loadstats"}
	<ul>
		<li>{tr}Execution time: {elapsed}s{/tr}</li>
		<li>{tr}Memory usage:{/tr} {memusage}</li>
		<li>{tr}Database queries:{/tr} {$gBitSystem->mDb->mNumQueries}</li>
		<li>{tr}Database time:{/tr} {$gBitSystem->mDb->mQueryTime|string_format:"%.3f"}s/{$gBitSystem->mDb->mQueryTime/$gBitSystem->mTimer->elapsed()*100|string_format:" %.1f"}%</li>
		<li>{tr}Compression:{/tr} {$output_compression|default:"&#8211;"}</li>
		{if $server_load}
			<li>{tr}Server load:{/tr} {$server_load|default:"&#8211;"}</li>
		{/if}
	</ul>
{/bitmodule}
{/strip}