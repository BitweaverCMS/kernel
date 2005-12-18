{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_package_menu.tpl,v 1.1.1.1.2.4 2005/12/18 19:58:26 squareing Exp $ *}
{strip}
{if $packageMenu}
	{bitmodule title="$moduleTitle" name="package_menu"}
		<div class="menu">
			{include file=$packageMenu.template}
		</div>
	{/bitmodule}
{elseif $smarty.const.ACTIVE_PACKAGE and $gBitUser->isAdmin()}
	{bitmodule title="$moduleTitle" name="package_menu"}
		{foreach key=key item=menu from=$adminMenu}
			<div class="menu {$key}menu">
				{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
					<a class="head" href="javascript:icntoggle('{$key}admenu');">{biticon ipackage=liberty iname="collapsed" id="`$key`admenuimg" iexplain="folder"}&nbsp;
				{else}
					<a class="head" href="javascript:toggle('{$key}admenu');">
				{/if}
				{tr}{$key|capitalize}{/tr}</a>
				{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
					<script type="text/javascript">
						setfoldericonstate('{$key}admenu');
					</script>
				{/if}
				<div id="{$key}admenu">
					{include file=`$menu.tpl`}
				</div>
				<script type="text/javascript">
					$({$key}admenu).style.display = '{$menu.display}';
				</script>
			</div>
		{/foreach}
		<div class="menu layoutmenu">
			{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
				<a class="head" href="javascript:icntoggle('layoutadmenu');">{biticon ipackage=liberty iname="collapsed" id="layoutadmenuimg" iexplain="folder"}&nbsp;
			{else}
				<a class="head" href="javascript:toggle('layoutadmenu');">
			{/if}
			{tr}Layout and Design{/tr}</a>
			{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
				<script type="text/javascript">
					setfoldericonstate('layoutadmenu');
				</script>
			{/if}
			<div id="layoutadmenu">
				{include file="bitpackage:kernel/menu_layout_admin.tpl"}
			</div>
			<script type="text/javascript">
				$(layoutadmenu).style.display = '{$layoutdisplay}';
			</script>
		</div>
	{/bitmodule}
{/if}
{/strip}
