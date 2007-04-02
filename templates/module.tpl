{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/Attic/module.tpl,v 1.11 2007/04/02 18:55:00 squareing Exp $ *}
{strip}
<div class="module box {$modInfo.name}">
	{if $moduleParams.title}
		<h3>
			{if $gBitSystem->isFeatureActive( 'themes_module_controls' )}
				<div class="control">
					<a title="{tr}Move module up{/tr}" href="{$smarty.const.KERNEL_PKG_URL}module_controls_inc.php?fMove=up&fPackage={$module_layout}&fModule={$module_id}">
						{biticon ipackage=liberty iname="move_up" iexplain="up"}</a>
					<a title="{tr}Move module down{/tr}" href="{$smarty.const.KERNEL_PKG_URL}module_controls_inc.php?fMove=down&fPackage={$module_layout}&fModule={$module_id}">
						{biticon ipackage=liberty iname="move_down" iexplain="down"}</a>
					<a title="{tr}Move module to opposite side{/tr}" href="{$smarty.const.KERNEL_PKG_URL}module_controls_inc.php?fMove={$colkey}&fPackage={$module_layout}&fModule={$module_id}">
						{biticon ipackage=liberty iname="move_left_right" iexplain="move left right"}</a>
					<a title="{tr}Unassign this module{/tr}" href="{$smarty.const.KERNEL_PKG_URL}module_controls_inc.php?fMove=unassign&fPackage={$module_layout}&fModule={$module_id}" onclick="return confirm('{tr}Are you sure you want to unassign this module?{/tr}')">
						{biticon ipackage="icons" iname="edit-delete" iexplain="remove"}</a>
				</div>
			{/if}
			{if $gBitSystem->isFeatureActive( 'themes_collapsible_modules' )}<a href="javascript:toggle('{$modInfo.name}');">{/if}
				{tr}{$moduleParams.title}{/tr}
			{if $gBitSystem->isFeatureActive( 'themes_collapsible_modules' )}</a>{/if}
		</h3>
	{/if}
	<div class="boxcontent" id="{$modInfo.module_name}"{if $gBitSystem->isFeatureActive( 'themes_collapsible_modules' )} style="display:{$modInfo.toggle_state};"{/if}>
		{$modInfo.data}
	</div>
</div>
{/strip}
