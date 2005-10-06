{strip}
{bitmodule title="$moduleTitle" name="packages_links"}
	<ul id="nav2" class="menu">
		{foreach key=key item=menu from=$gBitSystem->mAppMenu}
			<li><a class="head" href="{$menu.titleUrl}">{$menu.title}</a></li>
		{/foreach}

		{if $gBitUser->isAdmin()}
			<li><a class="head" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}Administration{/tr}</a></li>
		{/if}
	</ul>
{/bitmodule}
{/strip}
