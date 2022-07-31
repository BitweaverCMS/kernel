{strip}
{bitmodule title="$moduleTitle" name="admin_menu"}
	{foreach key=key item=menu from=$adminMenu}
		<div class="menu {$key}menu">
			{if $gBitSystem->isFeatureActive( 'site_menu_flip_icon' )}
				<a class="head" href="javascript:BitBase.flipIcon('{$key}admenu');">{booticon iname="fa-circle-plus" id="`$key`admenuimg" iexplain="folder"}&nbsp;
			{else}
				<a class="head" href="javascript:BitBase.flipWithSign('{$key}admenu',1);"><span style="font-family:monospace;" id="flipper{$key}admenu">&nbsp;</span>
			{/if}
			&nbsp;&nbsp;{tr}{$key|capitalize}{/tr}</a>

			<div id="{$key}admenu">
				{include file=$menu.tpl}
			</div>

			<script type="text/javascript">
				{if $gBitSystem->isFeatureActive( 'site_menu_flip_icon' )}
					BitBase.setFlipIcon('{$key}admenu');
				{else}
					BitBase.setFlipWithSign('{$key}admenu');
				{/if}
			</script>
		</div>
	{/foreach}
{/bitmodule}
{/strip}
