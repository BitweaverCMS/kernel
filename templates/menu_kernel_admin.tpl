{strip}
<ul>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=packages" title="{tr}Packages{/tr}" >{tr}Packages{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=features" title="{tr}Features{/tr}" >{tr}Features{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=general" title="{tr}General{/tr}" >{tr}General Settings{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=server" title="{tr}Server{/tr}" >{tr}Server Settings{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/backup.php">{tr}Backups{/tr}</a></li>
	{if $gBitSystem->isFeatureActive( 'banning' ) and ($gBitUser->hasPermission( 'bit_p_admin_banning' ))}
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/admin_banning.php">{tr}Banning{/tr}</a></li>
	{/if}
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/db_performance.php">{tr}Database Performance{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php">{tr}System Cache{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/list_cache.php">{tr}Link Cache{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/phpinfo.php">{tr}PHPinfo{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/admin_notifications.php">{tr}Notification{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?version_check=1">{tr}Check Version{/tr}</a></li>
</ul>
{/strip}
