{strip}
<nav class="navbar navbar-default {if $gBitSystem->getConfig('layout-header')}navbar-static-top{/if}" role="navigation" id="bittopbar">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bit-top-menu">{booticon iname="icon-reorder"}</button>
			<a class="navbar-brand" href="{$smarty.const.BIT_ROOT_URL}" {if $gBitSystem->getConfig('site_slogan')} title="{$gBitSystem->getConfig('site_slogan')|escape}" {/if}>{$gBitSystem->getConfig('site_title')}</a>
		</div>
		{if $gBitSystem->mAppMenu}
		<div class="collapse navbar-collapse" id="bit-top-menu">
			<ul class="nav navbar-nav">
				{foreach key=key item=menu from=$gBitSystem->mAppMenu}
					{if $menu.menu_title && $menu.index_url && $menu.menu_template && !$menu.is_disabled}
						{if $menu.menu_type == 'form'}
						{else}
							{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}
							<li class="dropdown m-{$key}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} active{/if}">
								{include file="`$menu.menu_template`" packageMenuClass="dropdown-menu" packageMenuTitle=$menu.menu_title}
							</li>
							{else}
							<li class="m-{$key}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} active{/if}">
								<a accesskey="{$key|truncate:1:""}" class="{if $gBitSystem->isFeatureActive( 'site_top_bar_dropdown' )}head{else}item{/if}{if $smarty.const.ACTIVE_PACKAGE eq $menu.package_name} selected{/if}" href="{$menu.index_url}">{tr}{$menu.menu_title}{/tr}</a>
							</li>
							{/if}
						{/if}
					{/if}
				{/foreach}
			</ul>

			<ul class="nav navbar-nav navbar-right">
				{if $gBitUser->isRegistered()}
					<li class="dropdown">
						<a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#"><i class="icon-user"></i> {displayname hash=$gBitUser->mInfo nolink=1} <b class="caret"></b></a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<li><a href="{$gBitUser->getDisplayUrl()}">{tr}My Profile{/tr}</a></li>
							<li><a href="{$smarty.const.USERS_PKG_URL}my.php">{tr}My Account{/tr}</a></li>
							<li><a href="{$smarty.const.USERS_PKG_URL}logout.php">{tr}Logout{/tr}</a></li>
							{if $adminMenu}
								<li class="dropdown-submenu menu-admin">{include file="bitpackage:kernel/menu_top_admin_inc.tpl"}</li>
							{/if}
						</ul>
					</li>
				{else}
					<li><a href="{$smarty.const.USERS_PKG_URL}signin.php">{tr}Sign In{/tr}</a></li>
				{/if}
			</ul>
		</div>
		{/if}
	</div>
</nav>

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
