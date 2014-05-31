{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin system">
	<div class="header">
		<h1>{tr}Cache Administration{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}
		{legend legend="Exterminator"}
			<div class="control-group column-group gutters">
				{formlabel label="Clear entire cache" for=""}
				{forminput}
					<a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php?prune=all">{tr}Empty All{/tr}</a>
					{formhelp note="This will clear out all cache files in all the directories listed below."}
				{/forminput}
			</div>

			<table class="table data">
				<caption>{tr}List of Cached Files{/tr}</caption>
				<tr>
					<th class="width25p">{tr}Cache Area{/tr}</th>
					<th class="width30p">{tr}Relative Path{/tr}</th>
					<th class="width15p">{tr}Files{/tr}</th>
					<th class="width15p">{tr}Size{/tr}</th>
					<th class="width15p">{tr}Actions{/tr}</th>
				</tr>
				{foreach from=$diskUsage key=key item=item}
					<tr class="{cycle values='odd,even'}">
						<td>{$item.title}</td>
						<td>{$item.path|replace:$smarty.const.BIT_ROOT_PATH:""|replace:"//":"/"}</td>
						<td class="alignright">{$item.du.count}</td>
						<td class="alignright">{$item.du.size|display_bytes}</td>
						<td class="actionicon">{smartlink ititle=Empty booticon="icon-trash" prune=$key}</td>
					</tr>
				{/foreach}
			</table>
		{/legend}

		{legend legend="Templates Compiler"}
			<table class="table data">
				<caption>{tr}List of Cached Templates{/tr}</caption>
				<tr>
					<th class="width25p">{tr}Language{/tr}</th>
					<th class="width30p">{tr}Relative Path{/tr}</th>
					<th class="width15p">{tr}Files{/tr}</th>
					<th class="width15p">{tr}Size{/tr}</th>
					<th class="width15p">{tr}Actions{/tr}</th>
				</tr>
				{foreach from=$templates key=key item=item}
					<tr class="{cycle values='odd,even'}">
						<td>{$item.title}</td>
						<td>{$item.path|replace:$smarty.const.BIT_ROOT_PATH:""|replace:"//":"/"}{$key}</td>
						<td class="alignright">{$item.du.count}</td>
						<td class="alignright">{$item.du.size|display_bytes}</td>
						<td class="actionicon">{smartlink ititle="Compile Templates" booticon="icon-edit" compiletemplates=$key}</td>
					</tr>
				{/foreach}
			</table>
		{/legend}
	</div><!-- end .body -->
</div><!-- end .system -->
{/strip}
