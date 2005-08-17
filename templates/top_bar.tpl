{strip}
<a class="skip" style="position:absolute;top:0;left:-999em;" href="#content">{tr}Skip Navigation{/tr}</a>
<div id="bittopbar">
	<ul id="nav" class="menu hor">
		{if $use_custom_top_bar and $gBitSystemPrefs.top_bar_position eq 'replace'}
			{include file="`$smarty.const.TEMP_PKG_PATH`nexus/modules/top_bar_inc.tpl"}
		{else}
			{if $use_custom_top_bar and $gBitSystemPrefs.top_bar_position eq 'left'}
				{include file="`$smarty.const.TEMP_PKG_PATH`nexus/modules/top_bar_inc.tpl"}
			{/if}

			<li class="m-home">
				<a class="head" accesskey="h" href="{$smarty.const.BIT_ROOT_URL}">{$gBitSystemPrefs.site_menu_title|default:$siteTitle}</a>
				{include file="bitpackage:kernel/menu_global.tpl"}
			</li>

			{foreach key=key item=menu from=$appMenu}
				{if $menu.title && $menu.titleUrl && $menu.template}
					<li class="m-{$key}{if $smarty.const.ACTIVE_PACKAGE eq $menu.adminPanel} current{/if}">
						<a accesskey="{$key|regex_replace:"/(.).*/":"\$1"}" class="{if $gBitSystem->isFeatureActive( 'feature_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq $menu.adminPanel} selected{/if}" href="{$menu.titleUrl}">{tr}{$menu.title}{/tr}</a>
						{if $gBitSystem->isFeatureActive( 'feature_top_bar_dropdown' )}
							{include file="`$menu.template`"}
						{/if}
					</li>
				{/if}
			{/foreach}

			{if $gBitUser->isAdmin()}
				<li class="m-admin{if $smarty.const.ACTIVE_PACKAGE eq 'kernel'} current{/if}">
					<a accesskey="A" class="{if $gBitSystem->isFeatureActive( 'feature_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq 'kernel'} selected{/if}" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}Administration{/tr}</a>
					{if $gBitSystem->isFeatureActive( 'feature_top_bar_dropdown' )}
						<ul>
							{foreach key=key item=menu from=$adminMenu}
								<li>
									<a class="head" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}{$key|capitalize}{/tr}</a>
									{include file=`$menu.tpl`}
								</li>
							{/foreach}
							<li>
								<a class="head" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}Layout and Design{/tr}</a>
								{include file="bitpackage:kernel/menu_layout_admin.tpl"}
							</li>
						</ul>
					{/if}
				</li>
			{/if}

			{if $use_custom_top_bar and ( !$gBitSystemPrefs.top_bar_position or $gBitSystemPrefs.top_bar_position eq 'right' )}
				{include file="`$smarty.const.TEMP_PKG_PATH`nexus/modules/top_bar_inc.tpl"}
			{/if}
		{/if}
	</ul>
	<div class="clear"></div>
</div>
{/strip}
