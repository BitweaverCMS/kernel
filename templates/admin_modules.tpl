{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/Attic/admin_modules.tpl,v 1.1 2005/06/19 04:52:54 bitweaver Exp $ *}

{$moduleJavascript}

{jstabs}
	{jstab title="Edit Modules"}

		{form legend="Edit Modules" onsubmit="PostSubmitProcess()"}
			<input type="hidden" name="page" value="{$page}" />
			<input type="hidden" name="fPackage" value="{$fPackage}" />
			<input type="hidden" id="fPreIndex" name="fPreIndex" value="0" />  {* Holds the index the select box had BEFORE it was changed (used to store the row/params/etc) *}
			{foreach from=$availHash item=module key=mod }
				<input type="hidden" id="fModuleAction{$mod}" name="fModuleAction[{$module.module_rsrc}]" value="none" />
				<input type="hidden" id="fModuleParams{$mod}" name="fModuleParams{$mod}" value="" />
				<input type="hidden" id="fModuleCacheTime{$mod}" name="fModuleCacheTime{$mod}" value="{$module.cache_time}" />
				<input type="hidden" id="fModuleRows{$mod}" name="fModuleRows{$mod}" value="{$module.rows}" />
				<input type="hidden" id="fModuleGroups{$mod}" name="fModuleGroups{$mod}" value="" />
			{/foreach}

			<div class="row">
				{formlabel label="Edit Module" for="moduleSelectId"}
				{forminput}
					<select id="moduleSelectId" name="fAssign[module_rsrc]" onchange="UpdateModuleWidget(this)">
						{foreach from=$availHash item=module key=mod }
							<option style="color: #ffffff" value="{$module.module_rsrc|escape}">{$module.name}</option>
						{/foreach}
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Status" for="moduleToggle"}
				{forminput}
					<input id="moduleStatusText" style="border-style:none;" value="uninitialized" />
					<input id="moduleToggle" type="button" onclick="ToggleModule()" value="Toggle Status" />
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Cache Time (seconds)" for="fCacheTime"}
				{forminput}
					<input size="10" type="text" name="fAssign[cache_time]" id="fCacheTime" value="" onkeypress="OnTextChange()" /> seconds
					{formhelp note="This is the number of seconds the module is cached before the content is refreshed. The higher the value, the less load there is on the server. (optional)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Rows" for="fRows"}
				{forminput}
					<input type="text" name="fAssign[rows]" id="fRows" value="{$fAssign.rows|escape}" onkeypress="OnTextChange()" />
					{formhelp note="Select what the maximum number of items are displayed. (optional - default is 10)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Parameters" for="fParams"}
				{forminput}
					<input type="text" name="fAssign[params]" id="fParams" value="{$fAssign.params|escape}" onkeypress="OnTextChange()" />
					{formhelp note="Here you can enter any additional parameters the module might need. (optional)"}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Groups" for="grp"}
				{forminput}
					<select multiple="multiple" name="groups[]" onchange="OnTextChange()" id="grp">
						{foreach name=groupList key=groupId from=$groups item=group}
							<option value="{$groupId}" id="fGroup{math equation="x - 1" x=$smarty.foreach.groupList.iteration }" >{$group.group_name}</option>
						{/foreach}
					</select>
					{formhelp note="Select the groups of users who can see this module when active."}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="fCancel" value="{tr}Cancel{/tr}" />
				<input type="submit" name="EditTabSubmit" value="{tr}Save changes{/tr}" />
			</div>

			{formhelp note="Enabling modules here does not modify the page layout. This page is used to specify what users can use what modules when modifying their own personal layout.<br />To change the module layout of your site please visit Administration --&gt; Layout and Design --&gt; Layout"}
		{/form}

		{if $actionsTaken}
			<h2>Action Summary</h2>
			<p>
				The following actions were performed on the last form submit
			</p>
			<ul>
				{foreach key=actionIndex from=$actionSummary item=action}
					<li>
						{if $action.actionType == 'enable'} Enabled {/if}
						{if $action.actionType == 'disable'} Disabled {/if}
						{$action.moduleName}
					</li>
				{/foreach}
			</ul>
		{/if}
	{/jstab}

	{jstab title="Module Settings"}
		{form legend="Global Module Settings"}
			<input type="hidden" name="page" value="{$page}" />
			{foreach from=$formModuleFeatures key=feature item=output}
				<div class="row">
					{formlabel label=`$output.label` for=$feature}
					{forminput}
						{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
						{formhelp note=`$output.note` page=`$output.page`}
					{/forminput}
				</div>
			{/foreach}
			<div class="row submit">
				<input type="submit" name="ModulesTabSubmit" value="{tr}Change preferences{/tr}" />
			</div>
		{/form}
	{/jstab}
{/jstabs}

{literal}
<script type="text/javascript">
	document.getElementById('fParams').value = modArray[0].params;
	document.getElementById('fCacheTime').value = modArray[0].cacheTime;
	document.getElementById('fRows').value = modArray[0].rows;
	LoadGroupSettings(0);
	UpdateModuleWidget(document.getElementById('moduleSelectId'));
</script>
{/literal}
