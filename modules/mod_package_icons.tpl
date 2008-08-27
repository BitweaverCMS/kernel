{strip}
{bitmodule title="$moduleTitle" name="packages_icons"}
	{foreach key=key item=menu from=$gBitSystem->mAppMenu}
		{if $menu.menu_template}
			<a class="{if $gBitSystem->mActivePackage eq $key}active{/if}" href="{$menu.index_url}">{biticon ipackage=$key iname="pkg_`$key`" iexplain=$menu.menu_title}</a>
		{/if}
	{/foreach}
{/bitmodule}
{/strip}
