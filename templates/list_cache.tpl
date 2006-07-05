<div class="floaticon">{bithelp}</div>

<div class="admin cache">
<div class="header">
<h1>{tr}Cache{/tr}</h1>
</div>

<div class="body">

<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$smarty.const.THEMES_PKG_URL}list_cache.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table class="data">
<tr>
<th><a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></th>
<th><a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'refresh_desc'}refresh_asc{else}refresh_desc{/if}">{tr}Last updated{/tr}</a></th>
<th>{tr}Action{/tr}</th>
</tr>
{section name=changes loop=$listpages}
<tr class="odd">
{if $smarty.section.changes.index % 2}
<td><a href="{$listpages[changes].url}">{$listpages[changes].url}</a></td>
<td>{$listpages[changes].refresh|bit_short_datetime}</td>
<td><a href="{$smarty.const.THEMES_PKG_URL}view_cache.php?cache_id={$listpages[changes].cache_id}">{tr}view{/tr}</a><br />
<a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].cache_id}">{tr}remove{/tr}</a><br />
<a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;refresh={$listpages[changes].cache_id}">{tr}refresh{/tr}</a></td>
</tr>
{else}
<tr class="even">
<td><a href="{$listpages[changes].url}">{$listpages[changes].url}</a></td>
<td>{$listpages[changes].refresh|bit_short_datetime}</td>
<td><a href="{$smarty.const.THEMES_PKG_URL}view_cache.php?cache_id={$listpages[changes].cache_id}">{tr}view{/tr}</a><br />
<a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].cache_id}">{tr}remove{/tr}</a><br />
<a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;refresh={$listpages[changes].cache_id}">{tr}refresh{/tr}</a></td>
</tr>
{/if}
{sectionelse}
<tr class="norecords"><td colspan="3">{tr}No records found{/tr}</td></tr>
{/section}
</table>

</div><!-- end .body -->

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $gBitSystem->isFeatureActive( 'site_direct_pagination' )}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:"$gBitSystem->getConfig('max_records')"}
<a href="{$smarty.const.THEMES_PKG_URL}list_cache.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

</div> {* end .admin *}
