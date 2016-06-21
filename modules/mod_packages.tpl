{strip}
{bitmodule title="$moduleTitle" name="packages_links"}
	<ul class="menu">
		{foreach key=key item=menu from=$gBitSystem->mAppMenu}
			{if $menu.menu_template}
				<li{if $gBitSystem->getActivePackage() eq $menu.package_name} class="active"{/if}><a class="head" href="{$menu.index_url}">{$menu.menu_title}</a></li>
			{/if}
		{/foreach}

		{if $gBitUser->isAdmin()}
			<li{if $gBitSystem->getActivePackage() eq 'kernel'} class="active"{/if}><a class="head" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}Administration{/tr}</a></li>
		{/if}
	</ul>
{/bitmodule}
{/strip}
