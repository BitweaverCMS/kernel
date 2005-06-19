{strip}
{if $control.cant_pages gt 1}
	<div class="pagination">
		{if $control.prev_offset >= 0}
			<a href="{$pgnUrl}?find={$control.find}&amp;sort_mode={$control.sort_mode}&amp;offset={$control.prev_offset}{$pgnVars}">&laquo;</a>
		{/if}

		&nbsp;{tr}Page {$control.actual_page} of {$control.cant_pages}{/tr}&nbsp;

		{if $control.next_offset >= 0}
			<a href="{$pgnUrl}?find={$control.find}&amp;sort_mode={$control.sort_mode}&amp;offset={$control.next_offset}{$pgnVars}">&raquo;</a>
		{/if}

		<br />

		{if $direct_pagination eq 'y'}
			{section loop=$control.cant_pages name=foo}
				{assign var=selector_offset value=$smarty.section.foo.index|times:$control.max_records}
				<a href="{$pgnUrl}?find={$control.find}&amp;sort_mode={$control.sort_mode}&amp;offset={$selector_offset}">{$smarty.section.foo.index_next}</a>
			{/section}
		{else}
			{form action="$pgnUrl" id="fPageSelect"}
				<input type="hidden" name="find" value="{$control.find}" />
				<input type="hidden" name="sort_mode" value="{$control.sort_mode}" />
				{foreach from=$pgnHidden key=name item=value}
					<input type="hidden" name="{$name}" value="{$value}" />
				{/foreach}
				{tr}Go to page{/tr} <input class="gotopage" type="text" size="3" maxlength="4" name="page" />
			{/form}
		{/if}
	</div> <!-- end .pagination -->
{/if}
{/strip}
