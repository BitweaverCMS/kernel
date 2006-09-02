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
{/bitmodule}
{/strip}
