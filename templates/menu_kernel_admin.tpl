{strip}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=features">{tr}Kernel{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=packages">{tr}Packages{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=server">{tr}Server{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php">{tr}File Caching{/tr}</a></li>
	{if extension_loaded('apc')}
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/apc.php">{tr}APCu Cache{/tr}</a></li>
	{/if}
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/admin_notifications.php">{tr}Notification{/tr}</a></li>
		{if $smarty.const.DB_PERFORMANCE_STATS eq 'TRUE'}
			<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/db_performance.php">{tr}Database Performance{/tr}</a></li>
		{/if}
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/phpinfo.php">{tr}PHPinfo{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?version_check=1">{tr}Check Version{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/sitemaps.php">{tr}Sitemaps{/tr}</a></li>
</ul>
{/strip}
