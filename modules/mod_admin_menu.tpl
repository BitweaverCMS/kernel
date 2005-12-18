{strip}
{bitmodule title="$moduleTitle" name="admin_menu"}
	{foreach key=key item=menu from=$adminMenu}
		<div class="admenu {$key}menu">
			{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
				<a class="menuhead" href="javascript:icntoggle('{$key}admenu');">{biticon ipackage=liberty iname="collapsed" id="`$key`menuimg" iexplain="folder"}
			{else}
				<a class="menuhead" href="javascript:toggle('{$key}admenu');">
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
	<div class="admenu {$key}menu">
		{if $gBitSystem->isFeatureActive( 'feature_menusfolderstyle' )}
			<a class="menuhead" href="javascript:icntoggle('layoutadmenu');">{biticon ipackage=liberty iname="collapsed" id="`$key`menuimg" iexplain="folder"}
		{else}
			<a class="menuhead" href="javascript:toggle('layoutadmenu');">
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
{/strip}
