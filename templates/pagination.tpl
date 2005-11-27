{strip}
{if $cant_pages gt 1}
	<div class="pagination">
		{if $prev_offset >= 0}
			<a href="{$pgnUrl}?find={$find}&amp;sort_mode={$sort_mode}&amp;offset={$prev_offset}{$pgnVars}">&laquo;</a>
		{/if}

		&nbsp;{tr}Page {$actual_page} of {$cant_pages}{/tr}&nbsp;

		{if $next_offset >= 0}
			<a href="{$pgnUrl}?find={$find}&amp;sort_mode={$sort_mode}&amp;offset={$next_offset}{$pgnVars}">&raquo;</a>
		{/if}

		<br />

		{if $direct_pagination eq 'y'}
			{section loop=$cant_pages name=foo}
				{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
				<a href="{$pgnUrl}?find={$find}&amp;sort_mode={$sort_mode}&amp;offset={$selector_offset}">{$smarty.section.foo.index_next}</a>
			{/section}
		{else}
			{form action="$pgnUrl" id="fPageSelect"}
				<input type="hidden" name="find" value="{$find}" />
				<input type="hidden" name="sort_mode" value="{$sort_mode}" />
				{foreach from=$pgnHidden key=name item=value}
					<input type="hidden" name="{$name}" value="{$value}" />
				{/foreach}
				{tr}Go to page{/tr} <input class="gotopage" type="text" size="3" maxlength="4" name="page" />
			{/form}
		{/if}
	</div> <!-- end .pagination -->
{elseif $listInfo && $listInfo.total_pages > 1}
	<div class="pagination">
	{tr}Displaying{/tr} <strong>{$listInfo.offset+1}</strong> {tr}to{/tr} <strong>{$listInfo.offset+$listInfo.max_records}</strong> ({tr}of{/tr} <strong>{$listInfo.total_records}</strong>)
	<div class="pager">
	{assign var=pageUrl value="`$smarty.server.PHP_SELF`?main_page=`$smarty.request.main_page`&sort_mode=`$listInfo.sort_mode`"}
	{if $listInfo.current_page > 1}
		{assign var=blockStart value=1}
		{tr}<a href="{$pageUrl}&page={$listInfo.current_page-1}">&laquo; {tr}Prev{/tr}</a>{/tr}&nbsp;
	{/if}
	{if $listInfo.current_page-$listInfo.block_pages > 0}
		{assign var=blockStart value=$listInfo.current_page-$listInfo.block_pages+1}
	{else}
		{assign var=blockStart value=1}
	{/if}
	{if $blockStart > 1} <a href="{$pageUrl}&page={$listInfo.current_page-1}">1</a>  <a href="{$pageUrl}&page={$listInfo.current_page-$listInfo.block_pages}">...</a> {/if}

	{section name=pager start=$blockStart loop=$blockStart+$listInfo.block_pages*2}
	{if $smarty.section.pager.index <= $listInfo.total_pages}
		{if $smarty.section.pager.index != $listInfo.current_page}
		<a href="{$pageUrl}&page={$smarty.section.pager.index}">{$smarty.section.pager.index}</a>
		{else}
		<strong>{$listInfo.current_page}</strong>
		{/if}&nbsp;
	{/if}
	{/section}
	{if $blockStart+$listInfo.block_pages*2 < $listInfo.total_pages} <a href="{$pageUrl}&page={$listInfo.current_page+1}">...</a>  <a href="{$pageUrl}&page={$listInfo.total_pages}">{$listInfo.total_pages}</a>{/if}

	{if $listInfo.current_page < $listInfo.total_pages}
	<a href="{$pageUrl}&list_page={$listInfo.current_page+1}">{tr}Next{/tr} &raquo;</a>
	{/if}
	</div>
	</div> <!-- end .pagination -->
{/if}
{/strip}
