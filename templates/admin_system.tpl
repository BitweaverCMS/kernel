{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin system">
	<div class="header">
		<h1>{tr}Cache Administration{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}
		{legend legend="Exterminator"}
			<div class="row">
				{formlabel label="Clear entire cache" for=""}
				{forminput}
					<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php?prune=all">{tr}Empty All{/tr}</a>
					{formhelp note="This will clear out all cache files in all the directories listed below."}
				{/forminput}
			</div>

			<table class="data">
				<caption>{tr}List of Cached Files{/tr}</caption>
				<tr>
					<th style="width:25%;">{tr}Cache Area{/tr}</th>
					<th style="width:30%;">{tr}Relative Path{/tr}</th>
					<th style="width:15%;">{tr}Files{/tr}</th>
					<th style="width:15%;">{tr}Size{/tr}</th>
					<th style="width:15%;">{tr}Actions{/tr}</th>
				</tr>
				{foreach from=$diskUsage key=key item=item}
					<tr class="{cycle values='odd,even'}">
						<td>{$item.title}</td>
						<td>{$item.path|replace:$smarty.const.BIT_ROOT_PATH:""|replace:"//":"/"}</td>
						<td style="text-align:right;">{$item.du.count}</td>
						<td style="text-align:right;">{$item.du.size|display_bytes}</td>
						<td class="actionicon">{smartlink ititle=Empty ibiticon="icons/edit-delete" prune=$key}</td>
					</tr>
				{/foreach}
			</table>
		{/legend}

		{legend legend="Templates Compiler"}
			<table class="data">
				<caption>{tr}List of Cached Templates{/tr}</caption>
				<tr>
					<th style="width:25%;">{tr}Language{/tr}</th>
					<th style="width:30%;">{tr}Relative Path{/tr}</th>
					<th style="width:15%;">{tr}Files{/tr}</th>
					<th style="width:15%;">{tr}Size{/tr}</th>
					<th style="width:15%;">{tr}Actions{/tr}</th>
				</tr>
				{foreach from=$templates key=key item=item}
					<tr class="{cycle values='odd,even'}">
						<td>{$item.title}</td>
						<td>{$item.path|replace:$smarty.const.BIT_ROOT_PATH:""|replace:"//":"/"}{$key}</td>
						<td style="text-align:right;">{$item.du.count}</td>
						<td style="text-align:right;">{$item.du.size|display_bytes}</td>
						<td class="actionicon">{smartlink ititle="Compile Templates" ibiticon="icons/accessories-text-editor" compiletemplates=$key}</td>
					</tr>
				{/foreach}
			</table>
		{/legend}
	</div><!-- end .body -->
</div><!-- end .system -->
{/strip}
