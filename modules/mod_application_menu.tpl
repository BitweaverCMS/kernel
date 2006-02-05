{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_application_menu.tpl,v 1.7 2006/02/05 21:30:55 squareing Exp $ *}
{strip}

{bitmodule title="$moduleTitle" name="application_menu"}

<div class="menu">
	<ul>
		<li><a class="item" href="{$smarty.const.BIT_ROOT_URL}">{$gBitSystemPrefs.site_title} {tr}Home{/tr}</a></li>
		{if $gBitUser->isAdmin()}
			<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php">{tr}Administration{/tr}</a></li>
		{/if}
	</ul>
</div>

<ul id="nav2" class="{if $gBitSystem->isFeatureActive( 'feature_usecss' )}ver {/if}menu {$key}menu">
	{foreach key=key item=menu from=$appMenu}
		{if $menu.template}
			<li>
				{if $gBitSystem->isFeatureActive( 'feature_cssmenus' )}
					{if $menu.title}
						<a class="head" href="{$menu.titleUrl}">{tr}{$menu.title}{/tr}</a>
					{/if}
					{include file=$menu.template}
				{else}
					{if $menu.title}
						{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
							<a class="head" href="javascript:icntoggle('{$key}menu');">{biticon ipackage=liberty iname="collapsed" id="`$key`menuimg" iexplain="folder"}
						{else}
							<a class="head" href="javascript:toggle('{$key}menu');">
						{/if}
						{tr}{$menu.title}{/tr}</a>
						{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
							<script type="text/javascript">
								setfoldericonstate('{$key}menu');
							</script>
						{/if}
					{/if}
					<div id="{$key}menu">
						{include file=$menu.template}
					</div>
					<script type="text/javascript">
						$({$key}menu).style.display = '{$menu.display}';
					</script>
				{/if}
			</li>
		{/if}
	{/foreach}

{* =========================== User menu =========================== *}
	{if $gBitSystem->isFeatureActive( 'feature_usermenu' )and $usr_user_menus}
		<li>
			{if $gBitSystem->isFeatureActive( 'feature_cssmenus' )}
				{if $menu.title}
					<a class="head" href="{$smarty.const.USERS_PKG_URL}menu.php">{tr}User Menu{/tr}</a>
				{/if}
				{if count($usr_user_menus) gt 0}
					<ul>
						{section name=ix loop=$usr_user_menus}
							<li><a class="item" {if $usr_user_menus[ix].mode eq 'n'}onkeypress="popUpWin(this.href,'fullScreen',0,0);" onclick="popUpWin(this.href,'fullScreen',0,0);return false;"{/if} href="{$usr_user_menus[ix].url}">{$usr_user_menus[ix].name}</a></li>
						{/section}
					</ul>
				{/if}
			{else}
				{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
					<a class="head" href="javascript:icntoggle('usrmenu');">{biticon ipackage=liberty iname="collapsed" id="usrmenu" iexplain="folder"}
				{else}
					<a class="head" href="javascript:toggle('usrmenu');">
				{/if}
				{tr}User Menu{/tr}</a>
				<div id="usrmenu">
					{* Show user menu contents only if there is something to display *}
					{if count($usr_user_menus) gt 0}
						<ul>
							{section name=ix loop=$usr_user_menus}
								<li><a class="item" {if $usr_user_menus[ix].mode eq 'n'}onkeypress="popUpWin(this.href,'fullScreen',0,0);" onclick="popUpWin(this.href,'fullScreen',0,0);return false;"{/if} href="{$usr_user_menus[ix].url}">{$usr_user_menus[ix].name}</a></li>
							{/section}
						</ul>
					{/if}
				</div>
				<script type="text/javascript">
					$(usrmenu).style.display = '{$usrmenu.display}';
				</script>
			{/if}
		</li>
	{/if}
</ul>

{/bitmodule}

{/strip}
