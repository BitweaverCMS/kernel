{strip}
{if $cant_pages gt 1}
	<div class="pagination">
		{if $prev_offset >= 0}
			<a href="{$pgnUrl}?find={$find|default:$smarty.request.find}&amp;sort_mode={$sort_mode}&amp;offset={$prev_offset}{$pgnVars}">&laquo;</a>
		{/if}

		&nbsp;{tr}Page {$actual_page} of {$cant_pages}{/tr}&nbsp;

		{if $next_offset >= 0}
			<a href="{$pgnUrl}?find={$find|default:$smarty.request.find}&amp;sort_mode={$sort_mode}&amp;offset={$next_offset}{$pgnVars}">&raquo;</a>
		{/if}

		<br />

		{if $direct_pagination eq 'y'}
			{section loop=$cant_pages name=foo}
				{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
				<a href="{$pgnUrl}?find={$find|default:$smarty.request.find}&amp;sort_mode={$sort_mode}&amp;offset={$selector_offset}">{$smarty.section.foo.index_next}</a>
			{/section}
		{else}
			{form action="$pgnUrl" id="fPageSelect"}
				<input type="hidden" name="find" value="{$find|default:$smarty.request.find}" />
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
		{math equation="offset + 1 * max" offset=$listInfo.offset max=$listInfo.max_records assign=to}
		{tr}Displaying{/tr} <strong>{$listInfo.offset+1}</strong> {tr}to{/tr} <strong>{if $to > $listInfo.total_records}{$listInfo.total_records}{else}{$to}{/if}</strong> ({tr}of{/tr} <strong>{$listInfo.total_records}</strong>)
		{if $gBitSystem->isFeatureActive( 'direct_pagination' )}
			<div class="pager">
				<span class="left">
					{assign var=pageUrl value="`$smarty.server.PHP_SELF`?sort_mode=`$listInfo.sort_mode`"}

					{foreach from=$listInfo.block.prev key=list_page item=prev}
						&nbsp;<a href="{$pageUrl}&list_page={$list_page}">{$prev}</a>&nbsp;
					{/foreach}

					{if $listInfo.current_page > 1}
						<a href="{$pageUrl}&list_page={$listInfo.current_page-1}">&laquo;&nbsp;{tr}Prev{/tr}</a>&nbsp;
					{/if}
				</span>

				<span class="right">
					{if $listInfo.current_page < $listInfo.total_pages}
						<a href="{$pageUrl}&list_page={$listInfo.current_page+1}">{tr}Next{/tr} &raquo;</a>
					{/if}

					{foreach from=$listInfo.block.next key=list_page item=next}
						&nbsp;<a href="{$pageUrl}&list_page={$list_page}">{$next}</a>&nbsp;
					{/foreach}
				</span>
			</div>
		{else}
			{form action="$pgnUrl"}
				<input type="hidden" name="find" value="{$find|default:$smarty.request.find}" />
				<input type="hidden" name="sort_mode" value="{$sort_mode}" />
				{foreach from=$pgnHidden key=name item=value}
					<input type="hidden" name="{$name}" value="{$value}" />
				{/foreach}
				{tr}Go to page{/tr} <input class="gotopage" type="text" size="3" maxlength="4" name="page" /> {tr}of{/tr} <strong>{$listInfo.total_pages}</strong>
			{/form}
		{/if}
	</div> <!-- end .pagination -->
{/if}
{/strip}
