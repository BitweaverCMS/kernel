{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_package_menu.tpl,v 1.1.1.1.2.1 2005/06/26 07:59:36 squareing Exp $ *}
{strip}

{if $packageMenu}
	{bitmodule title="$moduleTitle" name="package_menu"}
		<div class="menu">
			{include file=$packageMenu.template}
		</div>
	{/bitmodule}
{elseif $gBitLoc.ACTIVE_PACKAGE and $gBitUser->isAdmin()}
	{bitmodule title="$moduleTitle" name="package_menu"}
		{foreach key=key item=menu from=$adminMenu}
			<div class="menu {$key}menu">
				{if $gBitSystemPrefs.feature_menusfolderstyle eq 'y'}
					<a class="head" href="javascript:icntoggle('{$key}admenu');">{biticon ipackage=liberty iname="collapsed" id="`$key`admenuimg" iexplain="folder"}&nbsp;
				{else}
					<a class="head" href="javascript:toggle('{$key}admenu');">
				{/if}
				{tr}{$key|capitalize}{/tr}</a>
				{if $gBitSystemPrefs.feature_menusfolderstyle eq 'y'}
					<script type="text/javascript">
						setfoldericonstate('{$key}admenu');
					</script>
				{/if}
				<div id="{$key}admenu" style="{$menu.style}">
					{include file=`$menu.tpl`}
				</div>
			</div>
		{/foreach}
		<div class="menu layoutmenu">
			{if $gBitSystemPrefs.feature_menusfolderstyle eq 'y'}
				<a class="head" href="javascript:icntoggle('layoutadmenu');">{biticon ipackage=liberty iname="collapsed" id="layoutadmenuimg" iexplain="folder"}
			{else}
				<a class="head" href="javascript:toggle('layoutadmenu');">
			{/if}
			&nbsp;{tr}Layout and Design{/tr}</a>
			{if $gBitSystemPrefs.feature_menusfolderstyle eq 'y'}
				<script type="text/javascript">
					setfoldericonstate('layoutadmenu');
				</script>
			{/if}
			<div id="layoutadmenu" style="{$layoutstyle}">
				{include file="bitpackage:kernel/menu_layout_admin.tpl"}
			</div>
		</div>
	{/bitmodule}
{/if}

{/strip}
