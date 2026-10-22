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
	{assign var=pageUrl value="`$smarty.server.SCRIPT_URL`?`$pageUrlVar`"}
{/if}

{assign var=pgnMax value=$listInfo.max_records_sel|default:$listInfo.max_records}
{if $listInfo.page_records && ($listInfo.total_pages > 1 || ($listInfo.size_choices && ($listInfo.total_records > $listInfo.page_size || $listInfo.max_records_sel ne $listInfo.page_size)))}
<div class="paginator overflow-hidden clear">
	{if $listInfo.total_pages > 1}
	<ul class="pagination pull-left">
		{if $listInfo.current_page > 1}
			{assign var=blockStart value=1}
			<li>{tr}<a href="{$pageUrl}max_records={$pgnMax}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.current_page-1}">&laquo;</a>{/tr}</li>
		{/if}
		{if $listInfo.current_page-$listInfo.block_pages > 0}
			{assign var=blockStart value=$listInfo.current_page-$listInfo.block_pages}
		{else}
			{assign var=blockStart value=1}
		{/if}
		{if $blockStart > 1}
			<li><a href="{$pageUrl}max_records={$pgnMax}&amp;sort_mode={$listInfo.sort_mode}&amp;page=1">1</a></li>
			<li><a href="{$pageUrl}max_records={$pgnMax}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.current_page-$listInfo.block_pages}">...</a></li>
		{/if}

		{section name=current_page start=$blockStart loop=$blockStart+$listInfo.block_pages*2+1}
		{if $smarty.section.current_page.index <= $listInfo.total_pages}
			
			{if $smarty.section.current_page.index != $listInfo.current_page}
			<li><a href="{$pageUrl}max_records={$pgnMax}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$smarty.section.current_page.index}">{$smarty.section.current_page.index}</a></li>
			{else}
			<li class="active"><span>{$listInfo.current_page}</span></li>
			{/if}
		{/if}
		{/section}
		{if $blockStart+$listInfo.block_pages*2 < $listInfo.total_pages}
			<li><a href="{$pageUrl}max_records={$pgnMax}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.current_page+1}">...</a></li>
			<li><a href="{$pageUrl}max_records={$pgnMax}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.total_pages}">{$listInfo.total_pages}</a></li>
		{/if}

		{if $listInfo.current_page < $listInfo.total_pages}
		<li><a href="{$pageUrl}max_records={$pgnMax}&amp;sort_mode={$listInfo.sort_mode}&amp;page={$listInfo.current_page+1}">&raquo;</a></li>
		{/if}
	</ul>
	{/if}
	<div class="pagination pull-right">
		{form action="$pageUrl" class="form-inline"}
			{$listInfo.offset+1}-{math equation="x + y" x=$listInfo.offset y=$listInfo.page_records} {tr}of{/tr} {$listInfo.total_records}
			<input type="hidden" name="find" value="{$find|default:$smarty.request.find}" />
			<input type="hidden" name="sort_mode" value="{$sort_mode}" />
			<input type="hidden" name="page" value="1" />
			{foreach from=$pgnHidden key=name item=value}
				{if $name ne 'max_records' && $name ne 'page' && $name ne 'list_page'}
					<input type="hidden" name="{$name}" value="{$value}" />
				{/if}
			{/foreach}
			{if $listInfo.size_choices}
				<select name="max_records" class="input-mini" onchange="this.form.submit()" title="{tr}Per page{/tr}">
					{foreach from=$listInfo.size_choices key=val item=label}
						<option value="{$val|escape}"{if $listInfo.max_records_sel == $val} selected="selected"{/if}>{$label|escape}</option>
					{/foreach}
				</select>
			{/if}
		{/form}
	</div>
</div>
{/if}
{/strip}
