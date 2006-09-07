{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_package_menu.tpl,v 1.10 2006/09/07 16:45:40 squareing Exp $ *}
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
				{if $gBitSystem->isFeatureActive( 'site_menu_flip_icon' )}
					<a class="head" href="javascript:flipIcon('{$key}admenu');">{biticon ipackage="icons" iname="list-add" id="`$key`admenuimg" iexplain="folder"}&nbsp;
				{else}
					<a class="head" href="javascript:flipWithSign('{$key}admenu');"><span style="font-family:monospace;" id="flipper{$key}admenu">&nbsp;</span>
				{/if}
				&nbsp;&nbsp;{tr}{$key|capitalize}{/tr}</a>

				<div id="{$key}admenu">
					{include file=`$menu.tpl`}
				</div>

				<script type="text/javascript">
					{if $gBitSystem->isFeatureActive( 'site_menu_flip_icon' )}
						setFlipIcon('{$key}admenu');
					{else}
						setFlipWithSign('{$key}admenu');
					{/if}
				</script>
			</div>
		{/foreach}
	{/bitmodule}
{/if}
{/strip}
