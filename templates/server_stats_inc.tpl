{if $gBitUser->hasPermission('p_stats_view')}
<div class="server-stats">
	<ul class="inline">
		{if !$gBitSystem->isLive()}<li><span class="alert alert-warning">{tr}Site is not live. Search engines will not index this site.{/tr}</span></li>{/if}
		<li>{tr}Execution time:{/tr} {elapsed}s</li>
		<li>{tr}Memory:{/tr} {memusage}</li>
		<li>{tr}Db queries:{/tr} {$gBitSystem->mDb->mNumQueries}</li>
		<li>{tr}Db time:{/tr} {$gBitSystem->mDb->mQueryTime|string_format:"%.3f"}s / {($gBitSystem->mDb->mQueryTime/$gBitSystem->mTimer->elapsed()*100)|round}%</li>
		{if $server_load}
			<li>{tr}Server load:{/tr} {$server_load|default:"&#8211;"}</li>
		{/if}
	</ul>
</div>
{/if}
