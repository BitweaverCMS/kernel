{strip}
<nav class="ink-navigation">
	<ul class="menu horizontal blue">
		{foreach key=key item=menu from=$gBitSystem->mAppMenu}
				{if $menu.menu_title && $menu.index_url && $menu.menu_template && !$menu.is_disabled}
				{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}
				<li class="{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name}active{/if}">
					{include file=$menu.menu_template packageMenuClass="submenu" packageMenuTitle=$menu.menu_title}
				</li>
				{else}
				<li class="m-{$key}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} active{/if}">
					<a accesskey="{$key|truncate:1:""}" class="{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} selected{/if}" href="{$menu.index_url}">{tr}{$menu.menu_title}{/tr}</a>
				</li>
				{/if}
			{/if}
		{/foreach}
	</ul>
</nav>
{/strip}
