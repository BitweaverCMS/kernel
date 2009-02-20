{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_package_menu.tpl,v 1.13 2009/02/20 19:54:50 spiderr Exp $ *}
{strip}
{if $packageMenu}
	{bitmodule title="$moduleTitle" name="package_menu"}
		<div class="menu">
			{include file=$packageMenu.menu_template}
		</div>
	{/bitmodule}
{/if}
{/strip}
