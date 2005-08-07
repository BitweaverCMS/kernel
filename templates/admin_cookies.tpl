<div class="floaticon">{bithelp}</div>

<div class="admin cookies">
<div class="header">
<h1>{tr}Admin cookies{/tr}</h1>
</div>

<div class="body">

<h2>{tr}Create/edit cookies{/tr}</h2>
<form action="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php" method="post">
<input type="hidden" name="cookie_id" value="{$cookie_id|escape}" />
<table class="panel">
<tr><td>
	{tr}Cookie{/tr}:</td><td>
	<input type="text" maxlength="255" size="40" name="cookie" value="{$cookie|escape}" /></td></tr>
<tr class="panelsubmitrow"><td colspan="2">
	<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Upload Cookies from textfile{/tr}</h2>
<form enctype="multipart/form-data" action="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php" method="post">
<table class="panel">
<tr><td>{tr}Upload from disk:{/tr}</td><td>
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
<input name="userfile1" type="file" /></td></tr>
<tr class="panelsubmitrow"><td colspan="2"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Cookies{/tr}</h2>
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table class="data">
<tr>
<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'cookie_id_desc'}cookie_id_asc{else}cookie_id_desc{/if}">{tr}ID{/tr}</a></th>
<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'cookie_desc'}cookie_asc{else}cookie_desc{/if}">{tr}cookie{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].cookie_id}</td>
<td>{$channels[user].cookie}</td>
<td align="right">
   <a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].cookie_id}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this cookie?{/tr}')" title="{tr}Delete this cookie{/tr}">{biticon ipackage=liberty iname="delete" iexplain="remove"}</a>
   <a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;cookie_id={$channels[user].cookie_id}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
</td>
</tr>
{sectionelse}
<tr class="norecords"><td colspan="3">{tr}No records found{/tr}</td></tr>
{/section}
</table>

</div> {* end .body *}

<div class="navbar">
  <a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php?removeall=1">{tr}Remove all cookies{/tr}</a>
</div>

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_cookies.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

</div> {* end .cookies *}
