{* $Header: /cvsroot/bitweaver/_bit_kernel/modules/mod_package_menu.tpl,v 1.6 2006/02/06 15:22:35 spiderr Exp $ *}
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
	{/bitmodule}
{/if}
{/strip}
