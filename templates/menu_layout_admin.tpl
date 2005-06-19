{strip}
<ul>
	<li><a class="item" href="{$gBitLoc.THEMES_PKG_URL}admin/admin_themes_manager.php">{tr}Themes Manager{/tr}</a></li>
	<li><a class="item" href="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=layout" >{tr}Layout{/tr}</a></li>
	<li><a class="item" href="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=modules">{tr}Modules{/tr}</a></li>
	<li><a class="item" href="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=custom_modules">{tr}Custom Modules{/tr}</a></li>
{*	<li><a class="item" href="{$gBitLoc.KERNEL_PKG_URL}admin/index.php?page=menus">{tr}Menus{/tr}</a></li>*}
	{if $gBitSystemPrefs.feature_edit_templates eq 'y' and $gBitUser->hasPermission( 'bit_p_edit_templates' )}
		<li><a class="item" href="{$gBitLoc.THEMES_PKG_URL}edit_templates.php">{tr}Templates{/tr}</a></li>
	{/if}
</ul>
{/strip}
