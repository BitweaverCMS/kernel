<div class="admin permission">
<div class="header">
<h1>{tr}Assign permissions to{/tr} {$objectName}</h1>
</div>

<div class="body">

<h2>{tr}Current permissions for this object{/tr}</h2>
<table class="data">
<tr><th>{tr}group{/tr}</th>
<th>{tr}permission{/tr}</th>
<th>{tr}action{/tr}</th></tr>
{cycle values="even,odd" print=false}
{section  name=pg loop=$page_perms}
<tr class="{cycle}"><td>
{$page_perms[pg].group_name}</td><td>
{$page_perms[pg].perm_name}</td><td align="right">
<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?referer={$referer}&amp;action=remove&amp;objectName={$objectName}&amp;object_id={$object_id}&amp;object_type={$object_type}&amp;permType={$permType}&amp;page_id={$pageInfo.page_id}&amp;perm={$page_perms[pg].perm_name}&amp;group={$page_perms[pg].group_name}">{biticon ipackage="icons" iname="edit-delete" iexplain="remove"}</a>
</td></tr>
{sectionelse}
<tr class="norecords"><td colspan="3">{tr}No individual permissions, global permissions apply{/tr}</td></tr>
{/section}
</table>

<br />

<h2>{tr}Assign permissions to this object{/tr}</h2>
<form method="post" action="{$smarty.const.KERNEL_PKG_URL}object_permissions.php">
<input type="hidden" name="page" value="{$page|escape}" />
<input type="hidden" name="referer" value="{$referer|escape}" />
<input type="hidden" name="objectName" value="{$objectName|escape}" />
<input type="hidden" name="object_type" value="{$object_type|escape}" />
<input type="hidden" name="object_id" value="{$object_id|escape}" />
<input type="hidden" name="permType" value="{$permType|escape}" />
<table class="panel"><tr>
<td><input type="submit" name="assign" value="{tr}assign{/tr}" /></td>
<td>
	<select name="perm">
	{section name=prm loop=$perms}
	<option value="{$perms[prm]|escape}">{$perms[prm]}</option>
	{/section}
	</select>
</td>
<td>{tr}to group{/tr}:</td>
<td>
	<select name="group">
	{section name=grp loop=$groups}
	<option value="{$groups[grp].group_name|escape}">{$groups[grp].group_name}</option>
	{/section}
	</select>
</td>
</tr></table>
</form>

</div><!-- end .body -->
</div><!-- end .permission -->
