{strip}
{bitmodule title="$moduleTitle" name="admin_menu"}
	{foreach key=key item=menu from=$adminMenu}
		<div class="admenu {$key}menu">
			{if $gBitSystemPrefs.feature_menusfolderstyle eq 'y'}
				<a class="menuhead" href="javascript:icntoggle('{$key}admenu');">{biticon ipackage=liberty iname="collapsed" id="`$key`menuimg" iexplain="folder"}
			{else}
				<a class="menuhead" href="javascript:toggle('{$key}admenu');">
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
	<div class="admenu {$key}menu">
		{if $gBitSystemPrefs.feature_menusfolderstyle eq 'y'}
			<a class="menuhead" href="javascript:icntoggle('layoutadmenu');">{biticon ipackage=liberty iname="collapsed" id="`$key`menuimg" iexplain="folder"}
		{else}
			<a class="menuhead" href="javascript:toggle('layoutadmenu');">
		{/if}
		{tr}Layout and Design{/tr}</a>
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
{/strip}
