{strip}
{if $packageMenu}
	{bitmodule title="$moduleTitle" name="package_menu"}
		<div class="menu">
			{include file=$packageMenu.menu_template packageMenuClass="unstyled"}
		</div>
	{/bitmodule}
{/if}
{/strip}
