{strip}
{bitmodule title="$moduleTitle" name="loadstats"}
	<ul>
		<li>{tr}Execution time: {elapsed}s{/tr}</li>
		<li>{tr}Memory usage:{/tr} {memusage}</li>
		<li>{tr}Database queries:{/tr} {$gBitSystem->mDb->mNumQueries}</li>
		<li>GZIP: {$gzip}</li>
		<li>{tr}Server load:{/tr} {$server_load|default:"?"}</li>
	</ul>
{/bitmodule}
{/strip}
