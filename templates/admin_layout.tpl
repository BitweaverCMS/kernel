{literal}
<script type="text/javascript">//<![CDATA[
	function initDragDrop() {
		var list = $( "left" );
		DragDrop.makeListContainer( list, 'side_columns' );
		list.onDragOver = function() { this.style["background"] = "#feb"; };
		list.onDragOut = function() {this.style["background"] = "none"; };

		list = $( "center" );
		DragDrop.makeListContainer( list, 'center_column' );
		list.onDragOver = function() { this.style["background"] = "#dfe"; };
		list.onDragOut = function() {this.style["background"] = "none"; };

		list = $( "right" );
		DragDrop.makeListContainer( list, 'side_columns' );
		list.onDragOver = function() { this.style["background"] = "#feb"; };
		list.onDragOut = function() {this.style["background"] = "none"; };
	};
	
	function getSort( id ) {
		order = $( id );
		order.value = DragDrop.serData( id, null );
	}
//]]></script>
{/literal}

{strip}
{formfeedback hash=$feedback}

{* keep old layout option available for non js browsers *}
{if $smarty.request.nojs}

	<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
		<caption>{tr}Current Layout of '{if !$module_package || $module_package=='kernel'}Site Default{else}{$module_package|capitalize}{/if}'{/tr}</caption>
		<tr>
			{foreach from=$layoutAreas item=area key=colkey }
				<td style="width:33%" valign="top">
					<table class="data" style="width:100%">
						<tr>
							<th>{tr}{$colkey} column{/tr}</th>
						</tr>
						{section name=ix loop=$layout.$area}
							<tr class="{cycle values="even,odd"}">
								<td>
									<div class="highlight">{$layout.$area[ix].name}</div>
									<strong>{tr}Position{/tr}</strong>: {$layout.$area[ix].ord}
									<br />

									{if $layout.$area[ix].title}
										<strong>{tr}Title{/tr}</strong>: {$layout.$area[ix].title|escape}<br />
									{/if}
									{if $layout.$area[ix].cache_time}
										<strong>{tr}Cache Time{/tr}</strong>: {$layout.$area[ix].cache_time}<br />
									{/if}
									{if $layout.$area[ix].module_rows}
										<strong>{tr}Rows{/tr}</strong>: {$layout.$area[ix].module_rows}<br />
									{else}
										<strong>{tr}Rows{/tr}</strong>: {$gBitSystem->getConfig('max_records')} <em>{tr}Default{/tr}</em><br />
									{/if}
									{if $layout.$area[ix].params}
										<strong>{tr}Parameters{/tr}</strong>:<br />{$layout.$area[ix].params|replace:"&":"<br />"}
									{/if}

									<div style="text-align:center;">
										{smartlink ititle="Up" ibiticon="liberty/move_up" iforce="icon" page=layout move_module=up module_package=$module_package module=`$layout.$area[ix].module_id` nojs=true}
										&nbsp;&nbsp;
										{smartlink ititle="Down" ibiticon="liberty/move_down" iforce="icon" page=layout move_module=down module_package=$module_package module=`$layout.$area[ix].module_id` nojs=true}
										&nbsp;&nbsp;
										{if $colkey ne 'center'}
											{if $colkey == 'left'}
												{assign var=dir value=right}
											{elseif $colkey == 'right'}
												{assign var=dir value=left}
											{/if}
											{smartlink ititle="Move to $dir" ibiticon="liberty/move_$dir" iforce="icon" page=layout move_module=$colkey module_package=$module_package module=`$layout.$area[ix].module_id` nojs=true}
										{/if}
										&nbsp;&nbsp;
										{if $column[ix].type ne 'P'}
											{smartlink ititle="Unassign" ibiticon="icons/edit-delete" iforce=icon ionclick="return confirm('Are you sure you want to remove `$layout.$area[ix].name`?');" page=layout move_module=unassign module_package=$module_package module=`$layout.$area[ix].module_id` nojs=true ord=`$layout.$area[ix].ord`}
										{/if}
									</div>
								</td>
							</tr>
						{sectionelse}
							<tr class="{cycle values="even,odd"}" >
								<td colspan="3" align="center">
									{if $colkey eq 'center'}{tr}Default{/tr}{else}{tr}None{/tr}{/if}
								</td>
							</tr>
						{/section}
					</table>
				</td>
			{/foreach}
		</tr>
	</table>

	<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}">{tr}Use javascript variant{/tr}</a>
{else}

	{form}
		<table class="layout" style="width:100%" cellpadding="5" cellspacing="0" border="0">
			<caption>{tr}Current Layout of '{if !$module_package || $module_package=='kernel'}Site Default{else}{$module_package|capitalize}{/if}'{/tr}</caption>
			<tr>
				{foreach from=$layoutAreas item=area key=colkey}
					<td style="width:33%" valign="top">
						<h2>{$colkey} column</h2>
						<ul id="{$colkey}" class="sortable boxy">
							{section name=ix loop=$layout.$area}
								<li id="module_id-{$layout.$area[ix].module_id}">
									{if $colkey ne 'center'}
										<strong>{tr}Title{/tr}</strong>: <input size="20" name="modules[{$layout.$area[ix].module_id}][title]" value="{$layout.$area[ix].title|escape}" /><br />
										<strong>{tr}Rows{/tr}</strong>: <input size="5" name="modules[{$layout.$area[ix].module_id}][module_rows]" value="{$layout.$area[ix].module_rows}" /><br />
									{/if}
									<strong>{tr}Module{/tr}</strong>: {$layout.$area[ix].name|escape}<br />
									{if $layout.$area[ix].cache_time}
										<strong>{tr}Cache Time{/tr}</strong>: {$layout.$area[ix].cache_time}<br />
									{/if}
									{if $layout.$area[ix].params}
										<strong>{tr}Parameters{/tr}</strong>:<br />{$layout.$area[ix].params|replace:"&":"<br />"}<br />
									{/if}
									{if $column[ix].type ne 'P'}
										<input type="submit" name="unassign[{$layout.$area[ix].module_id}]" value="{tr}Remove Module{/tr}" onclick="javascript:getSort('center_column');getSort('side_columns');this.form.submit;" />
									{/if}
								</li>
							{sectionelse}
								<li id="module_id-{$layout.$area[ix].module_id}">
									<strong>{if $colkey eq 'center'}{tr}Default{/tr}{else}{tr}None{/tr}{/if}</strong>
								</li>
							{/section}
						</ul>
					</td>
				{/foreach}
			</tr>
		</table>

		<input type="hidden" name="side_columns" id="side_columns" value="" />
		<input type="hidden" name="center_column" id="center_column" value="" />
		<input type="hidden" name="module_package" value="{$module_package}" />
		<input type="hidden" name="page" value="{$page}" />

		<div class="row submit">
			<input type="submit" name="apply_layout" value="{tr}Apply Layout{/tr}" onclick="javascript:getSort('center_column');getSort('side_columns');this.form.submit;" />
			<input type="submit" name="reset" value="{tr}Reset{/tr}" />
		</div>
		{formhelp note="Drag and drop the modules where you want them, then hit the <strong>Apply Layout</strong> button."}
	{/form}

	<a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page={$page}&amp;nojs=true">{tr}Use non-javascript variant{/tr}</a>
{/if}

{form legend="Select Section"}
	<input type="hidden" name="page" value="{$page}" />
	<input type="hidden" name="nojs" value="{$smarty.request.nojs}" />
	<div class="row">
		{formlabel label="Create Customized layout for" for="module_package"}
		{forminput}
			<select name="module_package" id="module_package" onchange="this.form.submit();">
				<option value="home" {if $module_package == 'home'}selected="selected"{/if}>{tr}User Homepages{/tr}</option>
				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if $package.installed and ($package.activatable or $package.tables)}
						<option value="{$name}" {if $module_package == $name}selected="selected"{/if}>
							{if $name eq 'kernel'}
								{tr}Site Default{/tr}
							{else}
								{tr}{$name|capitalize}{/tr}
							{/if}
						</option>
					{/if}
				{/foreach}
			</select>

			<noscript>
				{formhelp note="Apply this setting before you customise and assign modules below."}
			</noscript>
		{/forminput}
	</div>

	<noscript>
		<div class="row submit">
			<input type="submit" name="fSubmitCustomize" value="{tr}Customize{/tr}" />
		</div>
	</noscript>
{/form}

{jstabs}
	{jstab title="Assign column module"}
		{form action=$smarty.server.PHP_SELF legend="Assign column module"}
			<input type="hidden" name="page" value="{$page}" />
			<input type="hidden" name="module_package" value="{$module_package}" />
			<div class="row">
				{formlabel label="Package"}
				{forminput}
					<span class="highlight">{tr}{if !$module_package || $module_package eq 'kernel'}Site Default{else}{$module_package|capitalize}{/if}{/tr}</span>
					{formhelp note="This is the package you are currently editing."}
				{/forminput}
			</div>

			{if $fEdit && $fAssign.name}
				<input type="hidden" name="assign_name" value="{$fAssign.name}" />
			{else}
				<div class="row">
					{formlabel label="Module" for="module_rsrc"}
					{forminput}
						{html_options name="fAssign[module_rsrc]" id="module_rsrc" options=$allModules selected=`$fAssign.name`}
						{formhelp note="Extended help can be found at the end of this page."}
					{/forminput}
				</div>
			{/if}

			<div class="row">
				{formlabel label="Position" for="pos"}
				{forminput}
					<select name="fAssign[pos]" id="pos">
						<option value="l" {if $fAssign.pos eq 'l'}selected="selected"{/if}>{tr}left column{/tr}</option>
						<option value="r" {if $fAssign.pos eq 'r'}selected="selected"{/if}>{tr}right column{/tr}</option>
					</select>
					{formhelp note="Select the column this module should be displayed in."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Title" for="title"}
				{forminput}
					<input type="text" size="48" name="fAssign[title]" id="title" value="{$fAssign.title|escape}" />
					{formhelp note="Here you can override the default title used by the module. This is global for layouts in all sections. If you want to add a title just for one section, enter a module parameter below such as: title=My Title"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Order" for="ord"}
				{forminput}
					<select name="fAssign[ord]" id="ord">
						{section name=ix loop=$orders}
							<option value="{$orders[ix]|escape}" {if $fAssign.ord eq $orders[ix]}selected="selected"{/if}>{$orders[ix]}</option>
						{/section}
					</select>
					{formhelp note="Select where within the column the module should be displayed."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Cache Time" for="cache_time"}
				{forminput}
					<input type="text" size="5" name="fAssign[cache_time]" id="cache_time" value="{$fAssign.cache_time|escape}" /> seconds
					{formhelp note="This is the number of seconds the module is cached before the content is refreshed. The higher the value, the less load there is on the server. (optional)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Rows" for="module_rows"}
				{forminput}
					<input type="text" size="5" name="fAssign[module_rows]" id="module_rows" value="{$fAssign.module_rows|escape}" />
					{formhelp note="Select what the maximum number of items are displayed. (optional - default is 10)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Parameters" for="params"}
				{forminput}
					<input type="text" size="48" name="fAssign[params]" id="params" value="{$fAssign.params|escape}" />
					{formhelp note="Here you can enter any additional parameters the module might need. Use the http query string form, e.g. foo=123&amp;bar=ABC (optional)"}
				{/forminput}
			</div>

			{if $gBitSystem->isFeatureActive( 'site_show_all_modules_always' )}
				<div class="row">
					{formhelp link="kernel/admin/index.php?page=modules/Module Settings" note="If you wish to restrict modules by group, please disable 'Display modules to all groups always'"}
				</div>
			{else}
				<div class="row">
					{formlabel label="Groups" for="groups"}
					{forminput}
						<select multiple="multiple" size="5" name="groups[]" id="groups">
							{foreach from=$groups key=groupId item=group}
								<option value="{$groupId}" {if $group.selected eq 'y'}selected="selected"{/if}>{$group.group_name}</option>
							{/foreach}
						</select>
						{formhelp note="Select the groups of users who can see this module. If you select no group, the module will be visible to all users."}
					{/forminput}
				</div>
			{/if}

			<div class="row submit">
				<input type="submit" name="ColumnTabSubmit" value="{tr}Assign{/tr}" />
			</div>
		{/form}
	{/jstab}

	{jstab title="Assign center piece"}
		{form action=$smarty.server.PHP_SELF legend="Assign center piece"}
			<input type="hidden" name="page" value="{$page}" />
			<input type="hidden" name="module_package" value="{$module_package}" />
			<input type="hidden" name="fAssign[pos]" value="c" />
			<input type="hidden" name="nojs" value="{$smarty.request.nojs}" />

			<div class="row">
				{formlabel label="Package"}
				{forminput}
					<span class="highlight">{tr}{if !$module_package || $module_package eq 'kernel'}Site Default{else}{$module_package|capitalize}{/if}{/tr}</span>
					{formhelp note="This is the package you are currently editing."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Center Piece" for="module"}
				{forminput}
					{if $fEdit && $fAssign.name}
						<input type="hidden" name="fAssign[module]" value="{$fAssign.module}" id="module" />{$fAssign.module}
					{else}
						{html_options name="fAssign[module_rsrc]" id="module" values=$allCenters options=$allCenters selected=`$mod`}
					{/if}
					{formhelp note="Pick the center bit you want to display when accessing this package."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Position"}
				{forminput}
					{tr}Center{/tr}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Order" for="c_ord"}
				{forminput}
					<select name="fAssign[ord]" id="c_ord">
						{section name=ix loop=$orders}
							<option value="{$orders[ix]|escape}" {if $assign_order eq $orders[ix]}selected="selected"{/if}>{$orders[ix]}</option>
						{/section}
					</select>
					{formhelp note="Select where within the column the module should be displayed."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Cache Time" for="c_cache_time"}
				{forminput}
					<input type="text" name="fAssign[cache_time]" id="c_cache_time" size="5" value="{$fAssign.cache_time|escape}" /> seconds
					{formhelp note="This is the number of seconds the module is cached before the content is refreshed. The higher the value, the less load there is on the server. (optional)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Rows" for="c_rows"}
				{forminput}
					<input type="text" size="5" name="fAssign[module_rows]" id="c_rows" value="{$fAssign.module_rows|escape}" />
					{formhelp note="Select what the maximum number of items are displayed. (optional - default is 10)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Parameters" for="c_params"}
				{forminput}
					<input type="text" size="48" name="fAssign[params]" id="c_params" value="{$fAssign.params|escape}" />
					{formhelp note="Here you can enter any additional parameters the module might need. (optional)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Groups" for="c_groups"}
				{forminput}
					<select multiple="multiple" size="5" name="groups[]" id="c_groups">
						{section name=ix loop=$groups}
							<option value="{$groups[ix].group_name|escape}" {if $groups[ix].selected eq 'y'}selected="selected"{/if}>{$groups[ix].group_name}</option>
						{/section}
					</select>
					{formhelp note="Select the groups of users who can see this module."}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="CenterTabSubmit" value="{tr}Assign{/tr}" />
			</div>
		{/form}
	{/jstab}

	{jstab title="Column Control"}
		{form legend="Column Visibility"}
			<input type="hidden" name="page" value="{$page}" />

			{foreach from=$formMiscFeatures key=feature item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$feature}
					{forminput}
						{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
						{formhelp hash=$output}
					{/forminput}
				</div>
			{/foreach}

			{foreach from=$hideColumns item=name key=package}
				<div class="row">
					{formlabel label=$name}
					{forminput}
						<label>
							<input type="checkbox" name="hide[{$package}_hide_left_col]" value="y" {if $gBitSystem->isFeatureActive("`$package`_hide_left_col")}checked="checked"{/if} /> {tr}Hide Left Column{/tr}
						</label>
						<br />
						<label>
							<input type="checkbox" name="hide[{$package}_hide_right_col]"  value="y" {if $gBitSystem->isFeatureActive("`$package`_hide_right_col")}checked="checked"{/if} /> {tr}Hide Right Column{/tr}
						</label>
					{/forminput}
				</div>
			{/foreach}

			<div class="row submit">
				<input type="submit" name="HideTabSubmit" value="{tr}Change preferences{/tr}" />
			</div>
		{/form}
	{/jstab}
{/jstabs}

<h1>{tr}Modules Help{/tr}</h1>
{formhelp note="Below you can find information on what modules do and what parameters they take. If a module is not listed, the module probably doesn't take any special parameters." page="ModuleParameters"}
<noscript><div>{smartlink ititle="Expand Help" page=$page expand_all=1}</div></noscript>
{foreach from=$allModulesHelp key=package item=help}
	<h2><a href="javascript:flip('id{$package}')">{$package}</a></h2>
	<div id="id{$package}" {if !$smarty.request.expand_all}style="display:none;"{/if}>
		{foreach from=$help key=file item=title}
			{box title=$title}
				{include file=$file}
			{/box}
		{/foreach}
	</div>
{/foreach}
{/strip}
