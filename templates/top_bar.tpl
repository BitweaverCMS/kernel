{strip}
<div id="bittopbar">
	<ul id="nav" class="menu hor">
		<li class="m-home">
			<a class="head" accesskey="h" href="{$smarty.const.BIT_ROOT_URL}">{$gBitSystem->getConfig('site_menu_title')|default:$gBitSystem->getConfig('site_title')}</a>
			{include file="bitpackage:kernel/menu_global.tpl"}
		</li>

		{foreach key=key item=menu from=$gBitSystem->mAppMenu}
			{if $menu.menu_title && $menu.index_url && $menu.menu_template && !$menu.is_disabled}
				<li class="m-{$key}{if $smarty.const.ACTIVE_PACKAGE eq $menu.active_package} current{/if}">
					<a accesskey="{$key|truncate:1:""}" class="{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} selected{/if}" href="{$menu.index_url}">{tr}{$menu.menu_title}{/tr}</a>
					{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}
						{include file="`$menu.menu_template`"}
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
	</ul>
	<div class="clear"></div>
</div>

{if $gBitSystem->isFeatureActive('site_top_bar_js') && $gBitSystem->isFeatureActive('site_top_bar_dropdown')}
	<script type="text/javascript"> /*<![CDATA[*/
		var listMenu = new FSMenu('listMenu', true, 'left', 'auto', '-999');
		{if $gBitSystem->isFeatureActive( 'site_top_bar_js_fade' )}
			listMenu.animations[listMenu.animations.length] = FSMenu.animFade;
		{/if}
		{if $gBitSystem->isFeatureActive( 'site_top_bar_js_swipe' )}
			listMenu.animations[listMenu.animations.length] = FSMenu.animSwipeDown;
		{/if}
		{if $gBitSystem->isFeatureActive( 'site_top_bar_js_clip' )}
			listMenu.animations[listMenu.animations.length] = FSMenu.animClipDown;
		{/if}
		addEvent(window, 'load', new Function('listMenu.activateMenu("nav")'));
	/*]]>*/ </script>
{/if}

{if $gBitSystem->isFeatureActive( 'site_top_column' ) && $t_modules && !$gHideModules}
	{section name=homeix loop=$t_modules}
		{$t_modules[homeix].data}
	{/section}
{/if}
{/strip}
