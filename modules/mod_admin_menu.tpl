{strip}
{bitmodule title="$moduleTitle" name="admin_menu"}
	{foreach key=key item=menu from=$adminMenu}
		<div class="menu {$key}menu">
			{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
				<a class="head" href="javascript:flipIcon('{$key}admenu');">{biticon ipackage=liberty iname="collapsed" id="`$key`admenuimg" iexplain="folder"}&nbsp;
			{else}
				<a class="head" href="javascript:flipWithSign('{$key}admenu');"><span id="flipper{$key}admenu">&nbsp;</span>
			{/if}
			{tr}{$key|capitalize}{/tr}</a>
			<div id="{$key}admenu">
				{include file=`$menu.tpl`}
			</div>
			<script type="text/javascript">
				{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
					setFlipIcon('{$key}admenu');
				{else}
					setFlipWithSign('{$key}admenu');
				{/if}
			</script>
		</div>
	{/foreach}
	<div class="admenu {$key}menu">
		{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
			<a class="menuhead" href="javascript:flipIcon('layoutadmenu');">{biticon ipackage=liberty iname="collapsed" id="layoutadmenuimg" iexplain="folder"}
		{else}
			<a class="menuhead" href="javascript:flipWithSign('layoutadmenu');"><span id="flipperlayoutadmenu">&nbsp;</span>
		{/if}
		{tr}Layout and Design{/tr}</a>
		<div id="layoutadmenu">
			{include file="bitpackage:kernel/menu_layout_admin.tpl"}
		</div>
		<script type="text/javascript">
			{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
				setFlipIcon('layoutadmenu');
			{else}
				setFlipWithSign('layoutadmenu');
			{/if}
		</script>
	</div>
{/bitmodule}
{/strip}
