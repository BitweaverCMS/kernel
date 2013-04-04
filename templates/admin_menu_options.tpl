{strip}

<table>
	<tr>
		<td class="aligntop">
			{box title="`$admmenu_info.name`"}
				{include file="bitpackage:users/user_menu.tpl" moptions=$allmoptions menu_info=$admmenu_info}
			{/box}
		</td>
		<td class="aligntop">
			{form legend="Edit menu options"}
				<input type="hidden" name="page" value="{$page}" />
				<input type="hidden" name="option_id" value="{$option_id|escape}" />
				<input type="hidden" name="menu_id" value="{$menu_id|escape}" />
				<div class="control-group">
					{formlabel label="Name" for="menu_name"}
					{forminput}
						<input id="menu_name" type="text" name="name" value="{$name|escape}"size="34"  />
						{formhelp note="Visible name of the link in the menu."}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="URL" for="menu_url"}
					{forminput}
						<input id="menu_url" type="text" name="url" value="{$url|escape}" size="34" />
						{formhelp note="Full path to the page. (This should be PACKAGE and RELATIVE PATH)."}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Package" for="menu_section"}
					{forminput}
						<input id="menu_section" type="text" name="section" value="{$section|escape}" size="34" />
						{formhelp note="You can select when this item is visible depending on what package you are browsing. (not working at the moment)"}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Permissions" for="menu_perm"}
					{forminput}
						<input id="menu_perm" type="text" name="perm" value="{$perm|escape}" size="34" />
						{formhelp note="Determines what permissions are required to view this menu item."}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Group" for="menu_groupname"}
					{forminput}
						<input id="menu_groupname" type="text" name="groupname" value="{$groupname|escape}" size="34" />
						{formhelp note="Select which group can view this item. Your can cofigure Groups and Users from the Administration --&gt; Users pages."}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Type" for="type"}
					{forminput}
						<select name="type" id="type">
							<option value="s" {if $type eq 's'}selected="selected"{/if}>{tr}section{/tr}</option>
							<option value="o" {if $type eq 'o'}selected="selected"{/if}>{tr}option{/tr}</option>
						</select>
						{formhelp note="<dl><dt>section</dt><dd>A section is the link that will serve as a collapsable menu section (or as a menu heading if the menu type is fixed).<dt>option</dt><dd>These are the actual menu options.</dd></dl>"}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Position" for="position"}
					{forminput}
						<input type="text" name="position" id="position" value="{$position|escape}" size="5" />
						{formhelp note=""}
					{/forminput}
				</div>

				<div class="control-group submit">
					<input type="submit" class="btn" name="save" value="{tr}Save{/tr}" />
				</div>
			{/form}

			<div class="navbar">
				<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=menus">{tr}List menus{/tr}</a>
				<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=menus&menu_id={$menu_id}">{tr}Edit this menu{/tr}</a>
			</div>
		</td>
	</tr>
</table>

{minifind menu_id=$menu_id sort_mode=$sort_mode page=$page}

<table class="data">
	<caption>{tr}Menu Options{/tr}</caption>
	<tr>
		<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'option_id_desc'}option_id_asc{else}option_id_desc{/if}">{tr}ID{/tr}</a></th>
		<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}Position{/tr}</a></th>
		<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
		<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></th>
		<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></th>
		<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'section_desc'}section_asc{else}section_desc{/if}">{tr}Sections{/tr}</a></th>
		<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'perm_desc'}perm_asc{else}perm_desc{/if}">{tr}Permissions{/tr}</a></th>
		<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupnam_desc'}groupname_asc{else}groupname_desc{/if}">{tr}Group{/tr}</a></th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{section name=user loop=$admmoptions}
		<tr class="{cycle}">
			<td>{$admmoptions[user].menu_id}</td>
			<td>{$admmoptions[user].position}</td>
			<td>{$admmoptions[user].name}</td>
			<td>{$admmoptions[user].url}</td>
			<td>{$admmoptions[user].type}</td>
			<td>{$admmoptions[user].section}</td>
			<td>{$admmoptions[user].perm}</td>
			<td>{$admmoptions[user].groupname}</td>
			<td class="alignright">
				<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$admmoptions[user].option_id}" onclick="return confirm('{tr}Are you sure you want to delete this menu item?{/tr}')" title="{tr}Delete this menu{/tr}">{booticon iname="icon-trash" ipackage="icons" iexplain="remove"}</a>
				<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;menu_id={$menu_id}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;option_id={$admmoptions[user].option_id}" title="Edit this menu">{booticon iname="icon-edit" ipackage="icons" iexplain="edit"}</a>
			</td>
		</tr>
	{sectionelse}
		<tr class="norecords"><td colspan="9">{tr}No records found{/tr}</td></tr>
	{/section}
</table>

{pagination menu_id=$menu_id page=$page}

{/strip}
