{strip}
{if $listInfo.query_string}
		{assign var=pageUrl value="`$smarty.server.SCRIPT_NAME`?`$listInfo.query_string`&amp;"}
{else}
	{capture name=string}
		{foreach from=$listInfo.parameters key=param item=value}
			{if $value|is_array}
				{foreach from=$value item=v}{if $value ne ''}{$param}[]={$v}&amp;{/if}{/foreach}
			{else}
				{if $value ne ''}{$param}={$value}&amp;{/if}
			{/if}
		{/foreach}
		{foreach from=$listInfo.ihash key=param item=value}
			{if $value|is_array}
				{foreach from=$value item=v}{if $value ne ''}{$param}[]={$v}&amp;{/if}{/foreach}
			{else}
				{if $value ne ''}{$param}={$value}&amp;{/if}
			{/if}
		{/foreach}
		{foreach from=$pgnHidden key=param item=value}
			{if $value|is_array}
				{foreach from=$value item=v}{if $value ne ''}{$param}[]={$v}&amp;{/if}{/foreach}
			{else}
				{if $value ne ''}{$param}={$value}&amp;{/if}
			{/if}
		{/foreach}
		{*if $listInfo.sort_mode}
			{if is_array($listInfo.sort_mode)}
				{foreach from=$listInfo.sort_mode item=sort}
					sort_mode[]={$sort}&amp;
				{/foreach}
			{else}
				sort_mode={$listInfo.sort_mode}&amp;
			{/if}
		{/if*}
		{if isset($listInfo.find) && $listInfo.find ne ''}
			find={$listInfo.find}&amp;
		{/if}
	{/capture}
	{assign var=pageUrlVar value=$smarty.capture.string|regex_replace:'/"/':'%22'}
	{assign var=pageUrl value="`$smarty.server.SCRIPT_URL``$pgnUrl`?`$pageUrlVar`"}
{/if}

{if $listInfo.total_pages > 1 && $listInfo.page_records}
<div class="paginator overflow-hidden clear">
	<ul class="pagination pull-left">
		{if $listInfo.current_page > 1}
			{assign var=blockStart value=1}
			<li>{tr}<a href="{$pageUrl}max_records={$listInfo.max_records}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.current_page-1}">&laquo;</a>{/tr}</li>
		{/if}
		{if $listInfo.current_page-$listInfo.block_pages > 0}
			{assign var=blockStart value=$listInfo.current_page-$listInfo.block_pages}
		{else}
			{assign var=blockStart value=1}
		{/if}
		{if $blockStart > 1}
			<li><a href="{$pageUrl}max_records={$listInfo.max_records}&amp;sort_mode={$listInfo.sort_mode}&amp;page=1">1</a></li>
			<li><a href="{$pageUrl}max_records={$listInfo.max_records}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.current_page-$listInfo.block_pages}">...</a></li>
		{/if}

		{section name=current_page start=$blockStart loop=$blockStart+$listInfo.block_pages*2+1}
		{if $smarty.section.current_page.index <= $listInfo.total_pages}
			
			{if $smarty.section.current_page.index != $listInfo.current_page}
			<li><a href="{$pageUrl}max_records={$listInfo.max_records}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$smarty.section.current_page.index}">{$smarty.section.current_page.index}</a></li>
			{else}
			<li class="active"><span>{$listInfo.current_page}</span></li>
			{/if}
		{/if}
		{/section}
		{if $blockStart+$listInfo.block_pages*2 < $listInfo.total_pages}
			<li><a href="{$pageUrl}max_records={$listInfo.max_records}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.current_page+1}">...</a></li>
			<li><a href="{$pageUrl}max_records={$listInfo.max_records}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.total_pages}">{$listInfo.total_pages}</a></li>
		{/if}

		{if $listInfo.current_page < $listInfo.total_pages}
		<li><a href="{$pageUrl}max_records={$listInfo.max_records}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.current_page+1}">&raquo;</a></li>
		{/if}
	</ul>
	<div class="pagination pull-right">
		{form action="$pageUrl" class="form-inline"}
		{$listInfo.offset+1} {tr}to{/tr} {math equation="x + y" x=$listInfo.offset y=$listInfo.page_records} {tr}of{/tr} {$listInfo.total_records},&nbsp; 
				<input type="hidden" name="find" value="{$find|default:$smarty.request.find}" />
				<input type="hidden" name="sort_mode" value="{$sort_mode}" />
				{foreach from=$pgnHidden key=name item=value}
					<input type="hidden" name="{$name}" value="{$value}" />
				{/foreach}
				{tr}Go to page{/tr} <input class="input-mini" type="text" size="3" maxlength="6" name="list_page" /> {tr}of{/tr} <strong>{$listInfo.total_pages}</strong>
		{/form}
	</div>
</div>
{/if}
{/strip}
