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
{/if}
{/strip}
