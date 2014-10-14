{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin system">
	<header>
		<h1>{tr}System Caching{/tr}</h1>
	</header>

	<div class="body">
		{formfeedback hash=$feedback}

	{jstabs}
		{jstab title="Opcode and memory caching"}

		<div class="form-group">
			{formlabel label="Object Caching"}
			{forminput}
				{if !function_exists('apc_store')}
					<div class="alert alert-error">{tr}The APC-U PHP extension must also be installed.{/tr}</div>
				{/if}
				{if $gBitSystem->isCacheActive()}
					<div class="inline-block alert alert-success">{tr}Active{/tr}: APC</div>
					{formhelp note="To disable object caching, edit your config_inc.php and add this line <strong>define( 'BIT_CACHE_OBJECTS', FALSE );</strong>."}
				{else}
					<div class="inline-block alert alert-warning">{tr}Disabled{/tr}</div>
					{formhelp note="To disable object caching, edit your config_inc.php and add this line <strong>define( 'BIT_CACHE_OBJECTS', FALSE );</strong>."}
				{/if}
			{/forminput}
		</div>

		{/jstab}
		
		{jstab title="File Caching"}

			{legend legend="All Cached Files"}
				<div class="form-group">
					{forminput}
						<a class="btn btn-warning" href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php?prune=all">{tr}Clear All Cache{/tr}</a>
						{formhelp note="This will clear out all cache files in all the directories listed below."}
					{/forminput}
				</div>

				<table class="table data">
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

			{legend legend="Cached Template Files by Language"}
				<table class="table data">
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
		{/jstab}
	{/jstabs}
	</div><!-- end .body -->
</div><!-- end .system -->
{/strip}
