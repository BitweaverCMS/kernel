{strip}
{bitmodule title="$moduleTitle" name="packages_links"}
	<ul id="nav2" class="menu">
		{foreach key=key item=menu from=$gBitSystem->mAppMenu}
			{if $menu.template}
				<li><a class="head" href="{$menu.titleUrl}">{$menu.title}</a></li>
			{/if}
		{/foreach}

		{if $gBitUser->isAdmin()}
			<li><a class="head" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}Administration{/tr}</a></li>
		{/if}
	</ul>
{/bitmodule}
{/strip}
