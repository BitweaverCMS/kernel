{strip}
<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
	<caption>{tr}Current Layout of '{if !$fPackage || $fPackage=='kernel'}Site Default{else}{$fPackage|capitalize}{/if}'{/tr}</caption>
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
									<strong>{tr}Title{/tr}</strong>: {$layout.$area[ix].title}<br />
								{/if}
								{if $layout.$area[ix].cache_time}
									<strong>{tr}Cache Time{/tr}</strong>: {$layout.$area[ix].cache_time}<br />
								{/if}
								{if $layout.$area[ix].rows}
									<strong>{tr}Rows{/tr}</strong>: {$layout.$area[ix].rows}<br />
								{/if}
								{if $layout.$area[ix].params}
									<strong>{tr}Parameters{/tr}</strong>:<br />{$layout.$area[ix].params|replace:"&":"<br />"}
								{/if}

								<div style="text-align:center;">
									{smartlink ititle="Up" ibiticon="liberty/move_up" iforce="icon" page=layout fMove=up fPackage=$fPackage fModule=`$layout.$area[ix].module_id`}
									&nbsp;&nbsp;
									{smartlink ititle="Down" ibiticon="liberty/move_down" iforce="icon" page=layout fMove=down fPackage=$fPackage fModule=`$layout.$area[ix].module_id`}
									&nbsp;&nbsp;
									{if $colkey ne 'center'}
										{smartlink ititle="Move to Right" ibiticon="liberty/move_$colkey" iforce="icon" page=layout fMove=$colkey fPackage=$fPackage fModule=`$layout.$area[ix].module_id`}
									{/if}
									&nbsp;&nbsp;
									{if $column[ix].type ne 'P'}
										{smartlink ititle="Unassign" ibiticon="liberty/delete_small" iforce=icon ionclick="return confirm('Are you sure you want to remove `$layout.$area[ix].name`?');" page=layout fMove=unassign fPackage=$fPackage fModule=`$layout.$area[ix].module_id`}
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

{form action=$smarty.server.PHP_SELF legend="Select Section"}
	<input type="hidden" name="page" value="{$page}" />
	<div class="row">
		{formlabel label="Create Customized layout for" for="fPackage"}
		{forminput}
			<select name="fPackage" id="fPackage">
				<option value="home" {if $fPackage == 'home'}selected="selected"{/if}>{tr}User Homepages{/tr}</option>
				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if $package.installed and ($package.activatable or $package.tables)}
						<option value="{$name}" {if $fPackage == $name}selected="selected"{/if}>{if $name eq 'kernel'}{tr}Site Default{/tr}{else}{tr}{$name|capitalize}{/tr}{/if}</option>
					{/if}
				{/foreach}
			</select>
			{formhelp note="Apply this setting before you customise and assign modules below."}
		{/forminput}
	</div>

	<div class="row submit">
		<input type="submit" name="fSubmitCustomize" value="{tr}Customize{/tr}" />
	</div>
{/form}

{jstabs}
	{jstab title="Assign column module"}
		{form action=$smarty.server.PHP_SELF legend="Assign column module"}
			<input type="hidden" name="page" value="{$page}" />
			<input type="hidden" name="fPackage" value="{$fPackage}" />
			<div class="row">
				{formlabel label="Package"}
				{forminput}
					{tr}{if !$fPackage || $fPackage eq 'kernel'}Site Default{else}{$fPackage}{/if}{/tr}
					{formhelp note="This is the package you are currently editing."}
				{/forminput}
			</div>

			{if $fEdit && $fAssign.name}
				<input type="hidden" name="assign_name" value="{$fAssign.name}" />
			{else}
				<div class="row">
					{formlabel label="Module" for="module_rsrc"}
					{forminput}
						{html_options name="fAssign[module_rsrc]" id="module_rsrc" options=$all_modules selected=`$fAssign.name`}
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
					<input type="text" name="fAssign[title]" id="title" value="{$fAssign.title|escape}" />
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
					<input type="text" name="fAssign[cache_time]" id="cache_time" size="5" value="{$fAssign.cache_time|escape}" /> seconds
					{formhelp note="This is the number of seconds the module is cached before the content is refreshed. The higher the value, the less load there is on the server. (optional)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Rows" for="rows"}
				{forminput}
					<input type="text" name="fAssign[rows]" id="rows" value="{$fAssign.rows|escape}" />
					{formhelp note="Select what the maximum number of items are displayed. (optional - default is 10)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Parameters" for="params"}
				{forminput}
					<input type="text" name="fAssign[params]" id="params" value="{$fAssign.params|escape}" />
					{formhelp note="Here you can enter any additional parameters the module might need. Use the http query string form, e.g. foo=123&amp;bar=ABC (optional)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Groups" for="groups"}
				{forminput}
					<select multiple="multiple" name="groups[]" id="groups">
						{foreach from=$groups key=groupId item=group}
							<option value="{$groupId}" {if $group.selected eq 'y'}selected="selected"{/if}>{$group.group_name}</option>
						{/foreach}
					</select>
					{formhelp note="
						Select the groups of users who can see this module.
						<ul>
							<li>If you select none, the module will be visisble to all groups.</li>
							<li>If you select groups, please make sure you have disabled the setting: 'Display modules to all groups always' in
								<br />Admin --&gt; Layout --&gt; Modules --&gt; Module Settings</li>
						</ul>
					"}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="ColumnTabSubmit" value="{tr}Assign{/tr}" />
			</div>
		{/form}
	{/jstab}

	{jstab title="Assign center piece"}
		{form action=$smarty.server.PHP_SELF legend="Assign center piece"}
			<input type="hidden" name="page" value="{$page}" />
			<input type="hidden" name="fPackage" value="{$fPackage}" />
			<input type="hidden" name="fAssign[pos]" value="c" />

			<div class="row">
				{formlabel label="Package"}
				{forminput}
					{tr}{if !$fPackage || $fPackage eq 'kernel'}Site Default{else}{$fPackage}{/if}{/tr}
					{formhelp note="This is the package you are currently editing."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Center Piece" for="module"}
				{forminput}
					{if $fEdit && $fAssign.name}
						<input type="hidden" name="fAssign[module]" value="{$fAssign.module}" id="module" />{$fAssign.module}
					{else}
						{html_options name="fAssign[module_rsrc]" id="module" values=$all_centers options=$all_centers selected=`$mod`}
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
					<input type="text" name="fAssign[rows]" id="c_rows" value="{$fAssign.rows|escape}" />
					{formhelp note="Select what the maximum number of items are displayed. (optional - default is 10)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Parameters" for="c_params"}
				{forminput}
					<input type="text" name="fAssign[params]" id="c_params" value="{$fAssign.params|escape}" />
					{formhelp note="Here you can enter any additional parameters the module might need. (optional)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Groups" for="c_groups"}
				{forminput}
					<select multiple="multiple" name="groups[]" id="c_groups">
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

	{jstab title="Miscellaneous Settigns"}
		{form action=$smarty.server.PHP_SELF legend="Miscellaneous Settigns"}
			<input type="hidden" name="page" value="{$page}" />
			{foreach from=$formMiscFeatures key=feature item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$feature}
					{forminput}
						{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
						{formhelp hash=$output}
					{/forminput}
				</div>
			{/foreach}

			<div class="row submit">
				<input type="submit" name="MiscTabSubmit" value="{tr}Change preferences{/tr}" />
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
