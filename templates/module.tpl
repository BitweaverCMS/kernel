{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/Attic/module.tpl,v 1.6 2006/07/04 20:44:56 windblown Exp $ *}
{strip}
<div class="module box {$module_name|replace:"_":"-"}">
	{if $module_title}
		<div class="boxtitle">
		<!-- nohighlight -->
		{if $gBitSystem->isFeatureActive( 'themes_module_controls' )}
				<div class="control">
					<a title="{tr}Move module up{/tr}" href="{$smarty.const.KERNEL_PKG_URL}module_controls_inc.php?fMove=up&fPackage={$module_layout}&fModule={$module_id}">
						{biticon ipackage=liberty iname="move_up" iexplain="up"}</a>
					<a title="{tr}Move module down{/tr}" href="{$smarty.const.KERNEL_PKG_URL}module_controls_inc.php?fMove=down&fPackage={$module_layout}&fModule={$module_id}">
						{biticon ipackage=liberty iname="move_down" iexplain="down"}</a>
					<a title="{tr}Move module to opposite side{/tr}" href="{$smarty.const.KERNEL_PKG_URL}module_controls_inc.php?fMove={$colkey}&fPackage={$module_layout}&fModule={$module_id}">
						{biticon ipackage=liberty iname="move_left_right" iexplain="move left right"}</a>
					<a title="{tr}Unassign this module{/tr}" href="{$smarty.const.KERNEL_PKG_URL}module_controls_inc.php?fMove=unassign&fPackage={$module_layout}&fModule={$module_id}" onclick="return confirm('{tr}Are you sure you want to unassign this module?{/tr}')">
						{biticon ipackage=liberty iname="delete_small" iexplain="remove"}</a>
				</div>
			{/if}
			{if $gBitSystem->isFeatureActive( 'themes_collapsible_modules' )}<a href="javascript:toggle('{$module_name}');">{/if}
				{tr}{$module_title}{/tr}
			{if $gBitSystem->isFeatureActive( 'themes_collapsible_modules' )}</a>{/if}
		<!-- /nohighlight -->
		</div>
	{/if}
	<div class="boxcontent" id="{$module_name}"{if $gBitSystem->isFeatureActive( 'themes_collapsible_modules' )} style="display:{$toggle_state};"{/if}>
	    <!-- nohighlight -->
		{$module_content}
		<!-- /nohighlight -->
	</div>
</div>
{/strip}
