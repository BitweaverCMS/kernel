{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/Attic/admin_custom_modules.tpl,v 1.1.1.1.2.1 2005/08/05 22:59:57 squareing Exp $ *}
{strip}

<a name="editcreate"></a>
{form legend="Edit/Create module" id="editusr"}
	<input type="hidden" name="page" value="{$page}" />

	<div class="row">
		{formlabel label="Name" for="um_name"}
		{forminput}
			<input type="text" name="um_name" id="um_name" value="{$um_name|escape}" />
			{formhelp note="You will see this name show up<ul><li>when you want to assign the module</li><li>in the 'div' surrounding the module (for css customisation)</li></ul>"}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Title" for="um_title"}
		{forminput}
			<input type="text" name="um_title" id="um_title" value="{$um_title|escape}" />
			{formhelp note="This is the name that will appear as the title of your module."}
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="Data" for="usermoduledata"}
		{forminput}
			<textarea id="usermoduledata" name="um_data" rows="10" cols="40">{$um_data|escape}</textarea>
			{formhelp note=""}
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="um_update" value="{tr}Save{/tr}" />
		{if $um_name ne ''}
			<br /><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=custom_modules#editcreate">{tr}Create new custom module{/tr}</a>
		{/if}
	</div>
{/form}

{legend legend="Objects that can be included"}
	{if $rsss eq 'y'}
		<div class="row">
			{formlabel label="RSS modules" for=""}
			{forminput}
				<select name="rsss" id="list_rsss">
					{section name=ix loop=$rsss}
						<option value="{literal}{{/literal}rss id={$rsss[ix].rss_id}{literal}}{/literal}">{$rsss[ix].name}</option>
					{/section}
				</select> 
				<a href="javascript:setUserModuleFromCombo('list_rsss');">{tr}Insert{/tr}</a>
				{formhelp note=""}
			{/forminput}
		</div>
	{/if}

	{if $menus}
		<div class="row">
			{formlabel label="Menus" for="list_menus"}
			{forminput}
				<select name="menus" id="list_menus">
					{section name=ix loop=$menus}
						<option value="{literal}{{/literal}menu id={$menus[ix].menu_id}{literal}}{/literal}">{$menus[ix].name}</option>
					{/section}
				</select> 
				<a href="javascript:setUserModuleFromCombo('list_menus');">{tr}Insert{/tr}</a>
				{formhelp note=""}
			{/forminput}
		</div>
	{/if}
{/legend}

<a name="assign"></a>
<table class="data">
	<caption>{tr}Custom Modules{/tr}</caption>
	<tr>
		<th>{tr}Name{/tr}</th>
		<th>{tr}Title{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>

	{section name=user loop=$user_modules}
		<tr class="{cycle values="odd,even"}">
			<td>{$user_modules[user].name}</td>
			<td>{$user_modules[user].title}</td>
			<td style="text-align:right">
				<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=layout&amp;module_name=_custom%3Acustom%2F{$user_modules[user].name}">{biticon ipackage=liberty iname=assign iexplain=assign}</a>
				<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=custom_modules&amp;um_edit={$user_modules[user].name}#editcreate">{biticon ipackage=liberty iname=edit iexplain=edit}</a>
				<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=custom_modules&amp;um_remove={$user_modules[user].name}">{biticon ipackage=liberty iname=delete iexplain=delete}</a>
			</td>
		</tr>
	{sectionelse}
		<tr class="norecords"><td colspan="3">{tr}No records found{/tr}</td></tr>
	{/section}
</table>

<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php">{tr}Clear Modules Cache{/tr}</a>
{/strip}
