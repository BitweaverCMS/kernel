{strip}
<div class="container" id="bittopbar">
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="nav-collapse collapse clear width100p">
				<ul class="nav dropmenu">
					{foreach key=key item=menu from=$gBitSystem->mAppMenu}
						{if $menu.menu_title && $menu.index_url && $menu.menu_template && !$menu.is_disabled}
							<li class="nav m-{$key}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} active{/if}">
								<a accesskey="{$key|truncate:1:""}" class="{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} selected{/if}" href="{$menu.index_url}">{tr}{$menu.menu_title}{/tr}</a>
								{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}
									{include file="`$menu.menu_template`"}
								{/if}
							</li>
						{/if}
					{/foreach}
				</ul>
			</div>
		</div>
	</div>
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
{/strip}
