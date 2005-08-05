{strip}

{jstabs}
		{if $menu_id > 0}
		{jstab title="Edit {$name} Menu"}
		{else}
		{jstab title="Create new Menu"}
		{/if}
		{if $menu_id > 0}
			<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}">{tr}Create new Menu{/tr}</a>
		{/if}
		{form legend="Edit/Create new Menu"}
			<input type="hidden" name="page" value="{$page}" />
			<input type="hidden" name="menu_id" value="{$menu_id|escape}" />
			<div class="row">
				{formlabel label="Name" for="menus_name"}
				{forminput}
					<input type="text" name="name" id="menus_name" value="{$name|escape}" />
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Description" for="menus_desc"}
				{forminput}
					<textarea name="description" id="menus_desc" rows="4" cols="40">{$description|escape}</textarea>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Type" for="menus_type"}
				{forminput}
					<select name="type" id="menus_type">
						<option value="d" {if $type eq 'd'}selected="selected"{/if}>{tr}dynamic collapsed{/tr}</option>
						<option value="e" {if $type eq 'e'}selected="selected"{/if}>{tr}dynamic extended{/tr}</option>
						<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}fixed{/tr}</option>
					</select>
					{formhelp note="<dl><dt>dynamic collapsed</dt><dd>When accessing the site for the first time, the menus will be dynamic and collapsed.</dd><dt>dynamic extended</dt><dd>When accessing the site for the first time, the menus will be dynamic and expanded with all links visible.</dd><dt>fixed</dt><dd>The menu will not be dynamic. this means that all links are visible and cannot be collapsed/hidden.</dd></dl>"}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</div>
		{/form}

		<h2>{tr}List of configured menus{/tr}</h2>

		<table class="data">
			<tr>
				<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'menu_id_asc'}menu_id_desc{else}menu_id_asc{/if}">{tr}ID{/tr}</a></th>
				<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_asc'}name_desc{else}name_asc{/if}">{tr}Name{/tr}</a></th>
				<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_asc'}description_desc{else}description_asc{/if}">{tr}Description{/tr}</a></th>
				<th><a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_asc'}type_desc{else}type_asc{/if}">{tr}Type{/tr}</a></th>
				<th>{tr}Options{/tr}</th>
				<th>{tr}Action{/tr}</th>
			</tr>

			{cycle values="even,odd" print=false}
			{section name=user loop=$channels}
				<tr class="{cycle}">
					<td>{$channels[user].menu_id}</td>
					<td>{$channels[user].name}</td>
					<td>{$channels[user].description}</td>
					<td>{$channels[user].type}</td>
					<td>{$channels[user].options}</td>
					<td class="actionicon">
						<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;menu_id={$channels[user].menu_id}" title="{tr}Edit this menu{/tr}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
						<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=menu_options&amp;menu_id={$channels[user].menu_id}" title="{tr}Configure this menu{/tr}">{biticon ipackage=liberty iname="config" iexplain="configure"}</a>
						<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].menu_id}" 
							onclick="return confirm('{tr}Are you sure you want to delete this menu?{/tr}')" title="{tr}Delete this menu{/tr}">{biticon ipackage=liberty iname="delete" iexplain="remove"}</a>
					</td>
				</tr>
			{sectionelse}
				<tr class="norecords"><td colspan="6">{tr}No records found{/tr}</td></tr>
			{/section}
		</table>

		{pagination page=$page}

		{minifind sort_mode=$sort_mode page=$page}

	{/jstab}

	{jstab title="Global Menu Settings"}
		{form legend="Global Menu Settings"}
			<input type="hidden" name="page" value="{$page}" />
			{foreach from=$formMenuFeatures key=feature item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$feature}
					{forminput}
						{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
						{formhelp note=`$output.note` page=`$output.page`}
					{/forminput}
				</div>
			{/foreach}
			<div class="row submit">
				<input type="submit" name="menu_features" value="{tr}Change preferences{/tr}" />
			</div>
		{/form}
	{/jstab}
{/jstabs}
{/strip}
