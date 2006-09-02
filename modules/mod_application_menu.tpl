{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_application_menu.tpl,v 1.11 2006/09/02 04:31:12 wolff_borg Exp $ *}
{strip}

{bitmodule title="$moduleTitle" name="application_menu"}

<div class="menu">
	<ul>
		<li><a class="item" href="{$smarty.const.BIT_ROOT_URL}">{$gBitSystem->getConfig('site_title')} {tr}Home{/tr}</a></li>
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
							<a class="head" href="javascript:flipIcon('{$key}menu');">{biticon ipackage=liberty iname="collapsed" id="`$key`menuimg" iexplain="folder"}&nbsp;
						{else}
							<a class="head" href="javascript:flipWithSign('{$key}menu');"><span id="flipper{$key}menu">&nbsp;</span>
						{/if}
						{tr}{$menu.title}{/tr}</a>
					{/if}
					<div id="{$key}menu">
						{include file=$menu.template}
					</div>
					{if $menu.title}
						<script type="text/javascript">
						{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
							setFlipIcon('{$key}menu');
						{else}
							setFlipWithSign('{$key}menu');
						{/if}
						</script>
					{/if}
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
					<a class="head" href="javascript:flipIcon('usrmenu');">{biticon ipackage=liberty iname="collapsed" id="usrmenuimg" iexplain="folder"}&nbsp;
				{else}
					<a class="head" href="javascript:flipWithSign('usrmenu');"><span id="flipperusrmenu">&nbsp;</span>
				{/if}
				{tr}User Menu{/tr}</a>
					{* Show user menu contents only if there is something to display *}
					{if count($usr_user_menus) gt 0}
						<div id="usrmenu">
						<ul>
							{section name=ix loop=$usr_user_menus}
								<li><a class="item" {if $usr_user_menus[ix].mode eq 'n'}onkeypress="popUpWin(this.href,'fullScreen',0,0);" onclick="popUpWin(this.href,'fullScreen',0,0);return false;"{/if} href="{$usr_user_menus[ix].url}">{$usr_user_menus[ix].name}</a></li>
							{/section}
						</ul>
						</div>
					{/if}
				<script type="text/javascript">
					{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
						setFlipIcon('usrmenu');
					{else}
						setFlipWithSign('usrmenu');
					{/if}
				</script>
			{/if}
		</li>
	{/if}
</ul>

{/bitmodule}

{/strip}
