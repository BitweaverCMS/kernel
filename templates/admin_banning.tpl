<a class="pagetitle" href="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php">{tr}Banning system{/tr}</a>

<h2>{tr}Add or edit a rule{/tr}</h2>
<form action="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php" method="post">
<input type="hidden" name="ban_id" value="{$ban_id|escape}" />
<table class="panel">
<tr>
	<td><label for="banning-title">{tr}Rule title{/tr}</label></td>
	<td>
		<input type="text" name="title" id="banning-title" value="{$info.title|escape}" maxlength="200" />
	</td>
</tr>
<tr>
	<td><label for="banning-userregex">{tr}Username regex matching{/tr}:</label></td>
	<td>
		<input type="radio" name="mode" value="user" {if $info.mode eq 'user'}checked="checked"{/if} />
		<input type="text" name="user" id="banning-userregex" value="{$info.user|escape}" />
	</td>
</tr>
<tr>
	<td><label for="banning-ipregex">{tr}IP regex matching{/tr}:</label></td>
	<td>
		<input type="radio" name="mode" value="ip" {if $info.mode eq 'ip'}checked="checked"{/if} />
		<input type="text" name="ip1" id="banning-ipregex" value="{$info.ip1|escape}" size="3" />.<input type="text" name="ip2" value="{$info.ip2|escape}" size="3" />.<input type="text" name="ip3" value="{$info.ip3|escape}" size="3" />.<input type="text" name="ip4" value="{$info.ip4|escape}" size="3" />
	</td>
</tr>
<tr>
	<td>{tr}Banned from sections{/tr}:</td>
	<td>

		<table><tr>
		{section name=ix loop=$sections}
        <td>
			<input type="checkbox" name="section[{$sections[ix]}]" id="banning-section" {if in_array($sections[ix],$info.sections)}checked="checked"{/if} /> <label for="banning-section">{$sections[ix]}</label>
        </td>
        {* see if we should go to the next row *}
        {if not ($smarty.section.ix.rownum mod 3)}
                {if not $smarty.section.ix.last}
                        </tr><tr>
                {/if}
        {/if}
        {if $smarty.section.ix.last}
                {* pad the cells not yet created *}
                {math equation = "n - a % n" n=3 a=$data|@count assign="cells"}
                {if $cells ne $cols}
                {section name=pad loop=$cells}
                        <td>&nbsp;</td>
                {/section}
                {/if}
                </tr>
        {/if}
    	{/section}
		</table>
	</td>
</tr>
<tr>
	<td><label for="banning-actdates">{tr}Rule activated by dates{/tr}</label></td>
	<td>
		<input type="checkbox" name="use_dates" id="banning-actdates" {if $info.use_dates eq 'y'}checked="checked"{/if} />
	</td>
</tr>
<tr>
	<td>{tr}Rule active from{/tr}</td>
	<td>
		{html_select_date prefix="date_from" time="$info.date_from"}
	</td>
</tr>
<tr>
	<td>{tr}Rule active until{/tr}</td>
	<td>
		{html_select_date prefix="date_to" time="$info.date_to"}
	</td>
</tr>
<tr>
	<td><label for="banning-mess">{tr}Custom message to the user{/tr}</label></td>
	<td>
		<textarea rows="4" cols="50" name="message">{$info.message|escape}</textarea>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" name="save" value="{tr}save{/tr}" />
	</td>
</tr>
</table>
</form>

<form method="post" action="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<label for="banning-find">{tr}Find{/tr}:</label><input type="text" name="find" id="banning-find" value="{$find|escape}" />
</form>

<h2>{tr}Rules{/tr}:</h2>
<form method="post" action="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />

<table>
<tr>
<th><input type="submit" name="del" value="{tr}Delete{/tr} " /></th>
<th>{tr}Title{/tr}</th>
<th>{tr}User/IP{/tr}</a></th>
<th>{tr}Sections{/tr}</a></th>
<th>{tr}Action{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$items}
<tr class="{cycle}"><td>
	<input type="checkbox" name="delsec[{$items[user].ban_id}]" /></td><td>
	<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php?ban_id={$items[user].ban_id}">{$items[user].title}</a></td><td>
	{if $items[user].mode eq 'user'}
		{$items[user].user}
	{else}
		{$items[user].ip1}.{$items[user].ip2}.{$items[user].ip3}.{$items[user].ip4}
	{/if}</td><td>
		{section name=ix loop=$items[user].sections}
			{$items[user].sections[ix].section}{if not $smarty.section.ix.last},{/if}
		{/section}</td><td>
&nbsp;&nbsp;<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$items[user].ban_id}" onclick="return confirm('{tr}Are you sure you want to delete this rule?{/tr}')" title="Delete this rule">{biticon ipackage=liberty iname="delete" iexplain="remove"}</a>&nbsp;&nbsp;
</td></tr>
{sectionelse}
	<tr class="norecords"><td colspan="5">{tr}No records found{/tr}</td></tr>
{/section}
</table>
</form>

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php?offset={$prev_offset}&amp;find={$find}">{tr}prev{/tr}</a>]�
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
�[<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php?offset={$next_offset}&amp;find={$find}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php offset=$selector_offset">
{$smarty.section.foo.index_next}</a>�
{/section}
{/if}
</div>
