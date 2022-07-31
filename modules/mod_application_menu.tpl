{* $Header$ *}
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
	{foreach key=key item=menu from=$gBitSystem->mAppMenu}
		{if $menu.menu_template}
			<li>
				{if $gBitSystem->isFeatureActive( 'feature_cssmenus' )}
					{if $menu.menu_title}
						<a class="head" href="{$menu.index_url}">{tr}{$menu.menu_title|escape}{/tr}</a>
					{/if}
					{include file=$menu.menu_template}
				{else}
					{if $menu.menu_title}
						{if $gBitSystem->isFeatureActive( 'site_menu_flip_icon' )}
							<a class="head" href="javascript:BitBase.flipIcon('{$key}menu');">{booticon iname="fa-circle-plus" id="`$key`menuimg" iexplain="folder"}&nbsp;
						{else}
							<a class="head" href="javascript:BitBase.flipWithSign('{$key}menu',1);"><span style="font-family:monospace;" id="flipper{$key}menu">&nbsp;</span>
						{/if}
						&nbsp;&nbsp;{tr}{$menu.menu_title|escape}{/tr}</a>
					{/if}

					<div id="{$key}menu">
						{include file=$menu.menu_template}
					</div>

					{if $menu.menu_title}
						<script type="text/javascript">
						{if $gBitSystem->isFeatureActive( 'site_menu_flip_icon' )}
							BitBase.setFlipIcon('{$key}menu');
						{else}
							BitBase.setFlipWithSign('{$key}menu');
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
				{if $menu.title|escape}
					<a class="head" href="{$smarty.const.USERS_PKG_URL}menu.php">{tr}User Menu{/tr}</a>
				{/if}
				{if count($usr_user_menus) gt 0}
					<ul>
						{section name=ix loop=$usr_user_menus}
							<li><a class="item" {if $usr_user_menus[ix].mode eq 'n'}onkeypress="javascript:BitBase.popUpWin(this.href,'fullScreen',0,0);" onclick="javascript:BitBase.popUpWin(this.href,'fullScreen',0,0);return false;"{/if} href="{$usr_user_menus[ix].url}">{$usr_user_menus[ix].name}</a></li>
						{/section}
					</ul>
				{/if}
			{else}
				{if $gBitSystem->isFeatureActive( 'site_menu_flip_icon' )}
					<a class="head" href="javascript:BitBase.flipIcon('usrmenu');">{booticon iname="fa-circle-plus" id="usrmenuimg" iexplain="folder"}&nbsp;
				{else}
					<a class="head" href="javascript:BitBase.flipWithSign('usrmenu',1);"><span style="font-family:monospace;" id="flipperusrmenu">&nbsp;</span>
				{/if}
				{tr}User Menu{/tr}</a>
					{* Show user menu contents only if there is something to display *}
					{if count($usr_user_menus) gt 0}
						<div id="usrmenu">
						<ul>
							{section name=ix loop=$usr_user_menus}
								<li><a class="item" {if $usr_user_menus[ix].mode eq 'n'}onkeypress="javascript:BitBase.popUpWin(this.href,'fullScreen',0,0);" onclick="javascript:BitBase.popUpWin(this.href,'fullScreen',0,0);return false;"{/if} href="{$usr_user_menus[ix].url}">{$usr_user_menus[ix].name}</a></li>
							{/section}
						</ul>
						</div>
					{/if}
				<script type="text/javascript">
					{if $gBitSystem->isFeatureActive( 'site_menu_flip_icon' )}
						BitBase.setFlipIcon('usrmenu');
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
