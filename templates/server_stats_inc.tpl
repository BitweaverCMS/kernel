{strip}{if !$gBitSystem->isLive() || $gBitUser->hasPermission('p_stats_view')}
<div class="server-stats">
		{if !$gBitSystem->isLive()}<span class="alert alert-warning">{tr}Site is not live. Search engines will not index this site.{/tr}</span>{/if}
		{tr}Execution:{/tr} {elapsed}s, {memusage}, {$gBitSystem->mDb->mNumQueries} Queries ( {$gBitSystem->mDb->mQueryTime|string_format:"%.3f"}s / {($gBitSystem->mDb->mQueryTime/$gBitSystem->mTimer->elapsed()*100)|round}% ) 
		{if $server_load}
			, {tr}Load:{/tr} {$server_load|default:"&#8211;"}
		{/if}
</div>
{/if}{/strip}
