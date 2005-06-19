<div class="floaticon">{bithelp}</div>

<div class="admin dsn">
<div class="header">
<h1>{tr}Admin dsn{/tr}</h1>
</div>

<div class="body">

<h2>{tr}Create/edit DSN{/tr}</h2>
<form action="{$gBitLoc.KERNEL_PKG_URL}admin/admin_dsn.php" method="post">
<input type="hidden" name="dsn_id" value="{$dsn_id|escape}" />
<table class="panel">
<tr><td>{tr}name{/tr}:</td><td><input type="text" maxlength="255" size="10" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td>{tr}DSN{/tr}:</td><td><input type="text" maxlength="255" size="40" name="dsn" value="{$info.dsn|escape}" /></td></tr>
<tr class="panelsubmitrow"><td colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}DSN{/tr}</h2>
<table class="data">
<tr>
<th><a href="{$gBitLoc.KERNEL_PKG_URL}admin/admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
<th><a href="{$gBitLoc.KERNEL_PKG_URL}admin/admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'dsn_desc'}dsn_asc{else}dsn_desc{/if}">{tr}DSN{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].name}</td>
<td>{$channels[user].dsn}</td>
<td align="right">
   <a href="{$gBitLoc.KERNEL_PKG_URL}admin/admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].dsn_id}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this dsn?{/tr}')" title="Delete this DSN">{biticon ipackage=liberty iname="delete" iexplain="remove"}</a>
   <a href="{$gBitLoc.KERNEL_PKG_URL}admin/admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;dsn_id={$channels[user].dsn_id}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
</td>
</tr>
{sectionelse}
<tr class="norecords"><td colspan="3">{tr}No records found{/tr}</td></tr>
{/section}
</table>

</div> {* end .body *}

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$gBitLoc.KERNEL_PKG_URL}admin/admin_dsn.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{$gBitLoc.KERNEL_PKG_URL}admin/admin_dsn.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$gBitLoc.KERNEL_PKG_URL}admin/admin_dsn.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

</div> {* end .admin *}
