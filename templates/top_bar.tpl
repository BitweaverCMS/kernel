{strip}
<div id="bittopbar">
	<ul id="nav" class="menu hor">
		{if $use_custom_top_bar and $gBitSystem->getConfig('nexus_top_bar') eq 'replace'}
			{include file="`$smarty.const.TEMP_PKG_PATH`nexus/modules/top_bar_inc.tpl"}
		{else}
			{if $use_custom_top_bar and $gBitSystem->getConfig('nexus_top_bar') eq 'left'}
				{include file="`$smarty.const.TEMP_PKG_PATH`nexus/modules/top_bar_inc.tpl"}
			{/if}

			<li class="m-home">
				<a class="head" accesskey="h" href="{$smarty.const.BIT_ROOT_URL}">{$gBitSystem->getConfig('site_menu_title')|default:$gBitSystem->getConfig('site_title')}</a>
				{include file="bitpackage:kernel/menu_global.tpl"}
			</li>

			{foreach key=key item=menu from=$gBitSystem->mAppMenu}
				{if $menu.menu_title && $menu.index_url && $menu.menu_template && !$menu.is_disabled}
					<li class="m-{$key}{if $smarty.const.ACTIVE_PACKAGE eq $menu.active_package} current{/if}">
						{* crazy MSIE stuff *}
						{if $gBrowserInfo.browser eq 'ie' and $gBrowserInfo.maj_ver lt 7}
							<a accesskey="{$key|truncate:1:""}" class="{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} selected{/if}" href="{$menu.index_url}">{tr}{$menu.menu_title}{/tr}
								{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}
									<table><tr><td>
										{include file="`$menu.menu_template`"}
									</td></tr></table>
								{/if}
							</a>
						{else}
							<a accesskey="{$key|truncate:1:""}" class="{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} selected{/if}" href="{$menu.index_url}">{tr}{$menu.menu_title}{/tr}</a>
							{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}
								{include file="`$menu.menu_template`"}
							{/if}
						{/if}
					</li>
				{/if}
			{/foreach}

			{if $gBitUser->isAdmin()}
				<li class="m-admin{if $smarty.const.ACTIVE_PACKAGE eq 'kernel'} current{/if}">
					<a accesskey="A" class="{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq 'kernel'} selected{/if}" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}Administration{/tr}</a>
					{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}
						<ul>
							{foreach key=key item=menu from=$adminMenu}
								<li>
									<a class="head" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}{$key|capitalize}{/tr}</a>
									{include file=`$menu.tpl`}
								</li>
							{/foreach}
						</ul>
					{/if}
				</li>
			{/if}

			{if $use_custom_top_bar and ( !$gBitSystem->getConfig('nexus_top_bar') or $gBitSystem->getConfig('nexus_top_bar') eq 'right' )}
				{include file="`$smarty.const.TEMP_PKG_PATH`nexus/modules/top_bar_inc.tpl"}
			{/if}
		{/if}
	</ul>
	<div class="clear"></div>
</div>
{/strip}
